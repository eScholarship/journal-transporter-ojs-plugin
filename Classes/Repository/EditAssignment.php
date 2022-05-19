<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class EditAssignment {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'editAssignment';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getEditAssignmentsByArticleId($article->getId());
    }
}