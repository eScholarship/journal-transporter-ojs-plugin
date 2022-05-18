<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ReviewAssignment {
    public function fetchByArticle($article, $round = null)
    {
        return DAOFactory::get()->getDAO('reviewAssignment')
            ->getReviewAssignmentsByArticleId($article->getId(), $round);
    }
}