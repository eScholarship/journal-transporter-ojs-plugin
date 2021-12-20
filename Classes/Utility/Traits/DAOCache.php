<?php namespace CdlExportPlugin\Utility\Traits;

use \DAORegistry;

trait DAOCache {
    private $DAOInstances = [];

    /**
     * @param $daoName
     * @return mixed
     */
    public function getDAO($daoName) {
        if(!array_key_exists($daoName, $this->DAOInstances)) {
            // This function will fatal error if the DAO isn't registered. Fine.
            $daoInstance = DAORegistry::getDAO(ucwords($daoName) . "DAO");
            $this->DAOInstances[$daoName] = $daoInstance;
        }

        return $this->DAOInstances[$daoName];
    }
}