<?php

namespace WPSP;

class Query {

    private $remote;
    private $params;
    // private $properties;
    private $latest;

    public function __construct__(Remote $remote, $params) {
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
        $args = array_merge($args, $this->params);
        $result = wp_remote_get( $url, $args );
        $this->latest = $result;
        return $result;
    }

    // public function addProperty( $key, $datapath ) {
    //     if ( ! array_key_exists( $key, $this->properties ) ) {
    //         $this->properties[ $key ] = $datapath;
    //         return true;
    //     }
    //     return false;
    // }

    public function addParam( $key, $value ) {
        if ( ! array_key_exists( $key, $this->params ) ) {
            $this->params[ $key ] = $value;
            return true;
        }
        return false;
    }

    public function setParam( $key, $value ) {
        if ( array_key_exists( $key, $this->params ) ) {
            $this->params[ $key ] = $value;
            return true;
        }
        return false;
    }

    // public function getData( $datapath ) {
    //     if ( array_key_exists( $datapath, $this->properties ) ) {
    //         return $this->resolveDataPath( $this->properties[ $datapath ] );
    //     }
    //     return $this->resolveDataPath( $datapath );
    // }

    // public function resolveDataPath( $datapath ) {
    //     return;
    // }
}