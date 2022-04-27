<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ReviewAssignment {
    public function fetchByArticle($article)
    {
        return DAOFactory::get()->getDAO('reviewAssignment')->getReviewAssignmentsByArticleId($article->getId());
    }
}