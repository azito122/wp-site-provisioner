<?php

namespace WPSP\query\params;

use WPSP\query\params\QueryParam as QueryParam;

class QueryParams {

    use \WPSP\traits\GetterSetter;

    protected $params;

    public function __construct( $params = null ) {
        $this->params = $params ? $params : array();
    }

    public function newParam( $key = null, $val = null ) {
        $newparam = new QueryParam( $key, $val );
        $this->addParam( $newparam );
        return $newparam;
    }

    public function addParam( $param ) {
        if ( $param instanceof QueryParam ) {
            array_push( $this->params, $param );
            return $param;
        } else {
            return $this->newParam();
        }
    }
}