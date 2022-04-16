<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\DAOFactory;

class AuthorSubmission {

    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('authorSubmission')->getAuthorSubmission($article->getId());
    }

    public function fetchEditorDecisionsByArticle($article)
    {
        return DAOFactory::get()->getDAO('authorSubmission')->getEditorDecisions($article->getId());
    }
}
