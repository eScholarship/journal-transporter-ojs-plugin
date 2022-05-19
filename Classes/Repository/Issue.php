<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class Issue {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'issue';

    /**
     * @param $journal
     * @return mixed
     */
    public function fetchByJournal($journal)
    {
        return $this->getIssues($journal->getId());
    }

    /**
     * @param $id
     * @param $journal
     * @return mixed
     */
    public function fetchByIdAndJournal($id, $journal)
    {
        return $this->getIssueById($id, $journal->getId());
    }

    /**
     * @param $journal
     * @return mixed
     */
    public function fetchPublishedByJournal($journal)
    {
        return $this->getPublishedIssues($journal->getId());
    }

    /**
     * @param $journal
     * @return mixed
     */
    public function fetchUnpublishedByJournal($journal)
    {
        return $this->getUnpublishedIssues($journal->getId());
    }
}
