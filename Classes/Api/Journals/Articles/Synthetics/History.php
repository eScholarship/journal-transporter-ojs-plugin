<?php namespace JournalTransporterPlugin\Api\Journals\Articles\Synthetics;

use JournalTransporterPlugin\Api\ApiRoute;

class History extends ApiRoute {
    protected $journalRepository;
    protected $articleRepository;

    /**
     * @param array $args
     * @return array
     * @throws \Exception
     */
    public function execute($args)
    {
        $journal = $this->journalRepository->fetchOneById($args['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($args['article'], $journal);

        $history = new \JournalTransporterPlugin\Builder\History($article);
        return $history->toArray();
    }
}