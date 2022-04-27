<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class Section {

    public function fetchByJournal($journal)
    {
        return DAOFactory::get()->getDAO('section')->getJournalSections($journal->getId());
    }

    public function fetchByIdAndJournal($id, $journal)
    {
        return DAOFactory::get()->getDAO('section')->getSection($id, $journal->getId());
    }

}
