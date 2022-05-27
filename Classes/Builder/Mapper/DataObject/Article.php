<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Repository\PublishedArticle;
use JournalTransporterPlugin\Repository\AuthorSubmission;
use JournalTransporterPlugin\Repository\EditAssignment;
use JournalTransporterPlugin\Utility\Enums\Discipline;
use JournalTransporterPlugin\Utility\SourceRecordKey;

class Article extends AbstractDataObjectMapper {
    protected static $contexts = ['index' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'title', 'datePublished']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'section', 'source' => 'sectionTitle'],
        ['property' => 'title', 'source' => 'articleTitle'],
        ['property' => 'abstract', 'source' => 'localizedAbstract', 'filters' => ['html']],
        ['property' => 'coverLetter', 'source' => 'commentsToEditor', 'filters' => ['html']],
        ['property' => 'disciplines'],
        ['property' => 'authors', 'context' => 'sourceRecordKey'],
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
        ['property' => 'issues'],
        ['property' => 'sections']
    ];

    /**
     * @param $data
     * @param $dataObject
     * @return mixed
     */
    protected static function preMap($dataObject, $context)
    {
        // Add the publishedArticle onto the article -- it has some useful info not on the article
        $dataObject->authorSubmission = (new AuthorSubmission)->fetchByArticle($dataObject);
        $dataObject->publishedArticle = (new PublishedArticle)->fetchByArticle($dataObject);

        $dataObject->mostRecentEditorDecision = self::getMostRecentEditorDecision($dataObject);

        $dataObject->publicationStatus = self::mapJournalStatus($dataObject->getStatus());

        $dataObject->disciplines = self::mapDisciplines($dataObject);

        $dataObject->issues = is_null($dataObject->publishedArticle) ?
            [] : [(object) ['source_record_key' => SourceRecordKey::issue($dataObject->publishedArticle->getIssueId())]];
        $dataObject->sections = is_null($dataObject->publishedArticle) ?
            [] : [(object) ['source_record_key' => SourceRecordKey::section($dataObject->publishedArticle->getSectionId())]];

        return $dataObject;
    }


    /**
     * TODO: Might want to move this out of the Mapper class into Repository
     * @param $dataObject
     */
    protected static function getMostRecentEditorDecision($dataObject)
    {
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
     * @param $status
     */
    protected static function mapJournalStatus($status)
    {
        // TODO: we're missing some stages: copyediting, typesetting, proofing, maybe others
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