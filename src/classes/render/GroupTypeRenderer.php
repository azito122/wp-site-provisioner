<?php

namespace WPSP\render;

include_once(__DIR__ . '/../GroupType.php');
include_once(__DIR__ . '/QueryRenderer.php');

abstract class GroupTypeRenderer extends Renderer {

    public static function render( $instance ) {
        $data = array(
            'label' => $instance->getLabel(),
            'meta-query' => self::entity( $instance->getMetaQuery() ),
            'user-query' => self::entity( $instance->getUserQuery() ),
        );
        return self::template( 'group-type', $data );
    }

    public static function derender( $type, $data ) {
        if (! array_key_exists( 'label', $data ) || empty( $data[ 'label' ] ) ) {
            return false;
        }
        $object = new \WPSP\GroupType();

        $object->setLabel( $data[ 'label' ] );

        $object->setMetaQuery( QueryRenderer::derender( 'Query', $data[ 'meta-query' ] ) );

        $object->setUserQuery( QueryRenderer::derender( 'Query', $data[ 'user-query' ] ) );

        $object->storeid = $data[ 'storeid' ];
        return $object;
    }

}