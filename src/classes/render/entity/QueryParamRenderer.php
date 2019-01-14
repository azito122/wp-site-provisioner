<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\query\QueryParam as QueryParam;

abstract class QueryParamRenderer implements \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $data = array(
            'key'   => $instance->key,
            'value' => $instance->value,
        );
        return Renderer::renderTemplate( 'query-param', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new QueryParam( $data[ 'key' ], $data[ 'value' ] );

        return $object;
    }
}