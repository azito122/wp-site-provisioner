<?php

namespace WPSP\query;

use WPSP\query\QueryParam as QueryParam;

class QueryParamMap {

    use \WPSP\traits\GetterSetter;

    protected $params;

    public function __construct( $params = null ) {
        $this->mappings = $params ? $params : array();
    }
}