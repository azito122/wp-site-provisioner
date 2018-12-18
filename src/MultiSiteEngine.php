<?php

namespace WPSP;

use SiteEngine;
use SingleSiteEngine;

class MultiSiteEngine extends SiteEngine {

    private $filter;
    private $siteengines = array();

    public function __construct__( $initialusers, $filter = null ) {
        if ( $filter ) {
            $this->filter = $filter;
        }
    }

    public function createSiteForUser( User $user ) {
        $this->sitengines[ $user->getId() ] = new SingleSiteEngine( $user->getId() );
    }

    public function update( UserList $users ) {
        $updateduserids = $users->getUserIds();
        $currentuserids = array_keys( $this->siteengines );

        $toadd = array_diff( $updateduserids, $currentuserids );
        $toremove = array_diff( $currentuserids, $updateduserids );

        foreach ( $toadd as $userid ) {
            $siteid = $this->createSiteForUser( $users->getById( $userid ) );
        }

        foreach ( $toremove as $userid ) {
            $this->deleteSite( $this->siteengines[ $userid ] );
            unset( $this->siteengines[ $userid ] );
        }

        foreach ( $this->siteengines as $userid => $siteengine ) {
            $this->updateUserSite( $siteengine, $users, $userid );
        }
    }

    public function updateUserSite( SiteEngine $siteengine, UserList $users, $ownerid ) {
        $siteengine->update( new UserList( $users->getById( $ownerid ) ) );
    }
}