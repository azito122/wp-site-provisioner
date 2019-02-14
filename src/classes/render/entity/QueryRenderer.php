<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\query\Query as Query;
use WPSP\render\entity\QueryParamRenderer as QueryParamRenderer;
use WPSP\render\entity\QueryResponseRenderer as QueryResponseRenderer;
use WPSP\render\Writer as Writer;

abstract class QueryRenderer implements \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        global $Store;

        $remotes = $Store->unstoreEntity( 'Remote' );
        $remotemenu = array();
        foreach ( $remotes as $remote ) {
            $remotemenu[ $remote->storeid ] = $remote->label;
        }

        $remotemenustring = Writer::select( [
            'name' => 'remoteid',
            'label' => 'Remote',
            'options' => $remotemenu,
            'default' => $instance->remoteid,
        ] );


        $data = array(
            'label'       => $instance->label,
            'storeid'     => $instance->storeid,
            'remotesmenu' => $remotemenustring,
            'path'        => $instance->extrapath,
            'params'      => self::renderParams( $instance->params ),
            'response'    => QueryResponseRenderer::render( $instance->responsemap ),
        );
        return Renderer::renderTemplate( 'entity', 'query', $data );
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
            $response = QueryResponseRenderer::derender( $data[ 'response' ] );
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
        return $paramsrendered;
    }

    public static function derenderParams( $data ) {
        $paramsderendered = array();
        foreach ( $data as $paramdata ) {
            array_push( $paramsderendered, QueryParamRenderer::derender( $paramdata ) );
        }
        return $paramsderendered;
    }

}