<?php

namespace WPSP\query;

use WPSP\Store as Store;
use WPSP\query\QueryParam as QueryParam;

class Query {

    public $storeid;

    private $remoteid;
    private $extrapath;
    private $params = array();
    private $response;

    private $remote;
    private $latest;
    private $data;

    public function __sleep() {
        return array(
            'remoteid',
            'extrapath',
            'params',
        );
    }

    public function __wakeup() {
        global $Store;
        $this->remote = $Store->unstoreEntity( 'Remote', $this->remoteid );
    }

    public function __construct( $response = null, $remoteid = null, $params = array() ) {
        $this->remoteid = $remoteid;
        $this->params = $params;
        $this->response = $response;
    }

    public function run( $data = array() ) {
        $this->data = $data;

        $url = $this->getUrl();
        $args = array(
            'timeout' => 5,
        );
        $args = array_merge( $args, $this->getParamsArray() );

        $response = wp_remote_get( $url, $args );
        $response = $this->normalizeResponse( $response );

        $this->latest = $response;

        return $response;
    }

    public function normalizeResponse( $response ) {
        $response = json_decode( $response[ 'body' ] );
        if ( ! $response || empty( $response ) || ! is_array( $response ) ) {
            return false;
        }

        $normalized = $this->getResponse()->normalize( $response );

        return $normalized;
    }

    public function resolve( $string ) {
        $matches = array();
        if ( preg_match( '/.*\{(.*?)\}.*/', $string, $matches ) ) {
            foreach ( $matches as $match ) {
                if ( array_key_exists( $match, $this->data ) ) {
                    $string = preg_replace( "/\{$match\}/", $this->data[ $match ], $string );
                }
            }
        }

        return $string;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getLabel() {
        return $this->label;
    }

    public function setLabel( $label ) {
        $this->label = is_string( $label ) ? $label : $this->label;
        $this->label = $label;
    }

    public function getRemoteId() {
        return $this->remoteid;
    }

    public function setRemoteId( $id ) {
        $this->remoteid = $id;
    }

    public function getRemote() {
        global $Store;

        if ( $this->remote ) {
            return $this->remote;
        }
        $remote = $Store->unstoreEntity( 'Remote', $this->remoteid );
        if ( $remote ) {
            $this->remote = $remote;
        }
        return $this->remote;
    }

    public function setRemote( $remote ) {
        $this->setRemoteId( $remote->storeid );
        $this->remote = $remote;
    }

    public function getExtraPath() {
        return $this->extrapath;
    }

    public function setExtraPath( $path ) {
        $this->extrapath = $path;
    }

    public function getUrl() {
        return $this->remote->getUrl() . $this->getExtraPath();
    }

    public function getParamsArray() {
        $params = array();
        foreach ( $this->params as $param ) {
            $params[ $param->getKey() ] = $this->resolve( $param->getValue() );
            // array_merge( $params, $this->getParam( $id ) );
        }
        return $params;
    }

    public function getParams() {
        return $this->params;
    }

    public function getParam( $id ) {
        if ( ! array_key_exists( $id, $this->params ) ) {
            return;
        }

        return array( $id => $this->resolve( $this->params[ $id ] ) );
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

    public function setParams( $params ) {
        $this->params = $params;
    }
}