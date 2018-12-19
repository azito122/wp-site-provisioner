<?php

namespace WPSP;

class Query {

    private $remote;
    private $params;
    private $latest;

    public function __construct(Remote $remote = null, $params = null ) {
        $this->remote = $remote;
        $this->params = $params;
    }

    public function setRemote(Remote $remote) {
        $this->remote = $remote;
    }

    public function run() {
        $url = $this->remote->getFullUrl();
        $args = array(
            'timeout' => 5,
        );
        $args = array_merge( $args, $this->getParams );
        $result = wp_remote_get( $url, $args );
        $this->latest = $result;
        return $result;
    }

    public function getParams() {
        return $this->params;
    }

    public function addParam( QueryParam $param ) {
        $this->params[] = $param;
    }

    public function deleteParam( QueryParam $param ) {
        unset( $this->params[ array_search( $param, $this->params ) ] );
    }
}