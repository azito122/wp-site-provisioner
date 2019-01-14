<?php

namespace WPSP\traits;

trait Storable {

    private $storeid;

    private function get_storeid() {
        if ( ! empty( $this->storeid ) ) {
            return $this->storeid;
        } else {
            $prefix = str_replace( '\\', '_', strtolower( get_class( $this ) ) ) . '_';
            $id = uniqid( $prefix );
            $this->storeid = $id;
            return $id;
        }
    }
}