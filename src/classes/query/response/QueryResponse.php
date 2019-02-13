<?php

namespace WPSP\query\response;

use WPSP\query\response\QueryResponseMapping as QueryResponseMapping;

class QueryResponse {

    use \WPSP\traits\GetterSetter;

    protected $mappings;
    protected $depth;
    protected $position;
    protected $allowadd;

    public function __construct( $mappings = array(), $allowdadd = true, $depth = 0, $position = null ) {
        $this->mappings = $mappings ? $mappings : array();
        $this->depth = $depth;
        $this->position = $position;
    }

    public function normalize( $response ) {
        $normalized = array();
        foreach ( $response as $piece ) {
            $normalizedpiece = array();
            $piece = (array)$piece;
            foreach ( $piece as $key => $val ) {
                $mapkeys = $this->getMappingKeys();
                if ( in_array( $key, $mapkeys ) ) {
                    $mapping = $this->getMappingByResponseKey( $key );
                    $resultkey = $mapping->localkey;
                    $resultvalue = $mapping->resolveValue( $piece );
                } else {
                    $resultkey = $key;
                    if ( is_array( $val ) ) {
                        $baseresponse = new QueryResponse();
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

    public function newMapping( $localkey = null, $responsekey = null ) {
        $newmapping = new QueryResponseMapping( $localkey, $responsekey );
        $this->addMapping( $newmapping );
        return $newmapping;
    }

    public function addMapping( $mapping ) {
        if ( $mapping instanceof QueryResponseMapping ) {
            array_push( $this->mappings, $mapping );
        } else {
            $this->newMapping();
        }
    }

    public function getMappingKeys() {
        $keys = array();
        foreach ( $this->mappings as $mapping ) {
            array_push( $keys, $mapping->responsekey );
        }
        return $keys;
    }

    public function getMappingByResponseKey( $key ) {
        foreach ( $this->mappings as $mapping ) {
            if ( $mapping->responsekey == $key ) {
                return $mapping;
            }
        }
        return false;
    }
}