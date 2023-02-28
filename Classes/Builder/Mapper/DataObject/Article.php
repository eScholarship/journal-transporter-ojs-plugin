<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Repository\PublishedArticle;
use JournalTransporterPlugin\Repository\AuthorSubmission;
use JournalTransporterPlugin\Repository\EditAssignment;
use JournalTransporterPlugin\Utility\Enums\Discipline;
use JournalTransporterPlugin\Utility\SourceRecordKey;

import('classes.submission.common.Action');

class Article extends AbstractDataObjectMapper {
    protected static $contexts = ['index' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'title', 'datePublished']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'section', 'source' => 'sectionTitle'],
        ['property' => 'title', 'source' => 'articleTitle'],
        ['property' => 'abstract', 'source' => 'localizedAbstract', 'filters' => ['html']],
        ['property' => 'coverLetter', 'source' => 'commentsToEditor', 'filters' => ['html']],
        ['property' => 'acknowledgements', 'source' => 'localizedSponsor', 'filters' => ['html']],
        ['property' => 'disciplines'],
        ['property' => 'keywords'],
        ['property' => 'creator', 'source' => 'user', 'context' => 'sourceRecordKey'],
        ['property' => 'authors', 'context' => 'sourceRecordKey'],
        ['property' => 'language'],
        ['property' => 'dateStarted', 'source' => 'dateSubmitted', 'filters' => ['datetime']],
        ['property' => 'dateSubmitted', 'filters' => ['datetime']],
        ['property' => 'dateUpdated', 'source' => 'lastModified', 'filters' => ['datetime']],
        ['property' => 'datePublished', 'source' => 'publishedArticle.datePublished', 'onError' => null, 'filters' => ['datetime']],
        ['property' => 'sequence', 'onError' => null],
        ['property' => 'doi', 'source' => 'storedDOI'],
        ['property' => 'pages'],
        ['property' => 'status', 'source' => 'submissionStatus'],
        ['property' => 'issues'],
        ['property' => 'sections'],
        ['property' => 'externalIds'],
        ['property' => 'license']
    ];

    /**
     * @param $data
     * @param $dataObject
     * @return mixed
     */
    protected static function preMap($dataObject, $context)
    {
        // Do less for index context
        if($context == 'index') return $dataObject;

        $dataObject->authorSubmission = (new AuthorSubmission)->fetchByArticle($dataObject);

        $dataObject->publishedArticle = (new PublishedArticle)->fetchByArticle($dataObject);

        $dataObject->submissionStatus = self::getSubmissionStatus($dataObject);

        $dataObject->disciplines = self::mapDisciplines($dataObject);

        $dataObject->issues = is_null($dataObject->publishedArticle) ?
            [] : [(object) ['source_record_key' => SourceRecordKey::issue($dataObject->publishedArticle->getIssueId())]];
        $dataObject->sections = is_null($dataObject->publishedArticle) ?
            [(object) ['source_record_key' => SourceRecordKey::section($dataObject->getSectionId())]] :
            [(object) ['source_record_key' => SourceRecordKey::section($dataObject->publishedArticle->getSectionId())]];

        $dataObject->externalIds = self::getExternalIds($dataObject);
        $dataObject->keywords = array_map('trim', explode(';', reset($dataObject->getData('subject'))));

        $dataObject->license = reset($dataObject->getData('eschol_license_url'));

        $dataObject->sequence = self::getArticleSequenceWithinIssue($dataObject);

        return $dataObject;
    }

    /**
     * @param $dataObject
     * @return mixed
     */
    protected function getArticleSequenceWithinIssue($dataObject)
    {
        if(is_null($dataObject->publishedArticle)) return null;
        $articles = (new PublishedArticle)->fetchArticlesByIssue($dataObject->publishedArticle->getIssueId());
        $sequence = 0;
        foreach($articles as $article) {
            $sequence++;
            if($article->getId() == $dataObject->getId()) {
                return $sequence;
            }
        }
        return null;
    }

    /**
     * @param $dataObject
     * @return array
     */
    protected static function getExternalIds($dataObject)
    {
        $ids = [];
        $ids[] = (object) ['name' => 'source_id', 'value' => $dataObject->getId()];

        // This is quite specific to CDL
        if($ark = $dataObject->getLocalizedData('eschol_ark')) {
            $ids[] = (object) ['name' => 'ark', 'value' => $ark];
        }

        return $ids;
    }

    /**
     * @param $dataObject
     * @return array
     */
    protected static function mapDisciplines($dataObject)
    {
        return array_filter(
            array_map(
                function($disciplineKey) { return Discipline::getDisciplineName($disciplineKey); },
                $dataObject->getLocalizedDiscipline()
            )
        );
    }

    /**
     * @param $dataObject
     * @return mixed
     */
    protected static function getSubmissionStatus($dataObject)
    {
        $status = $dataObject->authorSubmission->getSubmissionStatus();

        if ($status == STATUS_QUEUED_REVIEW) {
          $latestDecision = $dataObject->authorSubmission->getMostRecentDecision();
          if ($latestDecision){
            if($latestDecision == SUBMISSION_EDITOR_DECISION_PENDING_REVISIONS || $latestDecision == SUBMISSION_EDITOR_DECISION_RESUBMIT){
              return 'revise';
            }
          }
        }

        if ($status == STATUS_QUEUED_EDITING) {
          $latestDecision = $dataObject->authorSubmission->getMostRecentDecision();
          if ($latestDecision){
             if($latestDecision == SUBMISSION_EDITOR_DECISION_DECLINE){
               return 'rejected';
             }
          }
        }

        return @[STATUS_ARCHIVED => 'rejected',
                 STATUS_QUEUED => 'review',
                 STATUS_PUBLISHED => 'published',
                 STATUS_DECLINED => 'rejected',
                 STATUS_QUEUED_UNASSIGNED => 'submitted',
                 STATUS_QUEUED_REVIEW => 'review',
                 STATUS_QUEUED_EDITING => 'copyediting',
                 STATUS_INCOMPLETE => 'draft']
                 [$status] ?: null;
    }
}
