<?php namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObjectUtility;

class Reviews extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $reviewAssignmentRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $reviewAssignments = $this->reviewAssignmentRepository->fetchByArticle($article);

        if($arguments[ApiRoute::DEBUG_ARGUMENT]) {
            return array_map(function($item) {
                return DataObjectUtility::dataObjectToArray($item);
            }, array_values($reviewAssignments));
        } else {
            return array_map(function($item) {
                return NestedMapper::map($item);
                //return DataObjectUtility::dataObjectToArray($item);
            }, array_values($reviewAssignments));
        }
    }
}