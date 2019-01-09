<?php

namespace WPSP\query\response;

use WPSP\query\response\ResponseMapping as ResponseMapping;

class Response {

    private $map;
    private $depth;
    private $position;
    private $allowadd;

    public function __construct( $map = array(), $allowdadd = true, $depth = 0, $position = null ) {
        $this->map = $map;
        $this->depth = $depth;
        $this->position = $position;
    }

    public function normalize( $response ) {
        $normalized = array();
        foreach ( $response as $piece ) {
            $normalizedpiece = array();
            $piece = (array)$piece;
            foreach ( $piece as $key => $val ) {
                $mapkeys = $this->getMapKeys();
                if ( in_array( $key, $mapkeys ) ) {
                    $mapping = $this->getMappingByResponseKey( $key );
                    $resultkey = $mapping->getLocalKey();
                    $resultvalue = $mapping->getValue( $piece );
                } else {
                    $resultkey = $key;
                    if ( is_array( $val ) ) {
                        $baseresponse = new Response();
                        $resultvalue = $baseresponse->normalize( $val );
                    } else {
                        $resultvalue = $val;
                    }
                }
                $normalizedpiece[ $resultkey ] = $resultvalue;
            }
            array_push( $normalized, $normalizedpiece );
        }

        return $normalized;
    }

    public function setMapping( $localkey, $responsekey ) {
        $this->map[ $localkey ] = $responsekey;
    }

    public function addMapping( $mapping ) {
        if ( $mapping instanceof ResponseMapping ) {
            array_push( $this->map, $mapping );
        } else {
            array_push( $this->map, new ResponseMapping() );
        }
    }

    public function getMapKeys() {
        $keys = array();
        foreach ( $this->map as $mapping ) {
            array_push( $keys, $mapping->getResponseKey() );
        }
        return $keys;
    }

    public function getMappingByResponseKey( $key ) {
        foreach ( $this->map as $mapping ) {
            if ( $mapping->getResponseKey() == $key ) {
                return $mapping;
            }
        }
        return false;
    }
}