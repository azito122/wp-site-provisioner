<?php

namespace WPSP;

class User {

    private $id;
    private $login;
    private $roles;

    public function __construct__($id, $login, $roles) {
        $this->id = $id;
        $this->login = $login;
        $this->roles = $roles;
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
}