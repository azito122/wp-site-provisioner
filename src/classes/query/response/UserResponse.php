<?php

namespace WPSP\query\response;

use WPSP\query\response\QueryResponse as QueryResponse;
use WPSP\query\response\QueryResponseMapping as QueryResponseMapping;

class UserResponse extends QueryResponse {

    public function __construct( $map = array(), $depth = 0, $position = null  ) {
        $rolemap = array(
            new QueryResponseMapping( 'displayname', 'name' ),
            new QueryResponseMapping( 'identifier', 'shortname' ),
        );

        $this->mappings = array(
            new QueryResponseMapping( 'login', 'username' ),
            new QueryResponseMapping( 'firstname', 'firstname' ),
            new QueryResponseMapping( 'lastname', 'lastname' ),
            new QueryResponseMapping( 'email', 'email' ),
            new QueryResponseMapping( 'roles', 'roles', 'complex', new QueryResponse( $rolemap ) ),
        );
    }

}