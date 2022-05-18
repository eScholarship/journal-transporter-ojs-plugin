<?php namespace JournalTransporterPlugin\Api\Journals;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObjectUtility;

class ReviewForms extends ApiRoute  {
    protected $journalRepository;
    protected $reviewFormRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);

        return @$parameters['review_form'] ?
            $this->getReviewForm($parameters['review_form'], $journal, $arguments[ApiRoute::DEBUG_ARGUMENT]) :
            $this->getReviewForms($journal);
    }

    protected function getReviewForms($journal) {
        $resultSet = $this->reviewFormRepository->fetchByJournal($journal);

        return array_map(function($item) {
            return NestedMapper::map($item, 'index');
        }, $resultSet->toArray());
    }

    protected function getReviewForm($id, $journal, $debug) {
        $reviewForm = $this->reviewFormRepository->fetchOneById($id);
        if($debug) return $this->getDebugResponse($reviewForm);
        return NestedMapper::map($reviewForm);
    }

    /**
     * @param $article
     * @return object
     */
    protected function getDebugResponse($reviewForm) {
        return $reviewForm;
    }
}