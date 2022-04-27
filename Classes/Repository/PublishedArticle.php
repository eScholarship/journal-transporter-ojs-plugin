<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class PublishedArticle {
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('publishedArticle')->getPublishedArticleByArticleId($article->getId());
    }
}