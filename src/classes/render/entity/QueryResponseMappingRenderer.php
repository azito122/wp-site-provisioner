<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\query\response\QueryResponseMapping as QueryResponseMapping;

abstract class QueryResponseMappingRenderer implements \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $data = array(
            'localkey'    => $instance->localkey,
            'responsekey' => $instance->responsekey,
        );
        return Renderer::renderTemplate( 'entity', 'query-response-mapping', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new QueryResponseMapping( $data[ 'localkey' ], $data[ 'responsekey' ] );

        return $object;
    }
}