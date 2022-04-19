<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

use CdlExportPlugin\Repository\PublishedArticle;
use CdlExportPlugin\Repository\AuthorSubmission;

class Article extends AbstractDataObjectMapper {
    protected static $contexts = ['index' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'title']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'section', 'source' => 'sectionTitle'],
        ['property' => 'title', 'source' => 'articleTitle'],
        ['property' => 'authors'],
        ['property' => 'language'],
        ['property' => 'dateStarted', 'source' => 'dateSubmitted', 'filters' => ['datetime']],
        ['property' => 'dateSubmitted', 'filters' => ['datetime']],
        ['property' => 'dateUpdated', 'source' => 'lastModified', 'filters' => ['datetime']],
        ['property' => 'datePublished', 'source' => 'publishedArticle.datePublished', 'onError' => null, 'filters' => ['datetime']],
        ['property' => 'dateAccepted', 'onError' => null, 'filters' => ['datetime']], // TODO NOT IMPLEMENTED
        ['property' => 'dateDeclined', 'onError' => null, 'filters' => ['datetime']], // TODO NOT IMPLEMENTED
        ['property' => 'doi', 'source' => 'storedDOI'],
        ['property' => 'pages'],
        ['property' => 'mostRecentEditorDecision'],
        ['property' => 'status', 'source' => 'publicationStatus'],
        ['property' => 'issueSourceRecordKeys'],
        ['property' => 'sectionSourceRecordKey']
    ];

    /**
     * @param $data
     * @param $dataObject
     * @return mixed
     */
    protected static function preMap($dataObject, $context) {
        // Add the publishedArticle onto the article -- it has some useful info not on the article
        $dataObject->authorSubmission = (new AuthorSubmission)->fetchByArticle($dataObject);
        $dataObject->publishedArticle = (new PublishedArticle)->fetchByArticle($dataObject);

        $dataObject->mostRecentEditorDecision = self::getMostRecentEditorDecision($dataObject);

        $dataObject->publicationStatus = self::mapJournalStatus($dataObject->getStatus());

        // TODO: we are generating a reference to another source record key here; we'll likely need another way to do
        // this
        $dataObject->issueSourceRecordKeys = is_null($dataObject->publishedArticle) ?
            [] : [\Issue::class.':'.$dataObject->publishedArticle->getIssueId()];
        $dataObject->sectionSourceRecordKey = is_null($dataObject->publishedArticle) ?
            [] : [\Section::class.':'.$dataObject->publishedArticle->getSectionId()];

        return $dataObject;
    }

    /**
     * TODO: Might want to move this out of the Mapper class into Repository
     * @param $dataObject
     */
    protected static function getMostRecentEditorDecision($dataObject) {
        $editorDecisions = (new AuthorSubmission)->fetchEditorDecisionsByArticle($dataObject);

        if(count($editorDecisions) == 0) return [];

        usort($editorDecisions, function($a, $b) {
            $dateA = strtotime($a['dateDecided']);
            $dateB = strtotime($b['dateDecided']);

            return $dateB > $dateA;
        });

        $editorDecision = (object) array_pop($editorDecisions);
        $editorDecision->__mapperClass = 'EditorDecision';
        return $editorDecision;
    }

    /**
     * @param $status
     */
    protected static function mapJournalStatus($status) {
        // TODO: this mapping probably isn't quite right; also this mapping should probably go somewhere else
        // TODO: also this is a weird construction I've made here
        // TODO: also we're missing some stages: copyediting, typesetting, proofing, maybe others
        return @[STATUS_ARCHIVED => 'archived',
                 STATUS_QUEUED => 'submitted',
                 STATUS_PUBLISHED => 'published',
                 STATUS_DECLINED => 'rejected',
                 STATUS_QUEUED_UNASSIGNED => 'submitted',
                 STATUS_QUEUED_REVIEW => 'review',
                 STATUS_QUEUED_EDITING => 'copyediting',
                 STATUS_INCOMPLETE => 'draft']
                 [$status] ?: null;

    }
}