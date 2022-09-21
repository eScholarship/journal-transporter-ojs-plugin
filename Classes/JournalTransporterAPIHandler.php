<?php

import('classes.handler.Handler');

class JournalTransporterAPIHandler extends Handler {
    /**
     *
     */
    public function api() {
        $this->handleRequest(\JournalTransporterPlugin\Api\Controller::class);
    }

    /**
     * @param $class
     */
    protected function handleRequest($class) {
        $requestUri = preg_replace('/^.*\/index\.php\/pages\/jt\/api(.*)$/', '$1', $_SERVER['REQUEST_URI']);

        $api = new $class([$requestUri]);
        $response = $api->execute();

        $payload = $response->getPayload();

        header('Content-Type: '.$response->getContentType());
        http_response_code($response->getResponseCode());

        echo $response->getContentType() == 'text/json' ? json_encode($payload) : $payload;
    }
}