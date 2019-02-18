<?php

namespace WPSP;

class User {

    use \WPSP\traits\GetterSetter;

    protected $id;
    protected $login;
    protected $firstname;
    protected $lastname;
    protected $email;
    protected $roles;

    public function __construct( $userdata ) {
        $userdata        = (array) $userdata;
        $this->id        = isset( $userdata[ 'ID' ] )         ? $userdata[ 'ID' ]         : '';
        $this->login     = isset( $userdata[ 'user_login' ] ) ? $userdata[ 'user_login' ] : '';
        $this->firstname = isset( $userdata[ 'first_name' ] ) ? $userdata[ 'first_name' ] : '';
        $this->lastname  = isset( $userdata[ 'last_name' ] )  ? $userdata[ 'last_name' ]  : '';
        $this->email     = isset( $userdata[ 'user_email' ] ) ? $userdata[ 'user_email' ] : '';
    }

    public function getFullname() {
        return $this->firstname . $this->lastname;
    }

    public function get_fullname() {
        return $this->getFullname();
    }
}