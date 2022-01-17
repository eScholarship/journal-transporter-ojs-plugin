<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\Traits\DAOCache;

class Journal {
    use DAOCache;

    public function fetchAll()
    {
        return $this->getDAO('journal')->getJournals();
    }

    public function fetchOneById($id)
    {
        $journal = $this->getDAO('journal')->getJournal($id);
        if(is_null($journal)) throw new \Exception("Journal $id not found");
        return $journal;
    }
}
