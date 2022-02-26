<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

use CdlExportPlugin\Utility\DAOFactory;
use Config;

class Journal extends AbstractDataObjectMapper {
    protected static $contexts = ['list' => ['exclude' => '*', 'include' => ['id', 'title']]];

    protected static $mapping = <<<EOF
		                   id
		                   path
		 localizedTitle -> title
		journalInitials -> initials
EOF;

    /**
     * @param $data
     * @param $dataObject
     * @return mixed
     */
    protected static function postMap($data, $dataObject, $context) {
        if($context == 'list') return $data;

        $logoData = $dataObject->getSettings()['pageHeaderTitleImage']['en_US'];
        if($logoData) {
            $logoUrl =
                Config::getVar('general', 'base_url') .
                Config::getVar('files', 'public_files_dir') .
                '/journals/' . $dataObject->getId() . '/' . $logoData['uploadName'];
            $logoData['url'] = $logoUrl;
            $data['logo'] = $logoData;
        } else $data['logo'] = null;

        //$data = array_merge($data, self::getCounts($dataObject));

        return $data;
    }

    /**
     * TODO: implement this if useful
     * @param $journal
     * @return int[]
     */
    protected static function getCounts($journal)
    {
        return [
            'articleCount' => 0,
            'sectionCount' => 0,
            'issueCount' => 0,
        ];
        // try using ->RecordCount() on a DAO
    }

}