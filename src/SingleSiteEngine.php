<?php

namespace WPSP;

use SiteEngine;

class SingleSiteEngine extends SiteEngine {

    private $siteid;

    public function updateSites( UserList $userlist, $data ) {
        $this->data = $data;
        if ( empty( $this->siteid ) ) {
            $this->siteid = $this->createSite();
        }
        $this->updateSite( $this->siteid );
    }
}