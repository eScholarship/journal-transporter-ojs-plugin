<?php namespace JournalTransporterPlugin\Api\Journals\Articles\Digest;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Utility\DataObject;
use JournalTransporterPlugin\Api\ApiRoute;

class LogEntries extends ApiRoute {
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

        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $resultSet->toArray());
    }
}