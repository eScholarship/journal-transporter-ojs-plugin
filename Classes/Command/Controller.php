<?php namespace CdlExportPlugin\Command;

use CdlExportPlugin\Command\Traits\Handler;

class Controller {
    use Handler;

    private $allowedCommands = [
      'journals' => Journals::class
    ];

    /**
     *
     */
    function usage() {
        echo "Examples of usage.".PHP_EOL;
    }

}