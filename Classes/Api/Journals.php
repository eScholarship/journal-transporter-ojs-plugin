<?php namespace CdlExportPlugin\Api;

use CdlExportPlugin\Utility\DataObjectUtility;
use CdlExportPlugin\Utility\Traits\DAOCache;

class Journals {
    use DAOCache;

    public function execute()
    {
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