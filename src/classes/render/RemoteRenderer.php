<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\query\Remote as Remote;

abstract class RemoteRenderer extends Renderer {

    public static function render( $instance ) {
        $data = array(
            'storeid' => $instance->storeid,
            'label' => $instance->label,
            'url' => $instance->url,
        );
        return self::template( 'remote', $data );
    }

    public static function derender( $data, $type  = '' ) {
        if (! array_key_exists( 'label', $data ) || empty( $data[ 'label' ] ) ) {
            return false;
        }
        $object = new Remote( $data[ 'label' ], $data[ 'url' ] );
        $object->storeid = $data[ 'storeid' ];
        return $object;
    }

}