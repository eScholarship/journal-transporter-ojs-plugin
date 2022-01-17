<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\Traits\DAOCache;

class Article {
    use DAOCache;

    public function fetchByJournal($journal)
    {
        return $this->getDAO('article')->getArticlesByJournalId($journal->getId());
    }

    public function fetchById($id)
    {
        $article = $this->getDAO('article')->getArticle($id);
        if(is_null($article)) throw new \Exception("Article $id not found");
        return $article;
    }

    public function fetchByIdAndJournal($id, $journal)
    {
        $article = $this->getDAO('article')->getArticle($id, $journal->getId());
        if(is_null($article)) throw new \Exception("Article $id not found");
        return $article;
    }

}
