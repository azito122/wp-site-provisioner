<?php

namespace WPSP\traits;

trait Storable {

    private $storeid;

    private function makeStoreId() {
        $prefix = str_replace( '\\', '_', strtolower( get_class( $this ) ) ) . '_';
        $id = uniqid( $prefix );
        $this->storeid = $id;
        return $id;
    }
}