<?php namespace JournalTransporterPlugin\Api\Journals;

use JournalTransporterPlugin\Api\ApiRoute;
use JournalTransporterPlugin\Builder\Mapper\NestedMapper;
use JournalTransporterPlugin\Utility\DataObject;
use JournalTransporterPlugin\Utility\Enums\Role;
use JournalTransporterPlugin\Utility\Str;

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
        $users = $this->roleRepository->fetchUsersByJournal($journal)->toArray();

        if($arguments[ApiRoute::DEBUG_ARGUMENT]) return $users;

        $roles = array_map(function($user) use($journal) {
            $roles = array_map(function($role) {
                return (object)[
                    'label' => Str::camelToSnake(Role::getRoleName($role->getRoleId())),
                    'id' => $role->getRoleId(),
                ];
            }, $this->roleRepository->fetchByUserAndJournal($user, $journal));

            return array_map(function($role) use($user, $journal) {
                return (object)[
                    // Role records don't have an id, so we fabricate a SRK like this
                    'source_record_key' => 'JournalUserRole:'.$journal->getId().':'.$user->getId().':'.$role->id,
                    'user' => NestedMapper::map($user, 'sourceRecordKey'),
                    'role' => $role->label
                ];
            }, $roles);

        }, $users);
        return array_merge(...$roles);
    }
}