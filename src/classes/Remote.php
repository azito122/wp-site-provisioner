<?php

namespace WPSP;

class Remote {

    public $storeid;
    private $label;
    private $url;

    public function __construct ( $label = null, $url = null ) {
        $this->label = $label;
        $this->url = $url;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl( $url ) {
        $this->url = $url;
    }
}