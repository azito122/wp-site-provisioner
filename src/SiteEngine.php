<?php

namespace WPSP;

abstract class SiteEngine {

    private $config;

    public function deleteSite( $id ) {
        return wpmu_delete_blog( $id, true );
    }

    abstract public function update(UserList $userlist );
}