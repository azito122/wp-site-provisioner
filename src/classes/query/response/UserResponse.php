<?php

namespace WPSP\query\response;

use WPSP\query\response\Response as Response;
use WPSP\query\response\ResponseMapping as ResponseMapping;

class UserResponse extends Response {

    public function __construct( $map = array(), $depth = 0, $position = null  ) {
        $rolemap = array(
            new ResponseMapping( 'displayname', 'name' ),
            new ResponseMapping( 'identifier', 'shortname' ),
        );

        $this->map = array(
            new ResponseMapping( 'login', 'username' ),
            new ResponseMapping( 'firstname', 'firstname' ),
            new ResponseMapping( 'lastname', 'lastname' ),
            new ResponseMapping( 'email', 'email' ),
            new ResponseMapping( 'roles', 'roles', 'complex', new Response( $rolemap ) ),
        );
    }

}