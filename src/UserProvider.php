<?php

namespace WPSP;

use User;
use UserList;

abstract class UserProvider {

    private $userlist;

    abstract public function getUsers();

}