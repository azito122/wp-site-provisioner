<?php

namespace WPSP\query;

use WPSP\Store as Store;
use WPSP\query\QueryParam as QueryParam;
use WPSP\query\response\QueryResponseMap as QueryResponseMap;

class Query {

    use \WPSP\traits\GetterSetter;

    protected $storeid;

    protected $remoteid;
    protected $extrapath;
    protected $params = array();
    protected $responsemap;

    protected $remote;
    protected $latest;
    protected $data;

    public function __sleep() {
        return array(
            'remoteid',
            'extrapath',
            'params',
            'responsemap',
        );
    }

    public function __wakeup() {
        global $Store;
        $this->remote = $Store->unstoreEntity( 'Remote', $this->remoteid );
    }

    public function __construct( $responsemap = null, $remoteid = null, $params = array() ) {
        $this->remoteid = $remoteid;
        $this->params = $params;
        $this->responsemap = $responsemap ? $responsemap : new QueryResponseMap();
    }

    public function run( $data = array() ) {
        $this->data = $data;

        $url = $this->url;
        $args = array(
            'timeout' => 5,
        );

        $params = $this->getParamsArray();
        $url = \add_query_arg( $params, $url );
        echo $url;

        $responsemap = wp_remote_get( $url, $args );
        $responsemap = $this->normalizeResponse( $responsemap );

        $this->latest = $responsemap;

        return $responsemap;
    }

    private function normalizeResponse( $response ) {
        $response = json_decode( $response[ 'body' ] );
        if ( ! $response || empty( $response ) || ! is_array( $response ) ) {
            return false;
        }

        $normalized = $this->responsemap->normalize( $response );

        return $normalized;
    }

    private function resolve( $string ) {
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

    private function getParamsArray() {
        $params = array();
        foreach ( $this->params as $param ) {
            $params[ $param->key ] = $this->resolve( $param->value );
            // array_merge( $params, $this->getParam( $id ) );
        }
        return $params;
    }

    private function findParam( $id ) {
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

    public function get_url() {
        return $this->remote->url . $this->extrapath;
    }

    public function set_remote( $remote ) {
        $this->remoteid = $remote->storeid;
        $this->remote = $remote;
    }
}