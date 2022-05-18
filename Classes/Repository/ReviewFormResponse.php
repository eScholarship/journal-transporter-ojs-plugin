<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ReviewFormResponse {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'reviewFormResponse';

    /**
     * @param $review
     * @return mixed
     */
    public function fetchByReview($review)
    {
        return $this->getReviewReviewFormResponseValues($review->getId());
    }

//    /**
//     * @param $id
//     * @return mixed
//     */
//    public function fetchOneById($id)
//    {
//        return $this->getReviewFormElement($id);
//    }
}
