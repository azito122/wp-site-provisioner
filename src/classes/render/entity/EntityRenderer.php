<?php

namespace WPSP\render\entity;

use function WPSP\resolve_classname;

abstract class EntityRenderer implements \WPSP\render\entity\InterfaceEntityRenderer {

    protected static $type; // 'Group', 'Query', etc.

    public static function render( $instance ) {
    }

    public static function derender( $data ) {
    }

    public static function getBaseObject( $data ) {
        global $Store;

        // Get object if stored.
        $object = $Store->unstoreEntity( static::$type, $data[ 'uid' ] );

        // If not stored, get blank.
        if ( ! $object ) {
            $type = resolve_classname( static::$type );
            $object = new $type();
        }

        return $object;
    }

}