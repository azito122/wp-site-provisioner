<?php

namespace WPSP\siteengine;

use WPSP\UserList as UserList;

abstract class SiteEngine {

    protected $label;
    protected $config;
    protected $grouptypemeta = [];

    abstract public function update( UserList $userlist );
}