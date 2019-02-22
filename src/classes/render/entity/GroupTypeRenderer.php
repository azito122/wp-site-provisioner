<?php

namespace WPSP\render\entity;

use WPSP\render\Renderer as Renderer;
use WPSP\render\entity\QueryRenderer as QueryRenderer;
use WPSP\GroupType as GroupType;

abstract class GroupTypeRenderer extends \WPSP\render\entity\EntityRenderer {

    public static function render( $instance ) {
        $data = array(
            'storeid' => $instance->storeid,
            'label' => $instance->label,
            'meta-query' => Renderer::renderEntity( $instance->metaquery ),
            'user-query' => Renderer::renderEntity( $instance->userquery ),
        );
        return Renderer::renderTemplate( 'entity', 'group-type', $data );
    }

    public static function derender( $data, $type = '' ) {
        if ( ! array_key_exists( 'label', $data ) || empty( $data[ 'label' ] ) ) {
            return false;
        }
        $object = new GroupType();

        $object->label = $data[ 'label' ];

        $object->metaquery = QueryRenderer::derender( $data[ 'meta-query' ] );

        $object->userquery = QueryRenderer::derender( $data[ 'user-query' ] );

        $object->storeid = $data[ 'storeid' ];
        return $object;
    }

}