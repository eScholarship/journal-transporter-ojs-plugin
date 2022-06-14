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
                return Str::camelToSnake(Role::getRoleName($role->getRoleId()));
            }, $this->roleRepository->fetchByUserAndJournal($user, $journal));

            return array_map(function($role) use($user, $journal) {
                return (object)[
                    'source_record_key' => 'Role:'.hexdec(substr(sha1($journal->getId().':'.$user->getId().':'.$role), 0, 12)),
                    'user' => NestedMapper::map($user, 'sourceRecordKey'),
                    'role' => $role
                ];
            }, $roles);

        }, $users);
        return array_merge(...$roles);
    }
}