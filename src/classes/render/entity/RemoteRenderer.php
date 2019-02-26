<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\query\Remote as Remote;

abstract class RemoteRenderer extends \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $data = array(
            'uid' => $instance->uid,
            'label' => $instance->label,
            'url' => $instance->url,
        );
        return Renderer::renderTemplate( 'entity', 'remote', $data );
    }

    public static function derender( $data, $type  = '' ) {
        if (! array_key_exists( 'label', $data ) || empty( $data[ 'label' ] ) ) {
            return false;
        }
        $object = new Remote( $data[ 'label' ], $data[ 'url' ] );
        $object->uid = $data[ 'uid' ];
        return $object;
    }

}