<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class GalleyFile {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'articleGalley';

    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getGalleysByArticle($article->getId());
    }
}
