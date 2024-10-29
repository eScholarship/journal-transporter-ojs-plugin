<?php namespace JournalTransporterPlugin\DAO;

import('classes.note.NoteDAO');
import('classes.note.Note');

class Note extends \NoteDAO {
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
        return new \Note;
    }

    /**
     * Retrieve Notes by assoc id/type
     * @param $assocId int
     * @param $assocType int
     * @param $userId int
     * @return object DAOResultFactory containing matching Note objects
     */
    function getByAssoc($assocType, $assocId, $userId = null) {
        $params = array((int) $assocId, (int) $assocType);
        if (isset($userId)) $params[] = (int) $userId;

        $sql = 'SELECT * FROM notes WHERE assoc_id = ? AND assoc_type = ?';
        if (isset($userId)) {
            $sql .= ' AND user_id = ?';
        }
        $sql .= ' ORDER BY date_created DESC';

        $result =& $this->retrieveRange($sql, $params);

        return new \DAOResultFactory($result, $this, '_returnNoteFromRow');
    }
}
