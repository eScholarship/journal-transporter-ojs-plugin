<?php namespace CdlExportPlugin\Api\Journals;

use CdlExportPlugin\Api\ApiRoute;
use CdlExportPlugin\Builder\Mapper\NestedMapper;

class Sections extends ApiRoute {
    protected $journalRepository;
    protected $sectionRepository;

    /**
     * @param $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $resultSet = $this->sectionRepository->fetchByJournal($journal);

        return array_map(function($item) {
            return NestedMapper::map($item);
        }, $resultSet->toArray());
    }
}