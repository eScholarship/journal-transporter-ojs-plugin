<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

class ArticleComment extends AbstractDataObjectMapper
{
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'commentId'],
        ['property' => 'commentTitle'],
        ['property' => 'comments', 'filters' => ['html']],
        ['property' => 'datePosted', 'filters' => ['datetime']],
        ['property' => 'dateModified', 'filters' => ['datetime']],
        ['property' => 'visibleToAuthor', 'source' => 'viewable', 'filters' => ['boolean']],
        ['property' => 'author', 'source' => 'authorId', 'sourceRecordKey' => 'user'],
        ['property' => 'role', 'source' => 'roleId', 'mapTo' => 'role'],
        ['property' => 'commentType', 'mapTo' => 'commentType']
    ];
}

