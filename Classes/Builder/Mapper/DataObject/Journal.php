<?php namespace CdlExportPlugin\Builder\Mapper\DataObject;

use CdlExportPlugin\Utility\DAOFactory;
use Config;

class Journal extends AbstractDataObjectMapper {
    protected static $contexts = ['list' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'title', 'path']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'path'],
        ['property' => 'title', 'source' => 'localizedTitle'],
        ['property' => 'abbreviation', 'source' => 'journalInitials'],
        ['property' => 'description', 'source' => 'localizedDescription'],
        ['property' => 'enabled', 'filters' => ['boolean']],
        ['property' => 'contactName', 'source' => 'settings.contactName'],
        ['property' => 'contactEmail', 'source' => 'settings.contactEmail'],
        ['property' => 'contactPhone', 'source' => 'settings.contactPhone'],
        ['property' => 'contactFax', 'source' => 'settings.contactFax'],
        ['property' => 'emailSignature', 'source' => 'settings.emailSignature'],
        ['property' => 'onlineIssn', 'source' => 'settings.onlineIssn'],
        ['property' => 'printIssn', 'source' => 'settings.printIssn'],
        ['property' => 'supportEmail', 'source' => 'settings.supportEmail'],
        ['property' => 'supportName', 'source' => 'settings.supportName'],
    ];

    /**
     * @param $data
     * @param $dataObject
     * @return mixed
     */
    protected static function postMap($data, $dataObject, $context) {
        if($context == 'list') return $data;

        //$data['header'] = self::getImage($dataObject, 'pageHeaderTitleImage');
        $data['logo'] = self::getImage($dataObject, 'pageHeaderLogoImage');

        //$data = array_merge($data, self::getCounts($dataObject));

        return $data;
    }

    /**
     *
     * @param $dataObject
     * @param $settingKey
     * @return mixed|null
     */
    protected static function getImage($dataObject, $settingKey) {
        $imageData = $dataObject->getSettings()[$settingKey]['en_US'];
        if($imageData) {
            $imageUrl =
                Config::getVar('general', 'base_url') .
                Config::getVar('files', 'public_files_dir') .
                '/journals/' . $dataObject->getId() . '/' . $imageData['uploadName'];
            $imageData['url'] = $imageUrl;
            return $imageData;
        } else return null;
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