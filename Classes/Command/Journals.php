<?php namespace CdlExportPlugin\Command;

use CdlExportPlugin\Command\Journals\Journal;
use CdlExportPlugin\Command\Traits\Handler;
use CdlExportPlugin\Utility\Traits\DAOInjection;
use DAORegistry;
use CdlExportPlugin\Utility\DataObjectUtility;

class Journals {
    use Handler;
    use DAOInjection;

    private $DAOs = ['journal'];

    public function __construct($args) {
        $this->initializeHandler($args);
        $this->initializeDAOInjection();
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