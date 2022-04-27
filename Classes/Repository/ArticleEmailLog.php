<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ArticleEmailLog {
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('articleEmailLog')->getArticleLogEntries($article->getId());
    }
}
