<?php

namespace WPSP\query\response;

use WPSP\query\response\Response as Response;

class ResponseMapping {

    private $localkey;
    private $responsekey;
    private $type;
    private $subresponse;

    public function __construct( $localkey = '', $responsekey = '', $type = 'singlevalue', $subresponsemap = null ) {
        $this->localkey    = $localkey;
        $this->responsekey = $responsekey;
        $this->type        = $type;

        if ( $type == 'complex' ) {
            $subresponse = new Response( $subresponsemap );
        }
    }

    public function getValue( $responsepiece ) {
        if ( $this->type == 'singlevalue' ) {
            return $responsepiece[ $this->responsekey ];
        } else {
            return $subresponse->normalize( $responsepiece[ $responsekey ] );
        }
    }

    public function getLocalKey() {
        return $this->localkey;
    }

    public function getResponseKey() {
        return $this->responsekey;
    }
}
