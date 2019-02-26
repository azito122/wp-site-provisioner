<?php

namespace WPSP\siteengine;

use WPSP\UserList as UserList;

abstract class SiteEngine {

    use \WPSP\traits\GetterSetter;
    use \WPSP\traits\Storable;

    protected $label;
    protected $config;
    protected $grouptypemeta = [];

    abstract public function update( UserList $userlist );
}