<?php

namespace WPSP;

use SiteEngine;

class MultiSiteEngine extends SiteEngine {

    private $sites = array();

    public function createSiteForUser( User $user ) {

        $siteinfo = array(
            'title' => $this->resolveConfig( 'title' ),
        );

    }

    // public function resolveConfig( )

    public function updateSites( Userlist $users ) {

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