<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Repository\ArticleComment;
use JournalTransporterPlugin\Repository\Article;
use JournalTransporterPlugin\Repository\File;
use JournalTransporterPlugin\Repository\ReviewFormResponse;
use JournalTransporterPlugin\Repository\ArticleEventLog;
use JournalTransporterPlugin\Repository\SupplementaryFile;
use JournalTransporterPlugin\Utility\SourceRecordKey;

class ReviewAssignment extends AbstractDataObjectMapper {

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'reviewId'],
        ['property' => 'reviewer', 'source' => 'reviewerId', 'sourceRecordKey' => 'reviewer'],
        ['property' => 'editor', 'sourceRecordKey' => 'editor'],
        ['property' => 'round'],
        ['property' => 'reviewType', 'source' => 'reviewTypeText'],
        ['property' => 'reviewMethod'],
        ['property' => 'competingInterests'],
        ['property' => 'recommendation', 'source' => 'recommendationText'],
        ['property' => 'fullName', 'source' => 'reviewerFullName'],
        ['property' => 'dateRated', 'filters' => ['datetime']],
        ['property' => 'lastModified', 'filters' => ['datetime']],
        ['property' => 'dateAssigned', 'filters' => ['datetime']],
        ['property' => 'dateNotified', 'filters' => ['datetime']],
        ['property' => 'dateConfirmed', 'filters' => ['datetime']],
        ['property' => 'dateCompleted', 'filters' => ['datetime']],
        ['property' => 'dateAcknowledged', 'filters' => ['datetime']],
        ['property' => 'dateReminded', 'filters' => ['datetime']],
        ['property' => 'dateDue', 'filters' => ['datetime']],
        ['property' => 'dateResponseDue', 'filters' => ['datetime']],
        ['property' => 'declined', 'filters' => ['boolean']],
        ['property' => 'replaced', 'filters' => ['boolean']],
        ['property' => 'cancelled', 'filters' => ['boolean']],
        ['property' => 'reviewFiles', 'source' => 'reviewFileForRound', 'context' => 'sourceRecordKey'],
        ['property' => 'suppFiles', 'source' => 'supplementaryFiles'],
        ['property' => 'reviewerFile', 'context' => 'sourceRecordKey'],
        ['property' => 'comments', 'source' => 'reviewComments'],
        ['property' => 'quality', 'source' => 'qualityText'],
        ['property' => 'hasResponse', 'filter' => ['boolean']],
        ['property' => 'isComplete', 'filter' => ['boolean']],
        ['property' => 'reviewForm']
    ];

    /**
     * @param $data
     * @param $dataObject
     * @return mixed
     */
    protected static function preMap($dataObject, $context)
    {
        $dataObject->editor = self::getEditorFromLogEntry($dataObject);

        // TODO: Could improve performance by caching this article, or storing it for the next iteration
        $article = (new Article)->fetchById($dataObject->getArticleId());
        $dataObject->reviewComments = (new ArticleComment)->fetchByArticleAndReview($article, $dataObject, 'review');

        // We have to fetch the supp files ourselves because OJS doesn't return the revision number. We have to filter
        // because OJS includes review files that are not shared with reviewers.
        $dataObject->supplementaryFiles = array_filter(
            (new SupplementaryFile)->fetchByArticle($article),
            function($file) {
                return $file->getShowReviewers() == true;
            }
        );
        $dataObject->recommendationText = self::getRecommendationText($dataObject);
        $dataObject->reviewTypeText = self::getReviewTypeText($dataObject);
        $dataObject->qualityText = self::getQualityText($dataObject);
        $dataObject->hasResponse = self::getHasResponse($dataObject);
        $dataObject->isComplete = self::getIsComplete($dataObject);
        $dataObject->reviewForm = self::getReviewFormSourceRecordKey($dataObject);
        $dataObject->reviewFileForRound = self::getReviewFileForRound($dataObject);

        return $dataObject;
    }

    /**
     * @param $dataObject
     * @return mixed
     */
    protected static function getReviewFileForRound($dataObject)
    {
        $files = (new File)->fetchRevisionsByFile($dataObject->getReviewFile(), $dataObject->getRound());
        if(count($files) == 0) return null;
        return [end($files)];
    }


    /**
     * @param $dataObject
     * @return mixed
     */
    protected static function getEditorFromLogEntry($dataObject)
    {
        $logEntries = (new ArticleEventLog)
            ->getArticleLogEntriesByAssoc($dataObject->getArticleId(), 3, $dataObject->getReviewId())->toArray();
        if(count($logEntries) == 0) return null;
        usort($logEntries, function($a, $b) { return $a->getDateLogged() > $b->getDateLogged(); });
        return reset($logEntries)->getUserId();
    }

    /**
     * @param $dataObject
     * @return object
     */
    protected static function getReviewFormSourceRecordKey($dataObject)
    {
        if(is_null($dataObject->getReviewFormId())) return null;
        return (object)['source_record_key' => SourceRecordKey::reviewForm($dataObject->getReviewFormId())];
    }

    /**
     * @param $dataObject
     * @return mixed
     */
    protected static function getHasResponse($dataObject)
    {
        return count((new ReviewFormResponse)->fetchByReview($dataObject)) > 0;
    }

    protected static function getIsComplete($dataObject)
    {
        return !empty($dataObject->getDateCompleted());
    }

    /**
     * @param $reviewAssignment
     * @return string
     */
    protected static function getRecommendationText($reviewAssignment)
    {
        return @[
            SUBMISSION_REVIEWER_RECOMMENDATION_ACCEPT => 'accept',
            SUBMISSION_REVIEWER_RECOMMENDATION_PENDING_REVISIONS => 'pending_revisions',
            SUBMISSION_REVIEWER_RECOMMENDATION_RESUBMIT_HERE => 'resubmit_here',
            SUBMISSION_REVIEWER_RECOMMENDATION_RESUBMIT_ELSEWHERE => 'resubmit_elsewhere',
            SUBMISSION_REVIEWER_RECOMMENDATION_DECLINE => 'decline',
            SUBMISSION_REVIEWER_RECOMMENDATION_SEE_COMMENTS => 'see_comments'
        ][$reviewAssignment->getRecommendation()];
    }

    /**
     * @param $reviewAssignment
     * @return string
     */
    protected static function getReviewTypeText($reviewAssignment)
    {
        // See: lib/pkp/classes/submission/reviewAssignment/PKPReviewAssignment.inc.php
        // The constants aren't working here
        return @[
            1 => 'blind',
            2 => 'double_blind',
            3 => 'open'
        ][$reviewAssignment->getReviewType()];
    }

    /**
     * @param $reviewAssignment
     * @return float|int|null
     */
    protected static function getQualityText($reviewAssignment) {
        if(is_null($reviewAssignment->getQuality())) return null;
        return $reviewAssignment->getQuality() * 20;
    }
}