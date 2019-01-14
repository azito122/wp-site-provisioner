<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\render\QueryResponseMappingRenderer as QueryResponseMappingRenderer;
use WPSP\query\response\QueryResponseMap as QueryResponseMap;

abstract class QueryResponseMapRenderer extends Renderer {

    public static function render( $instance ) {
        $mappings = '';
        foreach ( $instance->mappings as $mapping ) {
            $mappings .= QueryResponseMappingRenderer::render( $mapping );
        }

        $data = array(
            'mappings' => $mappings,
        );
        return self::template( 'response', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $mappings = array();
        foreach ( $data[ 'map' ] as $mapping ) {
            array_push( $mappings, QueryResponseMappingRenderer::derender( $mapping ) );
        }

        $object = new QueryResponseMap( $mappings );

        return $object;
    }
}