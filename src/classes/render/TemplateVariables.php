<?php

namespace WPSP\render;

class TemplateVariables {

    private $data = array();

    public function __construct( $data ) {
        $this->data = $data;
    }

    public function get( $name, $default = '' ) {
        if ( array_key_exists( $name, $this->data ) && ! empty( $this->data[ $name ] && is_scalar( $this->data[ $name ] ) ) ) {
            return $this->data[ $name ];
        }
        return $default;
    }
}