<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\siteengine\SingleSiteEngine as SingleSiteEngine;

abstract class SingleSiteEngineRenderer implements \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $siteid = $instance->siteid;
        $siteurl = $siteid ? get_blog_details($siteid)->siteurl : '';
        $data = array(
            'owner_login'  => $instance->owner->login,
            'site-url'     => $siteurl,
            'site-path'    => $instance->getConfig( 'path', false ),
            'site-title'   => $instance->getConfig( 'title', false ),
            'site-tagline' => $instance->getConfig( 'tagline', false ),
        );
        return Renderer::renderTemplate( 'entity', 'single-site-engine', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = new SingleSiteEngine();

        $object->setConfig( 'path', $data[ 'site-path' ] );
        $object->setConfig( 'title', $data[ 'site-title' ] );
        $object->setConfig( 'tagline', $data[ 'site-tagline' ] );

        return $object;
    }

}