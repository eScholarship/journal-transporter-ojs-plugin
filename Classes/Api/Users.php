<?php namespace JournalTransporterPlugin\Api;

use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Utility\DataObject;

class Users extends ApiRoute {
    protected $userRepository;

    public function execute($parameters, $arguments)
    {
        return $this->getUser($parameters['user'], $arguments[ApiRoute::DEBUG_ARGUMENT]);
    }

    /**
     * @param $id
     * @return array|mixed|\stdClass
     * @throws \Exception
     */
    protected function getUser($id, $debug)
    {
        $item = $this->userRepository->fetchById($id);
        if($debug) return DataObject::dataObjectToArray($item);
        return NestedMapper::map($item);
    }
}