<?php

namespace WPSP\render;

abstract class GroupTypeRenderer extends Renderer {

    public static function render( $instance ) {
        $data = array(
            'group-type-label' => $instance->getLabel(),
            'meta-query' => self::entity( $instance->getMetaQuery() ),
            'user-query' => self::entity( $instance->getUserQuery() ),
        );
        return self::template( 'group-type', $data );
    }

}