<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class ReviewForm {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'reviewForm';

    /**
     * @param $journal
     * @return mixed
     */
    public function fetchByJournal($journal)
    {
        return $this->getByAssocId(256, $journal->getId());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function fetchOneById($id, $journal = null)
    {
        return is_null($journal) ? $this->getReviewForm($id) : $this->getReviewForm($id, 256, $journal->getId());
    }
}
