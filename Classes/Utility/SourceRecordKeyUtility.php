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
    static public function reviewer($id) {
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

    /**
     * @param $id
     * @return string
     */
    static public function reviewForm($id) {
        return \ReviewForm::class.':'.$id;
    }

    /**
     * Not a real class in OJS
     * @param $articleId
     * @param $round
     * @return string
     */
    static public function round($articleId, $round) {
        return 'ReviewRound:'.$articleId.':'.$round;
    }
}