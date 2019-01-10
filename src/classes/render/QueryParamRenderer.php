<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\query\QueryParam as QueryParam;

abstract class QueryParamRenderer extends Renderer {

    public static function render( $instance ) {
        $data = array(
            'key'   => $instance->getKey(),
            'value' => $instance->getValue(),
        );
        return self::template( 'query-param', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new QueryParam( $data[ 'key' ], $data[ 'value' ] );

        return $object;
    }
}