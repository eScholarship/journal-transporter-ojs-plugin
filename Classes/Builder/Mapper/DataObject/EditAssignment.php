<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

class EditAssignment extends AbstractDataObjectMapper {
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'editId'],
        ['property' => 'editorId'],
        ['property' => 'isEditor', 'filters' => ['boolean']],
        ['property' => 'dateNotified', 'filters' => ['datetime']],
        ['property' => 'dateUnderway', 'filters' => ['datetime']],
        ['property' => 'fullName', 'source' => 'editorFullName'],
        ['property' => 'firstName', 'source' => 'editorFirstName'],
        ['property' => 'lastName', 'source' => 'editorLastName'],
        ['property' => 'initials', 'source' => 'editorInitials'],
        ['property' => 'email', 'source' => 'editorEmail'],
    ];
}