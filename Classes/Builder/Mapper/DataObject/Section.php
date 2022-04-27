<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

class Section extends AbstractDataObjectMapper {
    protected static $contexts = ['index' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'title']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'title', 'source' => 'localizedTitle'],
        ['property' => 'abbreviation', 'source' => 'localizedAbbrev'],
        ['property' => 'sequence'],
    ];
}
