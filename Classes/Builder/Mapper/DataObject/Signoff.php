<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

class Signoff extends AbstractDataObjectMapper {
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'user', 'source' => 'userId', 'sourceRecordKey' => 'user'],
        ['property' => 'signoffType', 'source' => 'symbolic'],
        ['property' => 'dateNotified', 'filters' => ['datetime']],
        ['property' => 'dateCompleted', 'filters' => ['datetime']],
        ['property' => 'dateAcknowledged', 'filters' => ['datetime']],
    ];
}
