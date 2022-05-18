<?php namespace JournalTransporterPlugin\Utility;

class SourceRecordKeyUtility
{
    /**
     * @param $id
     * @return string
     */
    static public function editor($id) {
        return \User::class.':'.$id;
    }

    /**
     * @param $id
     * @return string
     */
    static public function issue($id) {
        return \Issue::class.':'.$id;
    }

    /**
     * @param $id
     * @return string
     */
    static public function section($id) {
        return \Section::class.':'.$id;
    }

}