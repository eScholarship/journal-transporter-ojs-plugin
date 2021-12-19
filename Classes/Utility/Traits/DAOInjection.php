<?php namespace CdlExportPlugin\Utility\Traits;

use \DAORegistry;

trait DAOInjection {
    private $DAOInstances = [];

    private $initialized = false;

    public function initializeDAOInjection() {
        if(!property_exists($this, 'DAOs')) {
            throw new \Exception('$DAOs property must be defined for classes using the DAOInjection trait. It should be an array of DAO names, ie ["journal", "issue"].');
        }
        foreach($this->DAOs as $DAO) {
            $this->DAOInstances[$DAO] = DAORegistry::getDAO(ucwords($DAO) . "DAO");
        }

        $this->initialized = true;
    }

    public function getDAO($DAO) {
        if(!$this->initialized) {
            throw new \Exception("DAOInjection is not initialized. Be sure to run `DAOInjection::inject() in your constructor.");
        }
        if(array_key_exists($DAO, $this->DAOInstances)) {
            return $this->DAOInstances[$DAO];
        }
        throw new \Exception("DAOInjection: DAO $DAO is not registered. Include it in your \$DAOs property.");
    }
}