<?php

namespace WPSP;

class UserList {

    use \WPSP\traits\GetterSetter;

    protected $users;

    public function __construct( $userdata ) {
        $users = array();
        foreach ( $userdata as $ud ) {
            array_push( $users, new User( $ud ) );
        }
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

    public function get_userids() {
        return array_map( function( $u ) {
            return $u->id;
        }, $this->users );
    }
}