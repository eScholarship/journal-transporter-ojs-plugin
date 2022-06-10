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
        $dataObject = self::addStatusProperties($dataObject);

        $dataObject->mostRecentEditorDecision = self::getMostRecentEditorDecision($dataObject);

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
     * @param $dataObject
     * @return mixed
     */
    protected static function addStatusProperties($dataObject)
    {
        $dataObject->publishedArticle = (new PublishedArticle)->fetchByArticle($dataObject);
        $dataObject->publicationStatus =
            is_null($dataObject->publishedArticle) ? self::getUnpublishedArticleStatus($dataObject) : 'published';
        return $dataObject;
    }


    /**
     * @param $status
     */
    protected static function getUnpublishedArticleStatus($dataObject)
    {
        $status = $dataObject->getStatus();

        return @[STATUS_ARCHIVED => 'rejected', // If not published and yes archived, it's rejected for all intents and purposes
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