<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

class EditorDecision extends AbstractDataObjectMapper {
    protected static $mapping = [
        ['property' => 'editorId'],
        ['property' => 'date', 'source' => 'dateDecided', 'filters' => ['datetime']],
        ['property' => 'decision'],
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
        return $dataObject;
    }
}
