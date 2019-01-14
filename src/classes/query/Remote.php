<?php

namespace WPSP\query;

class Remote {

    use \WPSP\traits\GetterSetter;
    use \WPSP\traits\Storable;

    protected $label;
    protected $url;

    public function __construct ( $label = null, $url = null ) {
        $this->label = $label;
        $this->url = $url;
    }
}