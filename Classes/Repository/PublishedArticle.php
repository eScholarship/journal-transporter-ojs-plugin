<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\DAOFactory;

class PublishedArticle {
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('publishedArticle')->getPublishedArticleByArticleId($article->getId());
    }
}