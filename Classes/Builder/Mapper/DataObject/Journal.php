<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

use Config;

class Journal extends AbstractDataObjectMapper {
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
    protected static function postMap($data, $dataObject) {
        $logoData = $dataObject->getSettings()['pageHeaderTitleImage']['en_US'];
        if($logoData) {
            $logoUrl =
                Config::getVar('general', 'base_url') .
                Config::getVar('files', 'public_files_dir') .
                '/journals/' . $dataObject->getId() . '/' . $logoData['uploadName'];
            $logoData['url'] = $logoUrl;
            $data['logo'] = $logoData;
        } else $data['logo'] = null;
        return $data;
    }
}