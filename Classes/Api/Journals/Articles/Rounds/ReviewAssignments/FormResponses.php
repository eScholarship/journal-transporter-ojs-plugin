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
    protected function formatResponse($reviewAssignment, $formElementId, $responseKey, $index) {
        $reviewFormElement = $this->reviewFormElementRepository->fetchOneById($formElementId);

        $responseValue = $responseKey;
        if(!(preg_match('/^[0-9]+$/'))) {
            foreach ($reviewFormElement->getLocalizedPossibleResponses() as $response) {
                if ($response['order'] == $responseKey) $responseValue = HTML::cleanHtml($response['content']);
            }
        }

        // To show form element, remove 'sourceRecordKey' value from the reviewFormElement to just a sourceRecordKey
        return (object)[
            'source_record_key' => SourceRecordKey::reviewAssignmentResponse($reviewAssignment->getId(), $index),
            'review_form_element' => NestedMapper::map($reviewFormElement, 'sourceRecordKey'),
            'response_value' => $responseValue
        ];
    }
}