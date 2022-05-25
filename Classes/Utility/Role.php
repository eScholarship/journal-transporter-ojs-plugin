<?php namespace JournalTransporterPlugin\Utility;

use JournalTransporterPlugin\Utility\DAOFactory;

class Role
{
    /**
     * Turn a role integer into a label
     * @param $route
     * @return string
     */
    static public function getRoleName($roleId) {
        return str_replace('user.role.', '', DAOFactory::get()->getDAO('role')->getRoleName($roleId));
    }
}