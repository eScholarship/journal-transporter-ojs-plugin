<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class Section {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'section';

    /**
     * @param $journal
     * @return mixed
     */
    public function fetchByJournal($journal)
    {
        return $this->getJournalSections($journal->getId());
    }

    /**
     * @param $id
     * @param $journal
     * @return mixed
     */
    public function fetchByIdAndJournal($id, $journal)
    {
        return $this->getSection($id, $journal->getId());
    }

}
