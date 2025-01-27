<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;
use JournalTransporterPlugin\Exception\UnknownDatabaseAccessObjectException;
use JournalTransporterPlugin\Exception\InvalidArgumentException;

trait Repository {
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws
     */
    public function __call($name, $arguments) {
        if(!property_exists($this, 'DAO')) throw new InvalidArgumentException('DAO method called without assigned DAO');
        if(!preg_match('/^[a-zA-Z]+$/', $this->DAO)) throw new UnknownDatabaseAccessObjectException('Invalid DAO name provided');

        return DAOFactory::get()->getDAO($this->DAO)->$name(...$arguments);
    }
}