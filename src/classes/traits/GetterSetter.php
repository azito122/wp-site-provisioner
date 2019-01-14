<?php

namespace WPSP\traits;

trait GetterSetter {

    function __get( $name ) {
        $m = "get_$name";
        if ( method_exists( $this, $m ) ) {
            return $this->$m();
        } else if ( property_exists ( $this, $name ) ) {
            return $this->$name;
        } else {
            $trace = debug_backtrace();
            trigger_error(
                'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
        }
    }

    function __set( $name, $value ) {
        $m = "set_$name";
        if ( method_exists( $this, $m ) ) {
            return $this->$m( $value );
        } else if ( property_exists ( $this, $name ) ) {
            return $this->$name = $value;
        } else {
            $trace = debug_backtrace();
            trigger_error(
                'Undefined property via __set(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
        }
    }
}