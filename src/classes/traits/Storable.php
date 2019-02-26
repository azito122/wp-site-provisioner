<?php

namespace WPSP\traits;

trait Storable {

    protected $uid;
    protected $sleeplist;

    public function __sleep() {
        if ( ! is_null( $this->sleeplist ) ) {
            return array_merge( $this->sleeplist, [
                'uid'
            ] );
        } else {
            return array_keys( get_object_vars( $this ) );
        }
    }

    public function makeUID() {
        $prefix = str_replace( '\\', '_', strtolower( get_class( $this ) ) ) . '_';
        $id = uniqid( $prefix );
        $this->uid = $id;
        return $id;
    }
}