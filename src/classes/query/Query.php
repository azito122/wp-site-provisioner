<?php

namespace WPSP\query;

use WPSP\Store as Store;
use WPSP\query\params\QueryParams as QueryParams;
use WPSP\query\response\QueryResponse as QueryResponse;

class Query {

    use \WPSP\traits\GetterSetter;
    use \WPSP\traits\Storable;

    protected $label;
    protected $remoteid;
    protected $extrapath;
    protected $params;
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

    public function __construct( $label, $responsemap = null, $remoteid = null, $params = null ) {
        $this->label = $label;
        $this->remoteid = $remoteid;
        $this->params = $params ? $params : new QueryParams();
        $this->responsemap = $responsemap ? $responsemap : new QueryResponse();
    }

    public function run( $data = array() ) {
        $this->data = $data;

        $url = $this->url;
        $args = array(
            'timeout' => 5,
        );

        $params = $this->getParamsArray();
        $url = \add_query_arg( $params, $url );

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

    public function resolve( $string ) {
        $matches = array();
        if ( preg_match_all( '/\{(.*?)\}/', $string, $matches ) ) {
            foreach ( $matches[1] as $match ) {
                if ( array_key_exists( $match, $this->data ) ) {
                    $string = preg_replace( "/\{$match\}/", $this->data[ $match ], $string );
                }
            }
        }

        return $string;
    }

    public function getParamsArray() {
        $params = $this->params->getArray();
        foreach ( $params as $key => $value ) {
            $params[ $key ] = $this->resolve( $value );
        }
        return $params;
    }

    // private function findParam( $id ) {
    //     if ( ! array_key_exists( $id, $this->params ) ) {
    //         return;
    //     }

    //     return array( $id => $this->resolve( $this->params[ $id ] ) );
    // }

    // public function addParam( $param = null ) {
    //     if ( ! $param instanceof QueryParam ) {
    //         $param = new QueryParam();
    //     }
    //     $this->params[] = $param;
    //     return $param;
    // }

    // public function deleteParam( QueryParam $param ) {
    //     unset( $this->params[ array_search( $param, $this->params ) ] );
    // }

    public function get_url() {
        return $this->remote->url . $this->extrapath;
    }

    public function set_remote( $remote ) {
        $this->remoteid = $remote->uid;
        $this->remote = $remote;
    }
}