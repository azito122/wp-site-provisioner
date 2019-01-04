<?php

namespace WPSP;

include_once(__DIR__ . '/../Remote.php');
include_once(__DIR__ . '/QueryParam.php');
include_once(__DIR__ . '/../UserList.php');
include_once(__DIR__ . '/../infrastructure/Store.php');

class Response {

    private $normalized;
    private $map;
    private $depth;
    private $position;

    public function __construct( $map, $depth = 0, $position = null ) {
        $this->map = $map;
        $this->depth = $depth;
        $this->position = $position;
    }

    public function normalize( $response ) {
        $result = array();

        foreach ( $response as $piece ) {
            foreach ( $this->map as $mapping ) {
                $value = $mapping->getValue( $piece );
                $result[ $mapping->getLocalKey() ] = $value;
            }
        }

        return $result;
    }

    public function setMapping( $localkey, $responsekey ) {
        $this->map[ $localkey ] = $responsekey;
    }
}