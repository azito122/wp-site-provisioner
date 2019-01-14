<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\query\response\QueryResponseMapping as QueryResponseMapping;

abstract class QueryResponseMappingRenderer extends Renderer {

    public static function render( $instance ) {
        $data = array(
            'localkey'    => $instance->localkey,
            'responsekey' => $instance->responsekey,
        );
        return self::template( 'response-mapping', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new QueryResponseMapping( $data[ 'localkey' ], $data[ 'responsekey' ] );

        return $object;
    }
}