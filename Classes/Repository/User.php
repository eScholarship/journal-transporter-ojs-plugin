<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\DAOFactory;

class User
{
    public function fetchById($id)
    {
        return DAOFactory::get()->getDAO('user')->getUser($id);
    }
}
