<?php

namespace WPSP;

use Query;

class GroupType {

    private $label;
    private $sourcequery;
    private $templatequery;
    private $properties;

    public function __construct__() {
        $this->templatequery = new Query();
    }

    public function setLabel( $label ) {
        if ( is_string( $label ) && ! empty( $label ) ) {
            $this->label = $label;
        }
    }

    public function setSourceQuery( Query $query ) {
        $this->sourcequery = $query;
    }

    public function makeQuery( $data ) {
        $template = $this->templatequery;
        $Q = clone $template;

        $labelkey = $template->getLabel();
        $Q->setLabel( $data[ $labelkey ] );

        foreach( $template->getParams() as $key => $val ) {
            if ( preg_match( '//', $val ) ) {
                $Q->setParam( $key, $data[ $val ]);
            }
        }
        return $Q;
    }
}