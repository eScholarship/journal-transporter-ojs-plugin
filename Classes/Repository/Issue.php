<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\DAOFactory;

class Issue {

    public function fetchByJournal($journal)
    {
        return DAOFactory::get()->getDAO('issue')->getIssues($journal->getId());
    }

    public function fetchByIdAndJournal($id, $journal)
    {
        return DAOFactory::get()->getDAO('issue')->getIssueById($id, $journal->getId());
    }

}
