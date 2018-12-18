<?php

namespace WPSP;

class UserList {

    private $users;

    public function __construct__( $users ) {
        $this->users = $users;
    }

    public function add( User $user ) {
        array_push( $this->users, $user );
    }

    public function remove( $userid ) {
        if ( is_numeric( $userid ) ) {
            // remove by id
        } else if ( is_string( $userid ) ) {
            // remove by login
        } else {
            return;
        }
    }
}