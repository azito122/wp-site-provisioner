<?php

namespace WPSP\siteengine;

use WPSP\siteengine\SiteEngine as SiteEngine;
use WPSP\siteengine\SingleSiteEngine as SingleSiteEngine;

class MultiSiteEngine extends SiteEngine {

    use \WPSP\traits\GetterSetter;

    protected $filter;
    protected $config = array(
        'each' => 'user',
    );
    protected $siteengines = array();

    public function __construct( $initialusers, $filter = null ) {
        if ( $filter ) {
            $this->filter = $filter;
        }
    }

    public function createSiteForUser( User $user ) {
        $this->sitengines[ $user->id ] = new SingleSiteEngine( $user->id );
    }

    public function update( UserList $users ) {
        $updateduserids = $users->userids;
        $currentuserids = array_keys( $this->siteengines );

        $toadd = array_diff( $updateduserids, $currentuserids );
        $toremove = array_diff( $currentuserids, $updateduserids );

        foreach ( $toadd as $userid ) {
            $siteid = $this->createSiteForUser( $users->findById( $userid ) );
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
        $siteengine->update( new UserList( $users->findById( $ownerid ) ) );
    }
}