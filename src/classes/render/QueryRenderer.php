<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\query\Query as Query;
use WPSP\render\QueryParamRenderer as QueryParamRenderer;
use WPSP\render\QueryResponseMapRenderer as QueryResponseMapRenderer;

abstract class QueryRenderer extends Renderer {

    public static function render( $instance ) {
        global $Store;

        $remotes = $Store->unstoreEntity( 'Remote' );
        $remotemenu = array();
        foreach ( $remotes as $remote ) {
            $remotemenu[ $remote->storeid ] = $remote->label;
        }
        $data = array(
            'storeid'  => $instance->storeid,
            'remotes'  => $remotemenu,
            'remoteid' => $instance->remoteid,
            'path'     => $instance->extrapath,
            'params'   => self::renderParams( $instance->params ),
            'response' => QueryResponseMapRenderer::render( $instance->responsemap ),
        );
        return self::template( 'query', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new Query();

        $object->remoteid = $data[ 'remoteid' ];

        $object->extrapath = $data[ 'path' ];

        if ( array_key_exists( 'params', $data ) ) {
            $paramsderendered = self::derenderParams( $data[ 'params' ] );
            $object->params = $paramsderendered;
        }

        if ( array_key_exists( 'response', $data ) ) {
            $response = QueryResponseMapRenderer::derender( $data[ 'response' ] );
            $object->responsemap = $response;
        }

        $object->storeid = $data[ 'storeid' ];
        return $object;
    }

    public static function renderParams( $params ) {
        $paramsrendered = '';
        foreach ( $params as $param ) {
            $paramsrendered .= QueryParamRenderer::render( $param );
        }
        return self::template( 'query-params', array(
            'params' => $paramsrendered,
        ) );
    }

    public static function derenderParams( $data ) {
        $paramsderendered = array();
        foreach ( $data as $paramdata ) {
            array_push( $paramsderendered, QueryParamRenderer::derender( $paramdata ) );
        }
        return $paramsderendered;
    }

}