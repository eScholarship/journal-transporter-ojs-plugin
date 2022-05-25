<?php namespace JournalTransporterPlugin\Api;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Utility\DataObject;

class Journals extends ApiRoute {
    protected $journalRepository;

    const JOURNALS_ID_FILTER_ARGUMENT = 'ids';
    const JOURNALS_PATH_FILTER_ARGUMENT = 'paths';

    public function execute($parameters, $arguments)
    {
        if(@$parameters['journal']) {
            return $this->getJournal($parameters['journal'], $arguments[ApiRoute::DEBUG_ARGUMENT]);
        } else {
            return $this->getJournals($this->validArguments($arguments));
        }
    }

    /**
     * @param $id
     * @return array|mixed|\stdClass
     * @throws \Exception
     */
    protected function getJournal($id, $debug)
    {
        $item = $this->journalRepository->fetchOneById($id);
        if($debug) return DataObject::dataObjectToArray($item);
        return NestedMapper::map($item);
    }

    /**
     * @return array
     */
    protected function getJournals($arguments)
    {
        if(count($arguments) > 0) {
            $journals = $this->journalRepository->fetchByIdsAndPaths(
                $arguments[self::JOURNALS_ID_FILTER_ARGUMENT],
                $arguments[self::JOURNALS_PATH_FILTER_ARGUMENT]
            );
        } else {
            $journals = $this->journalRepository->fetchAll($arguments)->toArray();
        }

        return array_map(function($item) {
            return NestedMapper::map($item, 'list');
        }, $journals);
    }

    /**
     * At this point, we're not doing this anywhere else, so I'm restraining myself from abstracting it. Also,
     * this is a little clunky.
     *
     * Note that we're exploding comma lists into arrays.
     *
     * @param $arguments
     */
    protected function validArguments($arguments) {
        $out = [];

        $ids = array_unique(array_filter(array_map('trim',
            explode(',', @$arguments[self::JOURNALS_ID_FILTER_ARGUMENT]))));

        foreach($ids as $id) {
            if(ctype_digit($id)) {
                if(!array_key_exists(self::JOURNALS_ID_FILTER_ARGUMENT, $out))
                    $out[self::JOURNALS_ID_FILTER_ARGUMENT] = [];
                $out[self::JOURNALS_ID_FILTER_ARGUMENT][] = $id;
            }
        }

        $paths = array_unique(array_filter(array_map('trim',
            explode(',', @$arguments[self::JOURNALS_PATH_FILTER_ARGUMENT]))));

        foreach($paths as $path) {
            if(preg_match('/^[0-9a-z_]+$/', $path)) {
                if(!array_key_exists(self::JOURNALS_PATH_FILTER_ARGUMENT, $out))
                    $out[self::JOURNALS_PATH_FILTER_ARGUMENT] = [];
                $out[self::JOURNALS_PATH_FILTER_ARGUMENT][] = $path;
            }
        }

        return $out;
    }
}