<?php namespace CdlExportPlugin\Command;

use CdlExportPlugin\Command\Traits\CommandHandler;

class Controller {
    use CommandHandler;

    private $allowedCommands = [
      'journals' => Journals::class,
      'api' => Api::class
    ];

    /**
     *
     */
    function usage() {
        echo "Examples of usage.".PHP_EOL;
    }

}