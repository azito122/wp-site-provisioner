<?php

namespace WPSP;

use User;
use UserList;

abstract class UserProvider {

    abstract public function getUsers();

}