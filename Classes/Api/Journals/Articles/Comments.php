<?php namespace JournalTransporterPlugin\Api\Journals\Articles;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Api\ApiRoute;

class Comments extends ApiRoute  {
    protected $journalRepository;
    protected $articleRepository;
    protected $articleCommentRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($parameters['article'], $journal);
        $comments = $this->articleCommentRepository->fetchByArticle($article);

        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $comments);
    }
}