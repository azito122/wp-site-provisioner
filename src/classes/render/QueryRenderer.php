<?php

namespace WPSP\render;

include_once(__DIR__ . '/../Query/Query.php');

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

    public static function derender( $type, $data ) {
        if (! array_key_exists( 'label', $data ) || empty( $data[ 'label' ] ) ) {
            return false;
        }
        $object = new \WPSP\Query();

        $object->setLabel( $data[ 'label' ] );

        $object->storeid = $data[ 'storeid' ];
        return $object;
    }

}