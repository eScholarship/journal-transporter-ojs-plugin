<?php
namespace JournalTransporterPlugin\Utility;

use Config;

class Files
{
    public static function getPublicJournalUrl($journal): string
    {
        $journalId = is_object($journal) ? $journal->getId() : (string) $journal;
        return Config::getVar('general', 'base_url') .
            Config::getVar('files', 'public_files_dir') .
            '/journals/' . $journalId;
    }
}