<?php

namespace WPSP;

class Remote {

    private $label;
    private $baseurl;
    private $path;

    public function __construct__ ($label, $url, $path) {
        $this->label = $label;
        $this->baseurl = $url;
        $this->path = $path;
    }

    public function getFullUrl() {
        return $this->baseurl . '/' . $this->path;
    }
}