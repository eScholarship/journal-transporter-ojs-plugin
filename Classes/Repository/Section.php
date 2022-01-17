<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\Traits\DAOCache;

class Section {
    use DAOCache;

    public function fetchByJournal($journal)
    {
        return $this->getDAO('section')->getJournalSections($journal->getId());
    }

}
