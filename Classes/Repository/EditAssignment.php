<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class EditAssignment {
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('editAssignment')->getEditAssignmentsByArticleId($article->getId());
    }
}