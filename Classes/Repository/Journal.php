<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;
use JournalTransporterPlugin\Utility\Traits\DAOCache;

class Journal {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'journal';

    /**
     * @return mixed
     */
    public function fetchAll()
    {
        return $this->getJournals();
    }

    /**
     * @param $ids
     * @param $paths
     * @return array
     */
    public function fetchByIdsAndPaths($ids, $paths) {
        $journals = [];

        foreach($ids as $id) {
            $journal = $this->getJournal($id);
            if($journal) $journals[] = $journal;
        }

        foreach($paths as $path) {
            $journal = $this->getJournalByPath($path);
            if($journal) $journals[] = $journal;
        }

        return $journals;
    }

    /**
     * Path will work here too
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function fetchOneById($id)
    {
        if(preg_match('/^[0-9]+$/', $id)) {
            $journal = $this->getJournal($id);
        } else {
            $journal = $this->getJournalByPath($id);
        }

        if(is_null($journal)) throw new \Exception("Journal $id not found");
        return $journal;
    }

}
