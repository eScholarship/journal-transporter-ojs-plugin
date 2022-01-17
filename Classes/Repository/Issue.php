<?php namespace CdlExportPlugin\Repository;

use CdlExportPlugin\Utility\Traits\DAOCache;

class Issue {
    use DAOCache;

    public function fetchByJournal($journal)
    {
        return $this->getDAO('issue')->getIssues($journal->getId());
    }

}
