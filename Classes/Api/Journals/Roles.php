<?php namespace JournalTransporterPlugin\Api\Journals;

use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Utility\DataObject;
use JournalTransporterPlugin\Utility\Role;

class Roles extends ApiRoute  {
    protected $journalRepository;
    protected $roleRepository;

    /**
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function execute($parameters, $arguments)
    {
        $journal = $this->journalRepository->fetchOneById($parameters['journal']);
        $resultSet = $this->roleRepository->fetchByJournal($journal);

        if($arguments[ApiRoute::DEBUG_ARGUMENT]) return DataObject::resultSetToArray($resultSet);

        return array_map(function($item) use($journal) {
            $roles = array_map(function($role) {
                return Role::getRoleName($role->getRoleId());
            }, $this->roleRepository->fetchByUserAndJournal($item, $journal));

            $user = NestedMapper::map($item, 'roles');
            $user['roles'] = $roles;

            return $user;
        }, $resultSet->toArray());
    }
}