<?php namespace CdlExportPlugin\Api;

use CdlExportPlugin\Utility\RegexUtility;
use CdlExportPlugin\Api\Journals\Sections;
use CdlExportPlugin\Api\Journals\Issues;
use CdlExportPlugin\Api\Journals\Roles;
use CdlExportPlugin\Api\Users;
use CdlExportPlugin\Api\Journals\Articles;
use CdlExportPlugin\Api\Journals\Articles\Digest\Emails;
use CdlExportPlugin\Api\Journals\Articles\Log;
use CdlExportPlugin\Api\Journals\Articles\Synthetics\History;

class Controller {

    private $args = [];

    private $routes = [
        // Don't use a ~ character in these route regexes, unless you escape them, kew? We're using them
        // as the delimiter. Note we're using named parameters too.
        '^/journals(/(?P<journal>\d+))?$' => Journals::class,
        '^/journals/(?P<journal>\d+)/sections(/(?P<section>\d+))?$' => Sections::class,
        '^/journals/(?P<journal>\d+)/issues(/(?P<issue>\d+))?$' => Issues::class,
        '^/journals/(?P<journal>\d+)/roles' => Roles::class,
        '^/journals/(?P<journal>\d+)/articles(/(?P<article>\d+))?$' => Articles::class,
        '^/journals/(?P<journal>\d+)/articles/(?P<article>\d+)/digest/emails(\.(?P<format>[a-z]+))?$' => Emails::class,
        '^/journals/(?P<journal>\d+)/articles/(?P<article>\d+)/digest/log' => Log::class,
        '^/journals/(?P<journal>\d+)/articles/(?P<article>\d+)/synthetics/history' => History::class,
        '^/users(/(?P<user>\d+))$' => Users::class
    ];

    /**
     * Api constructor.
     * @param $args
     */
    public function __construct($args) {
        $this->args = $args;
    }

    /**
     *
     */
    public function execute() {
        $skipRouteLookup = false;
        if(is_null($this->args[0])) {
            $out = ['Exception' => 'No route provided', 'routeRegexes' => array_keys($this->routes)];
            $skipRouteLookup = true;
        } else {
            $out = ['Exception' => ["Provided route '".$this->args[0]."' did not match defined routes"]]; // Fallback response
        }

        if(!$skipRouteLookup) {
            list($head, $tail) = explode('?', $this->args[0], 2);
            foreach ($this->routes as $route => $class) {
                $matches = [];
                if (preg_match('~' . $route . '~', $head, $matches)) {
                    $out = $this->callRouteHandler($route, $class, $matches, $this->parseArguments($tail));
                    break;
                }
            }
        }

        return $out;
    }

    /**
     * Call API method, catch exceptions
     * @param $route
     * @param $class
     * @param $routeParameters
     * @param array $arguments
     * @return array
     */
    private function callRouteHandler($route, $class, $routeParameters, $arguments = []) {
        $parameters = $this->zipArgs($routeParameters, RegexUtility::getRegexNamedMatches($route));

        try {
            $out = (new $class($routeParameters))->execute($parameters, $arguments);
        } catch(\Exception $e) {
            // TODO: this could be make more robust exception handling
            $out = ['Exception' => $e->getMessage()];
        }
        return $out;
    }

    /**
     * Given an array of matches and an array of allowed params, return just the params from the matches that are
     * allowed
     * @param $rawArgs
     * @param $allowedParameters
     * @return array
     */
    private function zipArgs($rawArgs, $allowedParameters) {
        $out = [];
        foreach($allowedParameters as $allowedParameter) {
            $out[$allowedParameter] = @$rawArgs[$allowedParameter];
        }
        return $out;
    }

    /**
     * This is pretty naive, but also good enough for our purposes.
     * Turns ?k1=v1&k2=v2 into key value pairs.
     * @param $argumentsString
     * @return array
     */
    private function parseArguments($argumentsString) {
        $decodedArgumentsString = urldecode($argumentsString);

        $arguments = [];
        if(strlen($decodedArgumentsString) > 0) {
            $pairs = explode('&', $decodedArgumentsString);
            foreach ($pairs as $pair) {
                list($key, $value) = explode('=', $pair);
                if (substr($key, -2) == '[]') {
                    if (is_array($arguments[$key])) {
                        $arguments[$key][] = $value;
                    } else {
                        $arguments[$key] = [$value];
                    }
                } else {
                    $arguments[$key] = is_null($value) ?: $value;
                }
            }
        }
        return $arguments;
    }
}