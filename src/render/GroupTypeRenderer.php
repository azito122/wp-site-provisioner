<?php

namespace WPSP\render;

class GroupTypeRenderer extends Renderer {

    public function __construct( $object ) {
        $this->dataobject = $object;
    }

    public function render( $instance ) {
        $data = array(
            'group-type-label' => $instance->getLabel(),
            'sourcequery' => $instance->getSourceQuery(),
            'templatequery' => $instance->getTemplateQuery(),
        );
        return self::template( 'group-type-form', $data );
    }

}