<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Utility\SourceRecordKeyUtility;

class EditorDecision extends AbstractDataObjectMapper {
    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'editDecisionId'],
        ['property' => 'editorSourceRecordKey'],
        ['property' => 'date', 'source' => 'dateDecided', 'filters' => ['datetime']],
        ['property' => 'decision'],
        ['property' => 'round', 'onError' => 'undefined']
    ];

    /**
     * @param $dataObject
     * @param $context
     * @return mixed
     */
    protected static function preMap($dataObject, $context) {
        // See: classes/submission/common/Action.inc.php:21-24
        $dataObject->decision = @[
            1 => 'accept',
            2 => 'revise',
            3 => 'resubmit',
            4 => 'decline'
        ][(int) $dataObject->decision];

        // TODO: address generation of source record keys
        $dataObject->editorSourceRecordKey = SourceRecordKeyUtility::editor($dataObject->editorId);

        return $dataObject;
    }
}
