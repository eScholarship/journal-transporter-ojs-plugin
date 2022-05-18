<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ReviewFormElement {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'reviewFormElement';

    /**
     * @param $reviewForm
     * @return mixed
     */
    public function fetchByReviewForm($reviewForm)
    {
        return $this->getReviewFormElements($reviewForm->getId());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetchOneById($id)
    {
        return $this->getReviewFormElement($id);
    }
}
