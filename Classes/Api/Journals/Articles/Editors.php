<?php namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;

class Editors extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $editAssignmentRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $editors = $this->editAssignmentRepository->fetchByArticle($article);

        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $editors->toArray());
    }
}