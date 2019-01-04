<?php

namespace WPSP\query\response;

class ResponseMapping {

    private $localkey;
    private $responsekey;
    private $type;
    private $subresponse;

    public function __construct( $localkey, $responsekey, $type = 'singlevalue', $subresponsemap = null ) {
        $this->localkey    = $localkey;
        $this->responsekey = $responsekey;
        $this->type        = $type;

        if ( $type == 'complex' ) {
            $subresponse = new Response( $subresponsemap );
        }
    }

    public function getValue( $responsepiece ) {
        if ( $this->type == 'singlevalue' ) {
            return $responsepiece[ $responsekey ];
        } else {
            return $subresponse->normalize( $responsepiece[ $responsekey ] );
        }
    }

}
