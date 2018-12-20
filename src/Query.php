<?php

namespace WPSP;

include_once(__DIR__ . '/Remote.php');
include_once(__DIR__ . '/QueryParam.php');

class Query {

    private $remote;
    private $params;
    private $latest;

    public function __construct(Remote $remote = null, $params = null ) {
        $this->remote = isset( $remote ) ? $remote : new Remote();
        $this->params = $params;
    }

    public function getRemote() {
        return $this->remote;
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

    public function addParam( $param = null ) {
        if ( ! $param instanceof QueryParam ) {
            $param = new QueryParam();
        }
        $this->params[] = $param;
        return $param;
    }

    public function deleteParam( QueryParam $param ) {
        unset( $this->params[ array_search( $param, $this->params ) ] );
    }
}