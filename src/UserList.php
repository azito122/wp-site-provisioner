<?php

namespace WPSP;

class UserList {

    private $users;

    public function __construct( $users ) {
        if ( $user instanceof User ) {
            $this->users = array( $user );
        } else if ( is_array( $users ) ) {
            $this->$users = $users;
        }
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

    public function getUsers() {
        return $this->users;
    }

    public function getUserIds() {
        return array_map( function( $u ) {
            return $u->getId();
        }, $this->users );
    }
}