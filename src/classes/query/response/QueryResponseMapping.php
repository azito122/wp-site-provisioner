<?php

namespace WPSP\query\response;

use WPSP\query\response\QueryResponse as QueryResponse;

class QueryResponseMapping {

    use \WPSP\traits\GetterSetter;

    protected $localkey;
    protected $responsekey;
    protected $type;
    protected $subresponse;

    public function __construct( $localkey = '', $responsekey = '', $type = 'singlevalue', $subresponsemappings = null ) {
        $this->localkey    = $localkey;
        $this->responsekey = $responsekey;
        $this->type        = $type;

        if ( $type == 'complex' ) {
            $subresponse = new QueryResponse( $subresponsemappings );
        }
    }

    public function resolveValue( $responsepiece ) {
        if ( $this->type == 'singlevalue' ) {
            return $responsepiece[ $this->responsekey ];
        } else {
            return $subresponse->normalize( $responsepiece[ $responsekey ] );
        }
    }

}
