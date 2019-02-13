<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\render\entity\QueryResponseMappingRenderer as QueryResponseMappingRenderer;
use WPSP\query\response\QueryResponse as QueryResponse;

abstract class QueryResponseRenderer implements \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $mappings = '';
        foreach ( $instance->mappings as $mapping ) {
            $mappings .= QueryResponseMappingRenderer::render( $mapping );
        }

        $data = array(
            'mappings' => $mappings,
        );
        return Renderer::renderTemplate( 'entity', 'query-response-map', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $mappings = array();
        foreach ( $data[ 'map' ] as $mapping ) {
            array_push( $mappings, QueryResponseMappingRenderer::derender( $mapping ) );
        }

        $object = new QueryResponse( $mappings );

        return $object;
    }
}