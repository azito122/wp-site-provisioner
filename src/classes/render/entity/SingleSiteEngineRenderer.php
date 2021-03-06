<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\siteengine\SingleSiteEngine as SingleSiteEngine;

abstract class SingleSiteEngineRenderer extends \WPSP\render\entity\EntityRenderer {

    protected static $type = 'SingleSiteEngine';

    public static function render( $instance ) {
        $siteid = $instance->siteid;
        $siteurl = $siteid ? get_blog_details($siteid)->siteurl : '';
        $data = array(
            'uid'          => $instance->uid,
            'label'        => $instance->label,
            'owner-login'  => $instance->owner->login,
            'site-url'     => $siteurl,
            'site-path'    => $instance->getConfig( 'path', false ),
            'site-title'   => $instance->getConfig( 'title', false ),
            'site-tagline' => $instance->getConfig( 'tagline', false ),
        );
        return Renderer::renderTemplate( 'entity', 'single-site-engine', $data );
    }

    public static function derender( $data, $type  = '' ) {
        $object = self::getBaseObject( $data );

        $object->label = $data[ 'label' ];
        $object->uid   = $data[ 'uid' ];
        $object->setConfig( 'path', $data[ 'site-path' ] );
        $object->setConfig( 'title', $data[ 'site-title' ] );
        $object->setConfig( 'tagline', $data[ 'site-tagline' ] );

        return $object;
    }

}