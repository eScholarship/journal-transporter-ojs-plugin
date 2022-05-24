<?php namespace JournalTransporterPlugin\Utility;

use \DAORegistry;

class DAOFactory {
    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * @var array
     */
    protected $DAOInstances = [];

    /**
     * DAOFactory constructor.
     */
    protected function __construct() {
    }

    /**
     * @return \JournalTransporterPlugin\Utility\DAOFactory
     */
    public function get() {
        return self::$instance === null ? new self : self::instance;
    }

    /**
     * @param $daoName
     * @return mixed
     */
    public function getDAO($daoName) {
        $ucDaoName = ucwords($daoName);
        if(!array_key_exists($ucDaoName, $this->DAOInstances)) {
            $inheritedClassName = 'JournalTransporterPlugin\\DAO\\'.$ucDaoName;
            if(class_exists($inheritedClassName)) {
                $this->DAOInstances[$ucDaoName] = new $inheritedClassName;
            } else {
                // This function will fatal error if the DAO isn't registered. Fine.
                $daoInstance = DAORegistry::getDAO(ucwords($ucDaoName) . "DAO");
                $this->DAOInstances[$ucDaoName] = $daoInstance;
            }
        }
        return $this->DAOInstances[$ucDaoName];
    }
}