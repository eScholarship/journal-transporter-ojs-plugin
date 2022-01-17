<?php namespace CdlExportPlugin\Api\Journals;

use CdlExportPlugin\Repository\Journal;
use CdlExportPlugin\Repository\Section;

class Sections {
    private $journalRepository;
    private $sectionRepository;

    public function __construct()
    {
        $this->journalRepository = new Journal;
        $this->sectionRepository = new Section;
    }

    /**
     * @param $args
     * @return array|array[]|mixed|\stdClass|\stdClass[]
     * @throws \Exception
     */
    public function execute($args)
    {
        $journal = $this->journalRepository->fetchOneById($args['journal']);
        $sectionsResultSet = $this->sectionRepository->fetchByJournal($journal);

        $sections = [];
        foreach ($sectionsResultSet->toArray() as $section) {
            $sections[] = [
                'title' => $section->getLocalizedTitle(),
                'abbreviation' => $section->getLocalizedAbbrev(),
                'id' => $section->getId()
            ];
        }

        return $sections;
    }
}