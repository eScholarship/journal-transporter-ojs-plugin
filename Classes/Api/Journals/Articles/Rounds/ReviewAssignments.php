<?php namespace JournalTransporterPlugin\Api\Journals\Articles\Rounds;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Utility\DataObjectUtility;

class ReviewAssignments extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $reviewAssignmentRepository;
    protected $sectionEditorSubmissionRepository;
    protected $reviewFormResponseRepository;

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
        $reviewAssignmentId = (int) $parameters['review_assignment'];

        if($reviewAssignmentId > 0) {
            // There doesn't seem to be a way to get review assignments by id, so we do it this way so that we're
            // sure that we're showing a review assignment associated with the article.
            foreach($reviewAssignments as $reviewAssignment) {
                if((int) $reviewAssignmentId !== (int) $reviewAssignment->getId()) continue;
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