<?php

namespace WPSP;

class QueryParam {

    private $key;
    private $value;

    public function __construct__( $key = null, $value = null ) {
        $this->key = $key;
        $this->value = $value;
    }

    public function setKey( $key ) {
        $this->key = $key;
    }

    public function setValue( $value ) {
        $this->value = $value;
    }

    public function getFull() {
        return array( $this->key => $this->value );
    }
}