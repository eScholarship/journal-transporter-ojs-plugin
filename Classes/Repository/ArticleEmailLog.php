<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ArticleEmailLog {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'articleEmailLog';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getArticleLogEntries($article->getId());
    }
}
