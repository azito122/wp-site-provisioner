<?php

namespace WPSP\render;

abstract class RendererFactory {

    public static function getRenderer( $object ) {
        $classname = end( explode( '\\', get_class( $object ) ) );

        switch ( $classname ) {
            case 'GroupType':
                return new GroupTypeRenderer( $object );
            case 'Group':
                return new GroupRenderer( $object );
            case 'Query':
                return new QueryRenderer( $object );
            case 'Remote':
                return new RemoteRenderer( $object );
            case 'SiteEngine':
                return new SiteEngineRenderer( $object );
        }
    }

}