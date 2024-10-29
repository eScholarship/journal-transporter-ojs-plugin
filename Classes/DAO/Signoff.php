<?php namespace JournalTransporterPlugin\DAO;

import('lib.pkp.classes.signoff.Signoff');
import('lib.pkp.classes.signoff.SignoffDAO');

class Signoff extends \SignoffDAO {
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Construct a new data object corresponding to this DAO.
     * @return Note
     */
    function newDataObject() {
        return new \Signoff;
    }

    /**
     * Retrieve Signoff by assoc id/type, without the symbolic
     * @param $assocId int
     * @param $assocType int
     * @return object DAOResultFactory containing matching Signoff objects
     */
    function getByAssoc($assocType, $assocId) {
        // Ordering by id because that correlates with order of process
        $sql = 'SELECT * FROM signoffs WHERE assoc_type = ? AND assoc_id = ? ORDER BY signoff_id ASC';
        $params = array((int) $assocType, (int) $assocId);

        $result =& $this->retrieve($sql, $params);

        $returner = new \DAOResultFactory($result, $this, '_fromRow', array('id'));
        return $returner;
    }
}
