<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\query\Query as Query;
use WPSP\render\QueryParamRenderer as QueryParamRenderer;

abstract class QueryRenderer extends Renderer {

    public static function render( $instance ) {
        global $Store;

        $remotes = $Store->unstoreEntity( 'Remote' );
        $remotemenu = array();
        foreach ( $remotes as $remote ) {
            $remotemenu[ $remote->storeid ] = $remote->getLabel();
        }
        $data = array(
            'storeid'  => $instance->storeid,
            'remotes'  => $remotemenu,
            'remoteid' => $instance->getRemoteId(),
            'path'     => $instance->getExtraPath(),
            'params'   => self::renderParams( $instance->getParams() ),
        );
        return self::template( 'query', $data );
    }

    public static function derender( $type, $data ) {
        $object = new Query();

        $object->setRemoteId( $data[ 'remoteid' ] );

        $object->setExtraPath( $data[ 'path' ] );

        $paramsderendered = self::derenderParams( $data[ 'params'] );
        $object->setParams( $paramsderendered );

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
            array_push( $paramsderendered, QueryParamRenderer::derender( 'QueryParam', $paramdata ) );
        }
        return $paramsderendered;
    }

}