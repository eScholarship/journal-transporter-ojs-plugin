<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class SupplementaryFile {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'suppFile';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('suppFile')->getSuppFilesByArticle($article->getId());
    }
}
