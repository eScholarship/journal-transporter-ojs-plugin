<?php namespace JournalTransporterPlugin\Api\Journals\ReviewForms;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObjectUtility;

class Elements extends ApiRoute  {
    protected $journalRepository;
    protected $reviewFormRepository;
    protected $reviewFormElementRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $reviewForm = $this->reviewFormRepository->fetchOneById($parameters['review_form'], $journal);

        return @$parameters['review_form_element'] ?
            $this->getReviewFormElement($parameters['review_form_element'], $reviewForm, $arguments[ApiRoute::DEBUG_ARGUMENT]) :
            $this->getReviewFormElements($reviewForm);
    }

    protected function getReviewFormElements($reviewForm) {
        $resultSet = $this->reviewFormElementRepository->fetchByReviewForm($reviewForm);

        return array_map(function($item) {
            return NestedMapper::map($item, 'index');
        }, $resultSet);
    }

    protected function getReviewFormElement($id, $reviewForm, $debug) {
        $reviewFormElement = $this->reviewFormElementRepository->fetchOneById($id);
        if($debug) return $this->getDebugResponse($reviewFormElement);
        return NestedMapper::map($reviewFormElement);
    }

    /**
     * @param $article
     * @return object
     */
    protected function getDebugResponse($reviewFormElement) {
        return $reviewFormElement;
    }
}