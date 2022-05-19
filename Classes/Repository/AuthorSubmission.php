<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class AuthorSubmission {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'authorSubmission';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getAuthorSubmission($article->getId());
    }

    /**
     * @param $article
     * @return mixed
     */
    public function fetchEditorDecisionsByArticle($article)
    {
        return $this->getEditorDecisions($article->getId());
    }
}
