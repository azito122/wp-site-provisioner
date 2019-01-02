<?php

namespace WPSP\render;

abstract class GroupTypeRenderer extends Renderer {

    public static function render( $instance ) {
        $data = array(
            'group-type-label' => $instance->getLabel(),
            'source-query' => self::entity( $instance->getSourceQuery() ),
            'template-query' => self::entity( $instance->getTemplateQuery() ),
        );
        return self::template( 'group-type', $data );
    }

}