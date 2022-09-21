<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Utility\SourceRecordKey;

class ArticleEventLogEntry extends AbstractDataObjectMapper
{
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'logId'],
        ['property' => 'user'],
        ['property' => 'date', 'source' => 'dateLogged', 'filters' => ['datetime']],
        ['property' => 'ip_address', 'source' => 'iPaddress'],
        ['property' => 'level'],
        ['property' => 'title'],
        ['property' => 'description', 'source' => 'message'],
    ];

    /**
     * @param $data
     * @param $dataObject
     * @return mixed
     */
    protected static function preMap($dataObject, $context)
    {
        $dataObject->user = SourceRecordKey::user($dataObject->getUserId());
        $dataObject->title = '[OJS] '.$dataObject->getEventTitle();
        $dataObject->level = end(explode('.', $dataObject->getLogLevelString()));
        return $dataObject;
    }
}