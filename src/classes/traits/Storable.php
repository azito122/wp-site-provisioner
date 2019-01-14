<?php

namespace WPSP\traits;

trait Storable {

    private $storeid;

    private function get_storeid() {
        if ( ! empty( $this->storeid ) ) {
            return $this->storeid;
        } else {
            $prefix = strtolower( get_class( $this ) );
            return uniqid( $prefix, true );
        }
    }
}