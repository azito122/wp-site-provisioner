<?php

namespace WPSP;

class User {

    protected $id;
    protected $login;
    protected $roles;

    public function __construct( $userdata ) {
        $this->id        = $userdata[ 'id' ];
        $this->login     = $userdata[ 'login' ];
        $this->firstname = $userdata[ 'firstname' ];
        $this->lastname  = $userdata[ 'lastname' ];
        $this->email     = $userdata[ 'email' ];
    }
}