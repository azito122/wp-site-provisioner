<?php

namespace WPSP;

class User {

    private $id;
    private $login;
    private $roles;

    public function __construct( $userdata ) {
        $this->id        = $userdata[ 'id' ];
        $this->login     = $userdata[ 'login' ];
        $this->firstname = $userdata[ 'firstname' ];
        $this->lastname  = $userdata[ 'lastname' ];
        $this->email     = $userdata[ 'email' ];
    }

    public function getId() {
        return $this->id;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function setRoles( $roles ) {
        $this->roles = $roles;
    }
}