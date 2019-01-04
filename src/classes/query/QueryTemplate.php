<?php

namespace WPSP;

class QueryTemplate extends Query {

    public function run( $typedata ) {
        $this->resolve( $data );

        parent::run();
    }

    public function resolve( $data ) {
        // $labelkey = $template->getLabel();
        // $Q->setLabel( $data[ $labelkey ] );

        foreach( $this->getParams() as $param ) {
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
    }
}