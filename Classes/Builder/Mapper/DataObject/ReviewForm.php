<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

class ReviewForm extends AbstractDataObjectMapper
{
    protected static $contexts = ['index' => ['exclude' => '*', 'include' => ['sourceRecordKey']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'sequence', 'filters' => ['integer']],
        ['property' => 'active', 'filters' => ['boolean']],
        ['property' => 'description', 'source' => 'localizedDescription', 'filters' => ['html']],
        ['property' => 'title', 'source' => 'localizedTitle']
    ];
}