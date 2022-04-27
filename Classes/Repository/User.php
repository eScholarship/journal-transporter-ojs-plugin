<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class User
{
    public function fetchById($id)
    {
        return DAOFactory::get()->getDAO('user')->getUser($id);
    }
}
