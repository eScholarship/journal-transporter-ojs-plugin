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
    public function fetchByArticleAndReview($article, $review = null)
    {
        return $this->getArticleComments(
            $article->getId(),
            1,
            is_object($review) ? $review->getReviewId() : null
        );
    }

    /**
     * @param $article
     * @param $review
     * @return mixed
     */
    public function fetchEditorCommentsByArticle($article)
    {
        return $this->getArticleComments(
            $article->getId(),
            2
        );
    }


    /**
     * @param $article
     * @return mixed
     */
    public function fetchByArticle($article)
    {
        return $this->getArticleComments($article->getId());
    }

}
