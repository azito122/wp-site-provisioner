<?php

namespace WPSP;

include_once(__DIR__ . '/Remote.php');
include_once(__DIR__ . '/QueryParam.php');
include_once(__DIR__ . '/infrastructure/Store.php');

class Query {

    private $remoteid;
    private $remote;
    private $params;
    private $latest;
    private $label;

    public function __sleep() {
        return array(
            'remoteid',
            'params',
            'label'
        );
    }

    public function __construct( $remoteid = null, $params = null ) {
        $this->remoteid = $remoteid;
        $this->params = $params;
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

    public function run() {
        $url = $this->getRemote()->getFullUrl();
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