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

import('lib.pkp.classes.plugins.GenericPlugin');

use CdlExportPlugin\Command\Controller;

class CdlExportPlugin extends GenericPlugin {
    const PLUGIN_DISPLAY_NAME = 'CDL OJS Export Plugin';
    const PLUGIN_DESCRIPTION = "Export plugin for CDL's Journal Portability project";

    /**
     * Called as a plugin is registered to the registry
     * @param $category String Name of category plugin was registered to
     * @return boolean True if plugin initialized successfully; if false,
     * 	the plugin will not be registered.
     */
    function register($category, $path) {
        $this->registerAutoload();

        if(parent::register($category, $path)) {
            if(Config::getVar('cdlexport', 'enable_plugin_endpoints') && $this->requestIsAuthorized()) {
                $this->registerLoadHandlerHook();
            }
        }
    }

    /**
     * Check to see if the current request is authorized
     * @return bool
     */
    protected function requestIsAuthorized() {
        // No authentication of any kind required, don't need to check
        if(!Config::getVar('cdlexport', 'require_basic_auth') &&
            !Config::getVar('cdlexport', 'require_site_admin')
        ) return true;

        // If basic auth is required and fails, no access for you!
        if(Config::getVar('cdlexport', 'require_basic_auth')) {
            // Can't enable basic auth with an empty username or password
            if(!(strlen($_SERVER['PHP_AUTH_USER']) > 0) || !(strlen($_SERVER['PHP_AUTH_PW']) > 0)) return false;

            if(Config::getVar('cdlexport', 'basic_auth_user') !== $_SERVER['PHP_AUTH_USER'] ||
                Config::getVar('cdlexport', 'basic_auth_password') !== $_SERVER['PHP_AUTH_PW']) return false;
        }


        // If site admin is required and this isn't a site admin, no access for you!
        if(Config::getVar('cdlexport', 'require_site_admin') && !Validation::isSiteAdmin()) {
            return false;
        }

        return true;
    }

    /**
     *
     */
    protected function registerLoadHandlerHook() {
        HookRegistry::register('LoadHandler', function ($hookname, $params) {
            if ($params[0] == 'cdlexport') {
                define('HANDLER_CLASS', 'CdlExportHandler');
                define('CDL_EXPORT_PLUGIN_NAME', $this->getName());
                $handlerFile =& $params[2];
                $handlerFile = $this->getPluginPath() . '/Classes/' . 'CdlExportHandler.php';
            }
        });
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
     *
     */
    function registerAutoload() {
        spl_autoload_register(function($class) {
            $namespace = 'CdlExportPlugin\\';
            if(strpos($class, $namespace) === 0) {
                $file = __DIR__.'/'.str_replace('\\', '/', substr($class, strlen($namespace))).'.php';
                if(file_exists($file)) {
                    include $file;
                }
            }
        });
    }
}

