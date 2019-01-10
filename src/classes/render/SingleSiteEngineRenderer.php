<?php

namespace WPSP\render;

use WPSP\render\Renderer as Renderer;
use WPSP\SingleSiteEngine as SingleSiteEngine;

abstract class SingleSiteEngineRenderer extends Renderer {

    public static function render( $instance ) {
        $sitetitle = $instance->getConfig( 'title', false );
        $sitetagline = $instance->getConfig( 'tagline', false );
        $data = array(
            'site-title'   => $sitetitle,
            'site-tagline' => $sitetagline,
        );
        return self::template( 'single-site-engine', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new SingleSiteEngine();

        $object->setConfig( 'title', $data['title'] );

        $object->setConfig( 'tagline', $data[ 'tagline' ] );

        return $object;
    }

}