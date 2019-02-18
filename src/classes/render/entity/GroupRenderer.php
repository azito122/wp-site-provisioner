<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\Group as Group;
use WPSP\render\entity\SingleSiteEngineRenderer as SingleSiteEngineRenderer;

abstract class GroupRenderer implements \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $siteengines = $instance->siteengines;
        $siteenginesrendered = '';
        foreach ( $siteengines as $siteengine ) {
            $siteenginesrendered .= SingleSiteEngineRenderer::render( $siteengine );
        }

        $data = array(
            'storeid'       => $instance->storeid,
            'label'         => $instance->label,
            'queryid'       => $instance->queryid,
            'meta'          => urlencode( json_encode( $instance->meta ) ),
            'site-engines'  => $siteenginesrendered,
        );

        return Renderer::renderTemplate( 'entity', 'group', $data );
    }

    public static function derender( $data ) {
        if (! array_key_exists( 'label', $data ) || empty( $data[ 'label' ] ) ) {
            return false;
        }

        $object = new Group();

        $object->label = $data[ 'label' ];
        $object->storeid = $data[ 'storeid' ];
        $object->queryid = $data[ 'queryid' ];
        $object->meta = (array) json_decode( urldecode( $data[ 'meta' ] ) );

        $siteengines = array_map( function( $se ) {
            return SingleSiteEngineRenderer::derender( $se );
        }, $data[ 'site-engines' ]);

        $object->siteengines = $siteengines;

        return $object;
    }

}