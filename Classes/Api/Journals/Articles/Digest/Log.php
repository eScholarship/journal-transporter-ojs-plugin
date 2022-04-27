<?php namespace JournalTransporterPlugin\Api\Journals\Articles\Digest;

use JournalTransporterPlugin\Utility\DataObjectUtility;
use JournalTransporterPlugin\Api\ApiRoute;

class Log extends ApiRoute {
    protected $journalRepository;
    protected $articleRepository;
    protected $articleEventLogRepository;

    /**
     * @param array $args
     * @return array
     * @throws \Exception
     */
    public function execute($args)
    {
        $journal = $this->journalRepository->fetchOneById($args['journal']);
        $article = $this->articleRepository->fetchByIdAndJournal($args['article'], $journal);
        $resultSet = $this->articleEventLogRepository->fetchByArticle($article);
        return DataObjectUtility::resultSetToArray($resultSet);
    }
}