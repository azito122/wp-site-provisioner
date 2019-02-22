<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\query\params\QueryParams as QueryParams;
use WPSP\render\entity\QueryParamRenderer as QueryParamRenderer;

abstract class QueryParamsRenderer extends \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $params = '';
        foreach ( $instance->params as $param ) {
            $params .= QueryParamRenderer::render( $param );
        }

        $data = array(
            'params' => $params,
        );
        return Renderer::renderTemplate( 'entity', 'query-params', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $params = array();
        foreach ( $data[ 'params' ] as $param ) {
            array_push( $params, QueryParamRenderer::derender( $param ) );
        }

        $object = new QueryParams( $params );

        return $object;
    }
}