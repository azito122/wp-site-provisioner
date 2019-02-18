<?php

namespace WPSP\siteengine;

abstract class SiteEngine {

    protected $label;
    protected $config;
    protected $grouptypemeta;

    abstract public function update(UserList $userlist );
}