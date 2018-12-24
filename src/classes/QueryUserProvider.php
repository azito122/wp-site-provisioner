<?php

namespace WPSP;

use UserProvider;
use UserList;
use Query;

class QueryUserProvider extends UserProvider {

    private $query;

    public function construct( Query $query ) {
        $this->query = $query;
    }

    public function getUsers() {
        $userdata = $this->query->run();
        return new UserList( $userdata );
    }

}