<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class SupplementaryFile
{
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('suppFile')->getSuppFilesByArticle($article->getId());
    }
}
