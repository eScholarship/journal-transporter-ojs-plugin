<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Utility\SourceRecordKeyUtility;

class EditAssignment extends AbstractDataObjectMapper {
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'editId'],
        ['property' => 'editorSourceRecordKey'],
        ['property' => 'isEditor', 'filters' => ['boolean']],
        ['property' => 'dateNotified', 'filters' => ['datetime']],
        ['property' => 'dateUnderway', 'filters' => ['datetime']],
        ['property' => 'fullName', 'source' => 'editorFullName'],
        ['property' => 'firstName', 'source' => 'editorFirstName'],
        ['property' => 'lastName', 'source' => 'editorLastName'],
        ['property' => 'initials', 'source' => 'editorInitials'],
        ['property' => 'email', 'source' => 'editorEmail'],
    ];

    /**
     * @param $dataObject
     * @param $context
     * @return mixed
     */
    protected static function preMap($dataObject, $context) {
        $dataObject->editorSourceRecordKey = SourceRecordKeyUtility::editor($dataObject->getEditorId());

        return $dataObject;
    }
}