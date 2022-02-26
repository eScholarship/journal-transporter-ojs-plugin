<?php namespace CdlExportPlugin\Api\Journals;

use CdlExportPlugin\Api\ApiRoute;
use CdlExportPlugin\Builder\Mapper\NestedMapper;
use CdlExportPlugin\Utility\DAOFactory;
use CdlExportPlugin\Utility\DataObjectUtility;

class Users extends ApiRoute  {
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

        if($arguments[ApiRoute::DEBUG_ARGUMENT]) return DataObjectUtility::resultSetToArray($resultSet);

        $roleDAO = DAOFactory::get()->getDAO('role');
        return array_map(function($item) use($journal, $roleDAO) {
            $roles = array_map(function($role) use($roleDAO) {
                return $roleDAO->getRoleName($role->_data['roleId']);
            }, $this->roleRepository->fetchByUserAndJournal($item, $journal));

            return (object) ['user' => NestedMapper::map($item), 'roles' => $roles];
        }, $resultSet->toArray());
    }
}