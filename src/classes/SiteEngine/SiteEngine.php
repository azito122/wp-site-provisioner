<?php

namespace WPSP\siteengine;

abstract class SiteEngine {

    protected $config;

    public function deleteSite( $id ) {
        return wpmu_delete_blog( $id, true );
    }

    abstract public function update(UserList $userlist );
}