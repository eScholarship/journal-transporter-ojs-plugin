<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use Config;
use JournalTransporterPlugin\Utility\Files;

class Issue extends AbstractDataObjectMapper {
    protected static $contexts = ['index' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'title']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'title', 'source' => 'localizedTitle'],
        ['property' => 'volume'],
        ['property' => 'number'],
        ['property' => 'year'],
        ['property' => 'published', 'filters' => ['boolean']],
        ['property' => 'current', 'filters' => ['boolean']],
        ['property' => 'datePublished', 'filters' => ['datetime']],
        ['property' => 'description', 'source' => 'localizedCoverPageDescription'],
        ['property' => 'coverPageAltText', 'source' => 'localizedCoverPageAltText'],
        ['property' => 'width', 'source' => 'issueWidth'],
        ['property' => 'height', 'source' => 'issueHeight'],
        ['property' => 'articlesCount', 'source' => 'numArticles'],
        ['property' => 'coverFile', 'source' => 'coverFileName']
    ];

    public static function preMap($dataObject, $context)
    {
        $dataObject->coverFileName = null;
        if(!is_null($dataObject->getLocalizedFilename())) {
            $dataObject->coverFileName = (object) [
                'url' => Files::getPublicJournalUrl($dataObject->getJournalId()) . '/' .
                    $dataObject->getLocalizedFilename(),
                'upload_name' => $dataObject->getLocalizedOriginalFileName()
            ];
        }
        return $dataObject;
    }
}