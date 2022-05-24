<?php namespace JournalTransporterPlugin\Api\Journals\Articles\Rounds\ReviewAssignments;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;

class Notes extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $reviewAssignmentRepository;
    protected $noteRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        // TODO: there's some duplicated code here, see FormReponses
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $reviewAssignments = $this->reviewAssignmentRepository->fetchByArticle($article, (int) $parameters['round']);
        $reviewAssignmentId = $parameters['review_assignment'];

        $reviewAssignment = null;
        foreach($reviewAssignments as $reviewAssignment) {
            if ((int)$reviewAssignmentId === (int)$reviewAssignment->getId()) break;
        }

        if(is_null($reviewAssignment)) throw new \Exception("ReviewAssignment $reviewAssignmentId not found");

        $notes = $this->noteRepository->fetchByReviewAssignment($reviewAssignment);

        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $notes);
    }

}