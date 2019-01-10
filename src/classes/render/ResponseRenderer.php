<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\render\ResponseMappingRenderer as ResponseMappingRenderer;
use WPSP\query\response\Response as Response;

abstract class ResponseRenderer extends Renderer {

    public static function render( $instance ) {
        $mappings = '';
        foreach ( $instance->getMappings() as $mapping ) {
            $mappings .= ResponseMappingRenderer::render( $mapping );
        }

        $data = array(
            'mappings' => $mappings,
        );
        return self::template( 'response', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $mappings = array();
        foreach ( $data[ 'map' ] as $mapping ) {
            array_push( $mappings, ResponseMappingRenderer::derender( $mapping ) );
        }

        $object = new Response( $mappings );

        return $object;
    }
}