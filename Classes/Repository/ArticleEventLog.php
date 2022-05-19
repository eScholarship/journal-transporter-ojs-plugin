<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ArticleEventLog {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'articleEventLog';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getArticleLogEntries($article->getId());
    }
}
