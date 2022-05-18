<?php namespace JournalTransporterPlugin\Repository;

use JournalTransporterPlugin\Utility\DAOFactory;

trait Repository {
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    protected function __call($name, $arguments) {
        if(!property_exists($this, 'DAO')) throw new \Exception('DAO method called without assigned DAO');
        if(!preg_match('/^[a-zA-Z]+$/', $this->DAO)) throw new \Exception('Invalid DAO name provided');

        return DAOFactory::get()->getDAO($this->DAO)->$name(...$arguments);
    }
}