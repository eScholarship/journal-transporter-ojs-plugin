<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class Role {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'role';

    /**
     * @param $journal
     * @return mixed
     */
    public function fetchByJournal($journal)
    {
        return $this->getUsersByJournalId($journal->getId());
    }

    /**
     * @param $user
     * @param $journal
     * @return mixed
     */
    public function fetchByUserAndJournal($user, $journal)
    {
        return $this->getRolesByUserId($user->getId(), $journal->getId());
    }
}
