<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\Group as Group;

abstract class GroupRenderer extends Renderer {

    public static function render( $instance ) {
        $siteengines = $instance->sitengines;
        $siteenginesrendered = array();
        foreach ( $siteengines as $siteengine ) {
            array_push( $siteenginesrendered, SiteEngineRenderer::render( $siteengine ) );
        }

        $data = array(
            'storeid'       => $instance->storeid,
            'label'         => $instance->label,
            // 'meta'          => json_encode( $instance->loadMeta() ),
            'site-engines'  => $siteenginesrendered,
        );

        return self::template( 'remote', $data );
    }

    public static function derender( $data, $type  = '' ) {
        if (! array_key_exists( 'label', $data ) || empty( $data[ 'label' ] ) ) {
            return false;
        }

        $object = new Group();

        $siteengines = array_map( function( $se ) {
            return SiteEngineRenderer::derender( $se );
        }, $data[ 'site-engines' ]);

        $object->siteengines = $siteengines;

        $object->storeid = $data[ 'storeid' ];
        return $object;
    }

}