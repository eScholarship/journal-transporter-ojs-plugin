<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Repository\ArticleComment;
use JournalTransporterPlugin\Repository\Article;

class ReviewAssignment extends AbstractDataObjectMapper {

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'reviewId'],
        ['property' => 'reviewer'],
        ['property' => 'round'],
        ['property' => 'reviewType', 'source' => 'reviewTypeText'],
        ['property' => 'reviewMethod'],
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
        ['property' => 'reviewFile', 'context' => 'sourceRecordKey'],
        ['property' => 'suppFiles', 'context' => 'sourceRecordKey'],
        ['property' => 'reviewerFile', 'context' => 'sourceRecordKey'],
        ['property' => 'comments', 'source' => 'reviewComments'],
        ['property' => 'quality', 'source' => 'qualityText'],
    ];

    /**
     * @param $data
     * @param $dataObject
     * @return mixed
     */
    protected static function preMap($dataObject, $context) {
        // TODO: we are generating a reference to another source record key here; we'll likely need another way to do
        // this
        $dataObject->reviewer = is_null($dataObject->getReviewerId()) ?
            null : (object) ['source_record_key' => \User::class.':'.$dataObject->getReviewerId()];

        // TODO: Could improve performance by caching this article, or storing it for the next iteration
        $article = (new Article)->fetchById($dataObject->getArticleId());
        $dataObject->reviewComments = (new ArticleComment)->fetchByArticleAndReview($article, $dataObject);

        $dataObject->recommendationText = self::getRecommendationText($dataObject);
        $dataObject->reviewTypeText = self::getReviewTypeText($dataObject);
        $dataObject->qualityText = self::getQualityText($dataObject);
        
        return $dataObject;
    }

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

    protected static function getReviewTypeText($reviewAssignment)
    {
        // See: lib/pkp/classes/submission/reviewAssignment/PKPReviewAssignment.inc.php
        // The constants aren't working here
        return @[
            SUBMISSION_REVIEW_METHOD_BLIND => 'blind',
            SUBMISSION_REVIEW_METHOD_DOUBLEBLIND => 'double_blind',
            SUBMISSION_REVIEW_METHOD_OPEN => 'open'
        ][$reviewAssignment->getReviewType()];
    }

    protected static function getQualityText($reviewAssignment) {
        return @[
            SUBMISSION_REVIEWER_RATING_VERY_GOOD => 'very_good',
            SUBMISSION_REVIEWER_RATING_GOOD => 'good',
            SUBMISSION_REVIEWER_RATING_AVERAGE => 'average',
            SUBMISSION_REVIEWER_RATING_POOR => 'poor',
            SUBMISSION_REVIEWER_RATING_VERY_POOR => 'very_poor'
        ][$reviewAssignment->getQuality()];
    }
}