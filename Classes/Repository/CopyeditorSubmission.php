<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class CopyeditorSubmission {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'copyeditorSubmission';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getCopyeditorSubmission($article->getId());
    }
}
