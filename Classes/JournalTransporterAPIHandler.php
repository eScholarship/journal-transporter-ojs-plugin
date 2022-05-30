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
        $requestUri = '/' . implode('/', array_slice(explode('/', $_SERVER['REQUEST_URI']), 5));
        $api = new $class([$requestUri]);
        $response = $api->execute();

        $payload = $response->getPayload();

        header('Content-Type: '.$response->getContentType());
        http_response_code($response->getResponseCode());

        echo $response->getContentType() == 'text/json' ? json_encode($payload) : $payload;
    }
}