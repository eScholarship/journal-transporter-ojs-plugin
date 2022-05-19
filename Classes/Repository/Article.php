<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class Article {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'article';

    /**
     * @param $journal
     * @return mixed
     */
    public function fetchByJournal($journal)
    {
        return $this->getArticlesByJournalId($journal->getId());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetchById($id)
    {
        $article = $this->getArticle($id);
        if(is_null($article)) throw new \Exception("Article $id not found");
        return $article;
    }

    public function fetchByIdAndJournal($id, $journal)
    {
        $article = $this->getArticle($id, $journal->getId());
        if(is_null($article)) throw new \Exception("Article $id not found");
        return $article;
    }

}
