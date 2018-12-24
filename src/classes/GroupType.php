<?php

namespace WPSP;

include_once(__DIR__ . '/Query.php');

class GroupType {

    private $label;
    private $sourcequery;
    private $templatequery;
    private $properties;

    public function __construct() {
        $this->templatequery = new Query();
        $this->sourcequery = new Query();
    }

    public function getTemplateQuery() {
        return $this->templatequery;
    }

    public function getLabel() {
        return $this->label;
    }

    public function setLabel( $label ) {
        if ( is_string( $label ) && ! empty( $label ) ) {
            $this->label = $label;
        }
    }

    public function getSourceQuery() {
        return $this->sourcequery;
    }

    public function setSourceQuery( Query $query ) {
        $this->sourcequery = $query;
    }

    public function getData() {
        return $this->sourcequery->run();
    }

    public function makeQuery( $data ) {
        $template = $this->templatequery;
        $Q = clone $template;

        // $labelkey = $template->getLabel();
        // $Q->setLabel( $data[ $labelkey ] );

        foreach( $template->getParams() as $param ) {
            $matches = array();
            $val = $param->getValue();
            if ( preg_match( '/.*\{(.*?)\}.*/', $val, $matches ) ) {
                foreach ( $matches as $match ) {
                    if ( array_key_exists( $match, $data ) ) {
                        $val = preg_replace( "/\{$match\}/", $data[ $match ], $val );
                    }
                }
            }
            $param->setValue( $val );
        }
        return $Q;
    }
}