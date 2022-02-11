<?php namespace CdlExportPlugin\Api\Journals;

use CdlExportPlugin\Api\ApiRoute;
use CdlExportPlugin\Builder\Mapper\NestedMapper;
use CdlExportPlugin\Utility\DataObjectUtility;

class Issues extends ApiRoute  {
    protected $journalRepository;
    protected $issueRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $resultSet = $this->issueRepository->fetchByJournal($journal);

        if($arguments[ApiRoute::DEBUG_ARGUMENT]) return DataObjectUtility::resultSetToArray($resultSet);

        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $resultSet->toArray());
    }
}