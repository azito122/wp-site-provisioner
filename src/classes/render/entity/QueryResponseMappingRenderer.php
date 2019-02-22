<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\query\response\QueryResponseMapping as QueryResponseMapping;

abstract class QueryResponseMappingRenderer extends \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $data = array(
            'entity-type' => 'query-response-mapping',
            'key'         => $instance->localkey,
            'value'       => $instance->responsekey,
            'key_name'    => 'localkey',
            'value_name'  => 'responsekey',
            'key_label'   => 'Local Key',
            'value_label' => 'Response Key'
        );
        return Renderer::renderTemplate( 'special', 'key-val-block', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new QueryResponseMapping( $data[ 'localkey' ], $data[ 'responsekey' ] );

        return $object;
    }
}