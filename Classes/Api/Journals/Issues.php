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
        if(@$parameters['issue']) {
            return $this->getIssue($parameters['issue'], $parameters['journal'], $arguments[ApiRoute::DEBUG_ARGUMENT]);
        } else {
            return $this->getIssues($parameters);
        }
    }

    protected function getIssue($issueId, $journalId, $debug)
    {
        $journal = $this->journalRepository->fetchOneById($journalId);
        $item = $this->issueRepository->fetchByIdAndJournal($issueId, $journal);
        if($debug) return DataObjectUtility::dataObjectToArray($item);
        return NestedMapper::map($item);
    }


    /**
     * @param $parameters
     * @return array
     */
    public function getIssues($parameters)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $resultSet = $this->issueRepository->fetchByJournal($journal);

        return array_map(function($item) {
            return NestedMapper::map($item, 'index');
        }, $resultSet->toArray());
    }
}