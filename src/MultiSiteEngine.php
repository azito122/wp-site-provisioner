<?php

namespace WPSP;

use SiteEngine;
use SingleSiteEngine;

class MultiSiteEngine extends SiteEngine {

    private $siteengines = array();

    public function createSiteForUser( User $user ) {

        $this->sitengines[] = new SingleSiteEngine( $this->config, $user );

    }

    public function updateSites( UserList $users ) {

        $updateduserids = $users->getUserIds();
        $currentuserids = array_keys( $this->sites );

        $toadd = array_diff( $updateduserids, $currentuserids );
        $toremove = array_diff( $currentuserids, $updateduserids );

        foreach ( $toadd as $userid ) {
            $siteid = $this->createSiteForUser( $users->getById( $userid ) );
            $this->sites[ $userid ] = $siteid;
        }

        foreach ( $toremove as $userid ) {
            $this->deleteSite( $this->sites[ $userid ] );
            unset( $this->sites[ $userid ] );
        }

        foreach ( $this->sites as $userid => $siteid ) {
            $this->updateSite( $siteid, $users );
        }
    }
}