<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\render\entity\SingleSiteEngineRenderer as SingleSiteEngineRenderer;

abstract class GroupRenderer extends \WPSP\render\entity\EntityRenderer {

    protected static $type = 'Group';

    public static function render( $instance ) {
        $siteengines = $instance->siteengines;
        $siteenginesrendered = '';
        if ( $siteengines ) {
            foreach ( $siteengines as $index => $siteengine ) {
                $extra = [
                    'index' => $index,
                ];
                $siteenginesrendered .= SingleSiteEngineRenderer::render( $siteengine, $extra );
            }
        }

        $data = array(
            'uid'       => $instance->uid,
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

        $object = self::getBaseObject( $data );

        $object->label = $data[ 'label' ];
        $object->uid = $data[ 'uid' ];
        $object->queryid = $data[ 'queryid' ];
        $object->meta = (array) json_decode( urldecode( $data[ 'meta' ] ) );

        $siteengines = array_map( function( $se ) {
            return SingleSiteEngineRenderer::derender( $se );
        }, $data[ 'site-engines' ]);

        $object->siteengines = $siteengines;

        return $object;
    }

}