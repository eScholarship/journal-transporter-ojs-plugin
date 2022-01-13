<?php namespace CdlExportPlugin\Api;

use CdlExportPlugin\Utility\DataObjectUtility;
use CdlExportPlugin\Utility\Traits\DAOCache;

class Journals {
    use DAOCache;

    public function execute($args)
    {
        return @$args['journal'] ? $this->getJournal($args['journal']) : $this->getJournals();
    }

    private function getJournal($id) {
        $journal = $this->getDAO('journal')->getJournal($id);
        if(is_null($journal)) throw new \Exception("Journal $id not found");
        return DataObjectUtility::dataObjectToArray($journal);
    }

    private function getJournals() {
        $journalsResultSet = $this->getDAO('journal')->getJournals();
        $data = [];
        foreach ($journalsResultSet->toArray() as $journal) {
            $data[] = [
                'title' => $journal->getLocalizedTitle(),
                'path' => $journal->getPath(),
                'id' => $journal->getId()
            ];
        }
        return $data;
    }
}