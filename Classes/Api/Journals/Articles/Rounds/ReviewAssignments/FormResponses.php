<?php namespace JournalTransporterPlugin\Api\Journals\Articles\Rounds\ReviewAssignments;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObject;
use JournalTransporterPlugin\Utility\HTML;
use JournalTransporterPlugin\Utility\SourceRecordKey;

class FormResponses extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $reviewAssignmentRepository;
    protected $reviewFormResponseRepository;
    protected $reviewFormElementRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $reviewAssignments = $this->reviewAssignmentRepository->fetchByArticle($article, (int) $parameters['round']);
        $reviewAssignmentId = $parameters['review_assignment'];

        $reviewAssignment = null;
        foreach($reviewAssignments as $reviewAssignment) {
            if ((int)$reviewAssignmentId === (int)$reviewAssignment->getId()) break;
        }

        if(is_null($reviewAssignment))
            throw new UnknownDatabaseAccessObjectException("ReviewAssignment $reviewAssignmentId not found");

        $responses = $this->reviewFormResponseRepository->fetchByReview($reviewAssignment);

        $responseOutput = [];
        $index = 0;
        foreach($responses as $formElementId => $responseValue) {
            // TODO: this passing of the index to render a source record key could merit revisiting
            $responseOutput[] = $this->formatResponse($reviewAssignment, $formElementId, $responseValue, $index);
            $index++;
        }
        return $responseOutput;
    }

    /**
     * @param $formElementId
     * @param $responseValue
     * @return object
     */
    protected function formatResponse($reviewAssignment, $formElementId, $responseData, $index) {
        $reviewFormElement = $this->reviewFormElementRepository->fetchOneById($formElementId);

        /**
         * Put together an array of the allowed options, if relevant
         */
        $responseOrderToContentMap = [];
        foreach ($reviewFormElement->getLocalizedPossibleResponses() as $responseOption) {
            $responseOrder = $responseOption['order'];
            $responseContent = $responseOption['content'];
            $responseOrderToContentMap[$responseOrder] = HTML::cleanHtml($responseContent);
        }

        /**
         * Different types of responses are handled differently. The messiest is when it's an array, which indicates
         * that it's a checkbox field type, which allows multiple selections.
         */
        $responseOutputValue = null;
        if(is_integer($responseData)) {
            if(array_key_exists($responseData, $responseOrderToContentMap)) {
                $responseOutputValue = $responseOrderToContentMap[$responseData];
            }
        } elseif(is_string($responseData)) {
            $responseOutputValue = HTML::cleanHtml($responseData);
        } elseif(is_array($responseData)) {
            $responseOutputValue = $responseData;

            foreach($responseData as $k => $v) {
                if(array_key_exists($v, $responseOrderToContentMap)) {
                    $responseOutputValue[$k] = $responseOrderToContentMap[$v];
                }
            }
        }

        // To show form element, remove 'sourceRecordKey' value from the reviewFormElement to just a sourceRecordKey
        return (object)[
            'source_record_key' => SourceRecordKey::reviewAssignmentResponse($reviewAssignment->getId(), $index),
            'review_form_element' => NestedMapper::map($reviewFormElement, 'sourceRecordKey'),
            'response_value' => $responseOutputValue
        ];
    }
}