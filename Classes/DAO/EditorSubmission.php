<?php namespace JournalTransporterPlugin\DAO;

import('classes.submission.editor.EditorSubmissionDAO');
import('classes.submission.editor.EditorSubmission');

class EditorSubmission extends \EditorSubmissionDAO {
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
        return new \EditorSubmission;
    }

    /**
     * Get the editor decisions for a review round of an article. This one includes the round.
     * @param $articleId int
     * @param $round int
     */
    function getEditorDecisions($articleId, $round = null) {
        $decisions = array();

        if ($round == null) {
            $result =& $this->retrieve(
                'SELECT edit_decision_id, editor_id, round, decision, date_decided FROM edit_decisions WHERE article_id = ? ORDER BY edit_decision_id ASC', $articleId
            );
        } else {
            $result =& $this->retrieve(
                'SELECT edit_decision_id, editor_id, round, decision, date_decided FROM edit_decisions WHERE article_id = ? AND round = ? ORDER BY edit_decision_id ASC',
                array($articleId, $round)
            );
        }

        while (!$result->EOF) {
            $decisions[] = array('editDecisionId' => $result->fields[0], 'editorId' => $result->fields[1], 'round' => $result->fields[2], 'decision' => $result->fields[3], 'dateDecided' => $this->datetimeFromDB($result->fields[4]));
            $result->moveNext();
        }
        $result->Close();
        unset($result);

        return $decisions;
    }
}
