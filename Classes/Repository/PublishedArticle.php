<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class PublishedArticle {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'publishedArticle';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getPublishedArticleByArticleId($article->getId());
    }
}