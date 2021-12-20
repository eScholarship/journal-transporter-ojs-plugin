<?php namespace CdlExportPlugin\Command;

use CdlExportPlugin\Command\Journals\Journal;
use CdlExportPlugin\Command\Traits\CommandHandler;
use CdlExportPlugin\Utility\Traits\DAOCache;
use DAORegistry;
use CdlExportPlugin\Utility\DataObjectUtility;

class Journals {
    use CommandHandler;
    use DAOCache;

    public function __construct($args) {
        $this->initializeHandler($args);
    }

    /**
     * List things
     */
    function execute() {
        $data = [];
        if(array_key_exists(0, $this->args)) {
            (new Journal($this->args))->execute();
        } else {
            $journalsResultSet = $this->getDAO('journal')->getJournals();
            foreach($journalsResultSet->toArray() as $journal) {
                $data[] = DataObjectUtility::dataObjectToArray($journal);
            }
            echo json_encode(['journals' => $data]);
        }
    }
}