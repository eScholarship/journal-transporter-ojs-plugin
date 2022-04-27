<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

class ArticleComment extends AbstractDataObjectMapper
{
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'commentId'],
        ['property' => 'commentTitle'],
        ['property' => 'comments'],
        ['property' => 'datePosted', 'filters' => ['datetime']],
    ];
}