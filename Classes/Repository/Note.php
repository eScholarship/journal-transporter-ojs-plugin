<?php namespace JournalTransporterPlugin\Repository;

class Note {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'note';

    /**
     * @param $article
     * @param null $round
     * @return mixed
     */
    public function fetchByReviewAssignment($reviewAssignment) {
        return $this->getByAssoc(257, 54515)->toArray();
    }
}