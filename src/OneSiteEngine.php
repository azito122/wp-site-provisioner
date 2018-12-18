<?php

namespace WPSP;

use SiteEngine;

class OneSiteEngine extends SiteEngine {

    private $siteid;

    public function updateSites( Userlist $userlist, $data ) {
        $this->data = $data;
        if ( empty( $this->siteid ) ) {
            $this->siteid = $this->createSite();
        }
        $this->updateSite( $this->siteid );
    }
}