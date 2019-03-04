<?php

namespace WPSP\traits;

trait Storable {

    protected $uid;

    public function __sleep() {
        if ( isset( $this->sleeplist ) ) {
            return array_merge( $this->sleeplist, [
                'uid'
            ] );
        } else {
            return array_keys( get_object_vars( $this ) );
        }
    }

    public function get_uid() {
        if ( isset( $this->uid ) ) {
            return $this->uid;
        } else {
            return $this->makeUID();
        }
    }

    public function makeUID() {
        $prefix = str_replace( '\\', '_', strtolower( get_class( $this ) ) ) . '_';
        $id = uniqid( $prefix );
        $this->uid = $id;
        return $id;
    }
}