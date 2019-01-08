<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\query\Query as Query;

abstract class QueryRenderer extends Renderer {

    public static function render( $instance ) {
        global $Store;

        $remotes = $Store->unstore( 'Remote' );
        $remotemenu = array();
        foreach ( $remotes as $remote ) {
            $remotemenu[ $remote->storeid ] = $remote->getLabel();
        }
        $data = array(
            'storeid' => $instance->storeid,
            'label' => $instance->getLabel(),
            'remotes' => $remotemenu,
            'remoteid' => $instance->getRemoteId(),
        );
        return self::template( 'query', $data );
    }

    public static function derender( $type, $data ) {
        if (! array_key_exists( 'label', $data ) || empty( $data[ 'label' ] ) ) {
            return false;
        }
        $object = new Query();

        $object->setLabel( $data[ 'label' ] );

        $object->setRemoteId( $data[ 'remoteid' ] );

        $object->storeid = $data[ 'storeid' ];
        return $object;
    }

}