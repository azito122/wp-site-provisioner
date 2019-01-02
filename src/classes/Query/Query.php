<?php

namespace WPSP;

include_once(__DIR__ . '/../Remote.php');
include_once(__DIR__ . '/QueryParam.php');
include_once(__DIR__ . '/../infrastructure/Store.php');

class Query {

    public $storeid;

    private $label;
    private $remoteid;
    private $params;

    private $remote;
    private $latest;
    private $data;

    public function __sleep() {
        return array(
            'label',
            'remoteid',
            'params',
        );
    }

    public function __wakeup() {
        global $Store;
        $this->remote = $Store->unstore( 'Remote', $this->remoteid );
    }

    public function __construct( $remoteid = null, $params = null ) {
        $this->remoteid = $remoteid;
        $this->params = $params;
    }

    public function run( $data = array() ) {
        $this->data = $data;

        $url = $this->getRemote()->getFullUrl();
        $args = array(
            'timeout' => 5,
        );
        $args = array_merge( $args, $this->getParams() );
        $result = wp_remote_get( $url, $args );
        $this->latest = $result;
        return $result;
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

    public function getLabel() {
        return $this->label;
    }

    public function setLabel( $label ) {
        $this->label = is_string( $label ) ? $label : $this->label;
    }

    public function getRemoteId() {
        return $this->remoteid;
    }

    public function getRemote() {
        if ( $this->remote ) {
            return $this->remote;
        }
        $remote = Store::getEntity( 'Remote', $this->remoteid );
        if ( $remote ) {
            $this->remote = $remote;
        }
        return $this->remote;
    }

    public function setRemote( $remote ) {
        if ( is_numeric( $remote ) ) {
            $this->remoteid = (int)$remote;
        } else if ( $remote instanceof Remote ) {
            $this->remoteid = $remote->getId();
        }
        $this->getRemote();
    }

    public function getParams() {
        $params = array();
        foreach ( $this->params as $id => $val ) {
            array_merge( $params, $this->getParam( $id ) );
        }
        return $params;
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
}