<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\DAOFactory;

class GalleyFile
{
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('articleGalley')->getGalleysByArticle($article->getId());
    }
}
