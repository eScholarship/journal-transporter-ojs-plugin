<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;
use JournalTransporterPlugin\Exception\CannotFetchDataObjectException;

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
        if(is_null($article)) throw new CannotFetchDataObjectException("Article $id not found");
        return $article;
    }

    public function fetchByIdAndJournal($id, $journal)
    {
        $article = $this->getArticle($id, $journal->getId());
        if(is_null($article)) throw new CannotFetchDataObjectException("Article $id not found");
        return $article;
    }

}
