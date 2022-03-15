<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\DAOFactory;

class SupplementaryFile
{
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('suppFile')->getSuppFilesByArticle($article->getId());
    }
}
