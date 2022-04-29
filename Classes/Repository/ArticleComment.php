<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ArticleComment {

    public function fetchByArticleAndReview($article, $review)
    {
        return DAOFactory::get()->getDAO('articleComment')->getArticleComments(
            $article->getId(),
            1,
            $review->getReviewId()
        );
    }

}
