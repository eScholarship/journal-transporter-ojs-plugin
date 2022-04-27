<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class GalleyFile
{
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('articleGalley')->getGalleysByArticle($article->getId());
    }
}
