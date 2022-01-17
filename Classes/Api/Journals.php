<?php namespace CdlExportPlugin\Api;

use CdlExportPlugin\Utility\DataObjectUtility;

class Journals extends ApiRoute {
    protected $journalRepository;

    public function execute($args)
    {
        return @$args['journal'] ? $this->getJournal($args['journal']) : $this->getJournals();
    }

    /**
     * @param $id
     * @return array|mixed|\stdClass
     * @throws \Exception
     */
    protected function getJournal($id)
    {
        $journal = $this->journalRepository->fetchOneById($id);
        return DataObjectUtility::dataObjectToArray($journal);
    }

    /**
     * @return array
     */
    protected function getJournals()
    {
        $journalsResultSet = $this->journalRepository->fetchAll();
        $journals = [];
        foreach ($journalsResultSet->toArray() as $journal) {
            $journals[] = [
                'title' => $journal->getLocalizedTitle(),
                'path' => $journal->getPath(),
                'id' => $journal->getId()
            ];
        }
        return $journals;
    }
}