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
        $result = array();

        foreach ( $response as $key => $val ) {
            $mapkeys = $this->getMapKeys();
            if ( in_array( $key, $mapkeys ) ) {
                $resultkey = $this->map[ $key ]->getLocalKey();
                $resultvalue = $this->map[ $key ]->getValue( $response );
            } else {
                $resultkey = $key;
                if ( is_array( $val ) ) {
                    $baseresponse = new Response();
                    $resultvalue = $baseresponse->normalize( $val );
                } else {
                    $resultvalue = $val;
                }
            }
            $result[ $resultkey ] = $resultvalue;
        }

        return $result;
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
}