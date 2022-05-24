<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

class Note extends AbstractDataObjectMapper {
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'user', 'source' => 'userId', 'sourceRecordKey' => 'user'],
        ['property' => 'dateCreated', 'filters' => ['datetime']],
        ['property' => 'dateModified', 'filters' => ['datetime']],
        ['property' => 'contents', 'filters' => ['html']],
        ['property' => 'title'],
        ['property' => 'file', 'context' => 'sourceRecordKey'],
        ['property' => 'reviewAssignment', 'source' => 'assocId', 'sourceRecordKey' => 'reviewAssignment'],
    ];
}
