<?php

namespace WPSP;

class User {

    use \WPSP\traits\GetterSetter;

    protected $id;
    protected $login;
    protected $roles;

    public function __construct( $userdata ) {
        $userdata        = (array) $userdata;
        $this->id        = $userdata[ 'ID' ];
        $this->login     = $userdata[ 'user_login' ];
        $this->firstname = $userdata[ 'first_name' ];
        $this->lastname  = $userdata[ 'last_name' ];
        $this->email     = $userdata[ 'user_email' ];
    }

    public function getFullname() {
        return $this->firstname . $this->lastname;
    }

    public function get_fullname() {
        return $this->getFullname();
    }
}