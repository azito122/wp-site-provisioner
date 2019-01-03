<?php

namespace WPSP\render;

include_once(__DIR__ . '/../infrastructure/Store.php');

abstract class QueryRenderer extends Renderer {

    public static function render( $instance ) {
        global $Store;


        $remotes = $Store->unstore( 'Remote' );
        $remotemenu = array();
        foreach ( $remotes as $remote ) {
            $remotemenu[ $remote->storeid ] = $remote->getLabel();
        }
        $data = array(
            'query-label' => $instance->getLabel(),
            'remotes' => $remotemenu,
            'remoteid' => $instance->getRemoteId(),
        );
        return self::template( 'query', $data );
    }

}