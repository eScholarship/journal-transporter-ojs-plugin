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

use CdlExportPlugin\Utility\DataObjectUtility;
use CdlExportPlugin\Command\Controller;

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

    function display($args) {
        die("Code in ".__CLASS__."::".__METHOD__." doesn't do anything.");
    }

    /**
     * Execute import/export tasks using the command-line interface.
     * @param $args Parameters to the plugin
     */
    function executeCLI($scriptName, $args) {
        $this->includeAllClasses();
        error_reporting(E_ERROR); // Don't show warnings or notices, lots of rattles in the OJS engine
        $cliController = new Controller();
        $cliController->initializeHandler($args);
        $cliController->execute();
    }

    /**
     * There's no autoloading here. We'll just load all classes when the CLI is executed.
     */
    function includeAllClasses() {
        // TODO: make this all better.
        require_once('Command/Traits/CommandHandler.php');
        require_once('Utility/Traits/DAOCache.php');

        // Include all classes in the plugin
        $directoryIterator = new RecursiveDirectoryIterator(dirname(__FILE__));
        $iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            if($file->getExtension() === 'php') {
                require_once($file);
            }
        }
    }
}

