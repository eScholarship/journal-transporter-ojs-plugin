<?php namespace JournalTransporterPlugin\Api\Journals\Articles\Reviews\Review;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObjectUtility;

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
            $responseOutput[] = $this->formatResponse($formElementId, $responseValue);
        }
        return $responseOutput;
    }

    /**
     * @param $formElementId
     * @param $responseValue
     * @return object
     */
    protected function formatResponse($formElementId, $responseValue) {
        $reviewFormElement = $this->reviewFormElementRepository->fetchOneById($formElementId);

        // TODO: to reduce the reviewFormElement to just a sourceRecordKey, add 'sourceRecordKey' as the context
        // in the second argument of map()
        return (object)['reviewFormElement' => NestedMapper::map($reviewFormElement), 'responseValue' => $responseValue];
    }
}