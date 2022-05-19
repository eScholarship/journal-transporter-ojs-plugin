<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

class ReviewFormElement extends AbstractDataObjectMapper
{
    protected static $contexts = ['index' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'type']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'sequence', 'filters' => ['integer']],
        ['property' => 'type'],
        ['property' => 'required', 'filters' => ['boolean']],
        ['property' => 'visible_to_author', 'source' => 'included', 'filters' => ['boolean']],
        ['property' => 'question', 'source' => 'localizedQuestion', 'filters' => ['html']],
        ['property' => 'responses'],
    ];

    /**
     * @param $dataObject
     * @return mixed
     */
    protected static function preMap($dataObject) {
        $dataObject->type = self::getElementType($dataObject->getElementType());
        $dataObject->responses = self::getFormattedResponses($dataObject->getLocalizedPossibleResponses());
        return $dataObject;
    }

    /**
     * @param $responses
     * @return object[]|null
     */
    protected static function getFormattedResponses($responses) {
        if(is_null($responses)) return null;
        return array_map(
            function($response) { return (object) ['key' => $response['order'], 'value' => $response['content']]; },
            $responses
        );
    }

    /**
     * @param $type
     * @return string
     */
    protected static function getElementType($type)
    {
        # See: classes/reviewForm/ReviewFormElement.inc.php
        return @[
            REVIEW_FORM_ELEMENT_TYPE_SMALL_TEXT_FIELD => 'small_text',
            REVIEW_FORM_ELEMENT_TYPE_TEXT_FIELD => 'text',
            REVIEW_FORM_ELEMENT_TYPE_TEXTAREA => 'textarea',
            REVIEW_FORM_ELEMENT_TYPE_CHECKBOXES => 'checkboxes',
            REVIEW_FORM_ELEMENT_TYPE_RADIO_BUTTONS => 'radio_buttons',
            REVIEW_FORM_ELEMENT_TYPE_DROP_DOWN_BOX => 'select'
        ][$type];
    }
}