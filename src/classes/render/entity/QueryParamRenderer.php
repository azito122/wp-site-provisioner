<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\query\params\QueryParam as QueryParam;

abstract class QueryParamRenderer implements \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $data = array(
            'entity-type' => 'query-param',
            'key'         => $instance->key,
            'value'       => $instance->value,
            'key_name'    => 'key',
            'value_name'  => 'value',
            'key_label'   => 'Key',
            'value_label' => 'Value'
        );
        return Renderer::renderTemplate( 'special', 'key-val-block', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new QueryParam( $data[ 'key' ], $data[ 'value' ] );

        return $object;
    }
}