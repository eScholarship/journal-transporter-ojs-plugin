<?php namespace JournalTransporterPlugin\Api;

use JournalTransporterPlugin\Utility\RegexUtility;

class Controller {

    private $args = [];

    private $routes = [];

    /**
     * Api constructor.
     * @param $args
     */
    public function __construct($args) {
        $this->args = $args;
        $this->routes = include('Routes.php');
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
            $out = ['Exception' =>
                [
                    "Provided route '".$this->args[0]."' did not match defined routes",
                    'routeRegexes' => array_keys($this->routes)
                ]
            ];
        }

        if(!$skipRouteLookup) {
            list($head, $tail) = explode('?', $this->args[0], 2);
            foreach ($this->routes as $route => $class) {
                $matches = [];
                if (preg_match('~^' . $route . '$~', $head, $matches)) {
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