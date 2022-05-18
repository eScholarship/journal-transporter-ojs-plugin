<?php namespace JournalTransporterPlugin\Api\Journals\Articles\Reviews;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObjectUtility;

class Review extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $reviewAssignmentRepository;
    protected $sectionEditorSubmissionRepository;

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
        $reviewId = (int) $parameters['review'];

        if($reviewId > 0) {
            foreach($reviewAssignments as $reviewAssignment) {
                if((int) $reviewId !== (int) $reviewAssignment->getId()) continue;
                if($arguments[ApiRoute::DEBUG_ARGUMENT]) return DataObjectUtility::dataObjectToArray($reviewAssignment);
                return NestedMapper::map($reviewAssignment);
            }
        } else {
            if($arguments[ApiRoute::DEBUG_ARGUMENT]) {
                return array_map(function($item) {
                    return DataObjectUtility::dataObjectToArray($item);
                }, array_values($reviewAssignments));
            } else {
                return array_map(function($item) {
                    return NestedMapper::map($item, 'sourceRecordKey');
                }, array_values($reviewAssignments));
            }
        }
    }
}