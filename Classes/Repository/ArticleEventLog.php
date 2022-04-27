<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ArticleEventLog {
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('articleEventLog')->getArticleLogEntries($article->getId());
    }
}
