<?php

namespace WPSP\render\entity;

use function WPSP\resolve_classname;

abstract class EntityRenderer implements \WPSP\render\entity\InterfaceEntityRenderer {

    protected static $type = 'no'; // 'Group', 'Query', etc.

    public static function render( $instance ) {
    }

    public static function derender( $data ) {
    }

    public static function getBaseObject( $data ) {
        global $Store;

        if ( isset( $data[ 'storeid' ] ) && ! empty( $data[ 'storeid' ] ) ) {
            $object = $Store->unstoreEntity( static::$type, $data[ 'storeid' ] );
        } else {
            $type = resolve_classname( static::$type );
            $object = new $type();
        }

        return $object;
    }

}