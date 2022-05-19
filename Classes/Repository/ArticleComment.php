<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ArticleComment {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'articleComment';

    /**
     * @param $article
     * @param $review
     * @return mixed
     */
    public function fetchByArticleAndReview($article, $review)
    {
        return $this->getArticleComments(
            $article->getId(),
            1,
            $review->getReviewId()
        );
    }

}
