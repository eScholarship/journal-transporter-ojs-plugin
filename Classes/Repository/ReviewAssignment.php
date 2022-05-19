<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ReviewAssignment {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'reviewAssignment';

    /**
     * @param $article
     * @param null $round
     * @return mixed
     */
    public function fetchByArticle($article, $round = null)
    {
        return $this->getReviewAssignmentsByArticleId($article->getId(), $round);
    }
}