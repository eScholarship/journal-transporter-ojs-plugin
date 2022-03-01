<?php namespace CdlExportPlugin\Api;

use CdlExportPlugin\Builder\Mapper\NestedMapper;
use CdlExportPlugin\Utility\DataObjectUtility;

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
        if($debug) return DataObjectUtility::dataObjectToArray($item);
        return NestedMapper::map($item);
    }
}