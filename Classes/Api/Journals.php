<?php namespace CdlExportPlugin\Api;

use CdlExportPlugin\Builder\Mapper\NestedMapper;
use CdlExportPlugin\Utility\DataObjectUtility;

class Journals extends ApiRoute {
    protected $journalRepository;

    public function execute($parameters, $arguments)
    {
        return @$parameters['journal'] ?
            $this->getJournal($parameters['journal'], $arguments[ApiRoute::DEBUG_ARGUMENT]) : $this->getJournals();
    }

    /**
     * @param $id
     * @return array|mixed|\stdClass
     * @throws \Exception
     */
    protected function getJournal($id, $debug)
    {
        $item = $this->journalRepository->fetchOneById($id);
        if($debug) return DataObjectUtility::dataObjectToArray($item);
        return NestedMapper::map($item);
    }

    /**
     * @return array
     */
    protected function getJournals()
    {
        $resultSet = $this->journalRepository->fetchAll();

        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $resultSet->toArray());
    }
}