<?php

namespace WPSP\query\params;

class QueryParam {

    use \WPSP\traits\GetterSetter;

    protected $key;
    protected $value;

    public function __construct( $key = null, $value = null ) {
        $this->key = $key;
        $this->value = $value;
    }

    public function setFull( $key, $value ) {
        $this->key = $key;
        $this->value = $value;
    }

    public function getFull() {
        return array( $this->key => $this->value );
    }
}