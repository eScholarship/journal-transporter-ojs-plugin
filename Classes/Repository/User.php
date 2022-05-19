<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

class User {
    use Repository;

    /**
     * @var string
     */
    protected $DAO = 'user';

    /**
     * @param $id
     * @return mixed
     */
    public function fetchById($id)
    {
        return DAOFactory::get()->getDAO('user')->getUser($id);
    }
}
