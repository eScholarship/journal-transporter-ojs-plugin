<?php namespace JournalTransporterPlugin\Api\Journals\Articles\Rounds\ReviewAssignments;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObjectUtility;
use JournalTransporterPlugin\Utility\SourceRecordKeyUtility;

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

        if(is_null($reviewAssignment)) throw new \Exception("ReviewAssignment $reviewAssignmentId not found");

        $responses = $this->reviewFormResponseRepository->fetchByReview($reviewAssignment);

        $responseOutput = [];
        foreach($responses as $formElementId => $responseValue) {
            $responseOutput[] = $this->formatResponse($reviewAssignment, $formElementId, $responseValue);
        }
        return $responseOutput;
    }

    /**
     * @param $formElementId
     * @param $responseValue
     * @return object
     */
    protected function formatResponse($reviewAssignment, $formElementId, $responseValue) {
        $reviewFormElement = $this->reviewFormElementRepository->fetchOneById($formElementId);

        // To show form element, remove 'sourceRecordKey' value from the reviewFormElement to just a sourceRecordKey
        return (object)['reviewFormElement' => NestedMapper::map($reviewFormElement, 'sourceRecordKey'), 'responseValue' => $responseValue];
    }
}