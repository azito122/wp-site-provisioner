<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\SingleSiteEngine as SingleSiteEngine;

abstract class SingleSiteEngineRenderer implements \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $siteid = $instance->siteid;
        $siteurl = $siteid ? get_blog_details($siteid)->siteurl : '';
        $data = array(
            'site-url'     => $siteurl,
            'site-path'    => $instance->getConfig( 'path', false ),
            'site-title'   => $instance->getConfig( 'title', false ),
            'site-tagline' => $instance->getConfig( 'tagline', false ),
        );
        return Renderer::renderTemplate( 'entity', 'single-site-engine', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new SingleSiteEngine();

        $object->setConfig( 'title', $data['title'] );

        $object->setConfig( 'tagline', $data[ 'tagline' ] );

        return $object;
    }

}