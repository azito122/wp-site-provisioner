<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\query\response\ResponseMapping as ResponseMapping;

abstract class ResponseMappingRenderer extends Renderer {

    public static function render( $instance ) {
        $data = array(
            'localkey'    => $instance->getLocalKey(),
            'responsekey' => $instance->getResponseKey(),
        );
        return self::template( 'response-mapping', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new ResponseMapping( $data[ 'localkey' ], $data[ 'responsekey' ] );

        return $object;
    }
}