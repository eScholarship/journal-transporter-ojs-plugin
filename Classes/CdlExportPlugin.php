<?php

/**
 * @file CdlExportPlugin.php
 *
 * Copyright (c) 2021 Cast Iron Coding
 * Distributed under the GNU GPL v2.
 *
 * @class CdlExportPlugin
 * @ingroup plugins_importexport_cdlexport
 *
 * @brief Export plugin for CDL's Journal Portability project
 */

// $Id$


import('classes.plugins.ImportExportPlugin');

class CdlExportPlugin extends ImportExportPlugin {
    const PLUGIN_DISPLAY_NAME = 'CDL OJS Export Plugin';
    const PLUGIN_DESCRIPTION = "Export plugin for CDL's Journal Portability project";

    /**
     * Called as a plugin is registered to the registry
     * @param $category String Name of category plugin was registered to
     * @return boolean True if plugin initialized successfully; if false,
     * 	the plugin will not be registered.
     */
    function register($category, $path) {
        return parent::register($category, $path);
    }

    /**
     * Get the name of this plugin. The name must be unique within
     * its category.
     * @return String name of plugin
     */
    function getName() {
        return __CLASS__;
    }

    /**
     * @return string
     */
    function getDisplayName() {
        return self::PLUGIN_DISPLAY_NAME;
    }

    /**
     * @return string
     */
    function getDescription() {
        return self::PLUGIN_DESCRIPTION;
    }

    function display(&$args) {
        die("Code in ".__CLASS__."::".__METHOD__." borrowed from sample importexport plugin. Aborting!");
        parent::display($args);
        switch (array_shift($args)) {
            case 'exportIssue':
                // The actual issue export code would go here
                break;
            default:
                // Display a list of issues for export
                $journal =& Request::getJournal();
                $issueDao =& DAORegistry::getDAO('IssueDAO');
                $issues =& $issueDao->getIssues($journal->getId(), Handler::getRangeInfo('issues'));

                $templateMgr =& TemplateManager::getManager();
                $templateMgr->assign_by_ref('issues', $issues);
                $templateMgr->display($this->getTemplatePath() . 'issues.tpl');
        }
    }

    /**
     * Execute import/export tasks using the command-line interface.
     * @param $args Parameters to the plugin
     */
    function executeCLI($scriptName, &$args) {
        $this->default($scriptName, $args);
    }

    /**
     * List things
     */
    function default($scriptName, $args) {
        $journalDao = DAORegistry::getDAO('JournalDAO');
        $data = [];
        if(array_key_exists(0, $args)) {
            $journalPath = $args[0];
        } else {
            $journalsResultSet = $journalDao->getJournals();
            while(!$journalsResultSet->eof()) {
                $journal = $journalsResultSet->next();
                $data[] = ['title' => $journal->getLocalizedTitle(), 'path' => $journal->getPath()];
            }
            echo json_encode(['journals' => $data]);
            die();
        }


        $issueDao = DAORegistry::getDAO('IssueDAO');
        $sectionsDao = DAORegistry::getDAO('SectionDAO');

        $journal = $journalDao->getJournalByPath($journalPath);
        if(is_null($journal)) {
            echo "Could not find a journal with path $journalPath".PHP_EOL;
            die();
        }

        $data['title'] = $journal->getLocalizedTitle();

        $issuesResultSet = $issueDao->getIssues($journal->getId());
        $issues = [];
        while(!$issuesResultSet->eof()) {
            $issue = $issuesResultSet->next();
            $numberOfArticles = $issueDao->getNumArticles($issue->getId());
            $dataIssue = [
                'title' => $issue->getLocalizedTitle(),
                'numberOfArticles' => $numberOfArticles,
                'published' => $issue->getPublished()
            ];
            $issues[] = $dataIssue;
        }
        $data['issues'] = $issues;

        $sectionsResultSet = $sectionsDao->getJournalSections($journal->getId());
        $sections = [];
        while(!$sectionsResultSet->eof()) {
            $section = $sectionsResultSet->next();
            $dataSection = ['title' => $section->getLocalizedTitle()];
            $sections = $dataSection;
        }

        $data['sections'] = $sections;

        echo json_encode($data);
    }
}

