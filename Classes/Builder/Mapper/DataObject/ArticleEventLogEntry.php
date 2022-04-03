<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class ArticleEventLogEntry extends AbstractDataObjectMapper
{
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'logId'],
        ['property' => 'articleId'],
        ['property' => 'userId'],
        ['property' => 'dateLogged', 'filters' => ['datetime']],
        ['property' => 'ip', 'source' => 'iPaddress'],
        ['property' => 'logLevel'],
        ['property' => 'eventType'],
        ['property' => 'assocType'],
        ['property' => 'message'],
        ['property' => 'logLevelString'],
        ['property' => 'eventTitle'],
        ['property' => 'userFullName'],
        ['property' => 'userEmail'],
        ['property' => 'assocTypeString'],
        ['property' => 'assocTypeLongString'],
    ];
}