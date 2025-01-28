<?php namespace JournalTransporterPlugin\Builder\Mapper\DataObject;

use JournalTransporterPlugin\Utility\DAOFactory;
use Config;
use JournalTransporterPlugin\Utility\Files;
use JournalTransporterPlugin\Utility\Str;

class Journal extends AbstractDataObjectMapper {
    protected static $contexts = ['list' => ['exclude' => '*', 'include' => ['sourceRecordKey', 'title', 'path']]];

    protected static $mapping = [
        ['property' => 'sourceRecordKey', 'source' => 'id'],
        ['property' => 'path'],
        ['property' => 'title', 'source' => 'localizedTitle'],
        ['property' => 'abbreviation', 'source' => 'journalInitials'],
        ['property' => 'description', 'source' => 'localizedDescription', 'filters' => ['html']],
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
        ['property' => 'copyrightNotice', 'source' => 'settings.copyrightNotice', 'filters' => ['first']],
        ['property' => 'headerFile'],
        ['property' => 'logoFile']
    ];

    /**
     * @param $dataObject
     * @param $context
     * @return mixed
     */
    protected static function preMap($dataObject, $context) {
        $dataObject->headerFile = self::getImage($dataObject, 'pageHeaderTitleImage');
        $dataObject->logoFile  = self::getImage($dataObject, 'pageHeaderLogoImage');
        return $dataObject;
    }

    /**
     *
     * @param $dataObject
     * @param $settingKey
     * @return mixed|null
     */
    protected static function getImage($dataObject, $settingKey) {
        $settings = @$dataObject->getSettings()[$settingKey]['en_US'];
        if($settings){
            $imageData = array_combine(array_map([Str::class, 'camelToSnake'], array_keys($settings)),array_values($settings));
            if($imageData) {
                $imageUrl = Files::getPublicJournalUrl($dataObject) . '/' . $imageData['uploadName'];
                $imageData['url'] = $imageUrl . $imageData['upload_name'];
                return (object) $imageData;
            } else return null;
        } else return null;
    }
}