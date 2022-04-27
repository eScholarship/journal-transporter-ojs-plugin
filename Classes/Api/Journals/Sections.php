<?php namespace JournalTransporterPlugin\Api\Journals;

use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Utility\DataObjectUtility;

class Sections extends ApiRoute {
    protected $journalRepository;
    protected $sectionRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        if(@$parameters['section']) {
            return $this->getSection($parameters['section'], $parameters['journal'], $arguments[ApiRoute::DEBUG_ARGUMENT]);
        } else {
            return $this->getSections($parameters);
        }
    }

    protected function getSection($sectionId, $journalId, $debug)
    {
        $journal = $this->journalRepository->fetchOneById($journalId);
        $item = $this->sectionRepository->fetchByIdAndJournal($sectionId, $journal);
        if($debug) return DataObjectUtility::dataObjectToArray($item);
        return NestedMapper::map($item);
    }

    /**
     * @param $parameters
     * @return array
     */
    public function getSections($parameters)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $resultSet = $this->sectionRepository->fetchByJournal($journal);

        return array_map(function($item) {
            return NestedMapper::map($item, 'index');
        }, $resultSet->toArray());
    }
}