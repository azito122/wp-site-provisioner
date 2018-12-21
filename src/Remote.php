<?php

namespace WPSP;

class Remote {

    public $storeid;
    private $label;
    private $baseurl;
    private $path;

    public function __construct ( $label = null, $url = null, $path = null ) {
        $this->label = $label;
        $this->baseurl = $url;
        $this->path = $path;
    }

    public function getFullUrl() {
        return $this->baseurl . '/' . $this->path;
    }

    public function setBaseUrl( $url ) {
        $this->baseurl = $url;
    }

    public function setPath( $path ) {
        $this->path = $path;
    }

    public function setFullUrl( $url ) {
        $parsed = parse_url( $url );
        $this->setBaseUrl ( $parsed['scheme'] . '://' . $parsed[ 'host' ] );
        $this->setPath( $parsed[ 'path' ] );
    }
}