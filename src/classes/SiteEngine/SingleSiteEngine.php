<?php

namespace WPSP\siteengine;

use SiteEngine;
use UserList;

class SingleSiteEngine extends SiteEngine {

    private $siteid;
    private $owner;

    public function __construct( $initialusers, $ownerid = null ) {
        if ( isset( $ownerid ) ) {
            $this->setOwner( $initialusers->getById( $ownerid ) );
        }
    }

    public function update( UserList $userlist ) {
        if ( empty( $this->siteid ) ) {
            $this->siteid = $this->createSite();
        }
        $this->updateSite( $userlist, $grouptypemeta );
    }

    public function createSite( $siteinfo = array() ) {

        $domain = 'd';
        $path = array_key_exists( 'path', $siteinfo ) ? $siteinfo[ 'path' ] : $this->getConfig( 'path' );
        $title = array_key_exists( 'title', $siteinfo[ 'title' ] ) ? $siteinfo[ 'title' ] : $this->getConfig( 'title' );
        $adminuserid = array_key_exists( 'adminuserid', $siteinfo[ 'adminuserid' ] ) ? $siteinfo[ 'adminuserid' ] : $this->getConfig( 'adminuserid' );

        $siteid = wpmu_create_blog( $domain, $path, $title, $adminuserid );
        update_blog_option( $siteid, 'blogdescription', $this->getConfig( 'tagline' ) );
    }

    public function updateSite( $userlist, $grouptypemeta ) {
        $this->updateSiteAccess( $userlist->getUsers() );

        $currenttitle = get_blog_option( $this->siteid, 'blogname' );
        $updatedtitle = $this->getConfig( 'title' );

        if ( $currenttitle != $updatedtitle ) {
            set_blog_option( $this->siteid, 'blogname', $this->getConfig( 'title' ) );
        }

        $currenttagline = get_blog_option( $this->siteid, 'blogdescription' );
        $updatedtagline = $this->getConfig( 'tagline' );

        if ( $currenttagline != $updatedtagline ) {
            set_blog_option( $this->siteid, 'blogdescription', $this->getConfig( 'tagline' ) );
        }
    }

    public function updateSiteAccess() {

    }

    public function getFlagData() {
        $ownerdata = $this->getOwnerData() ? $this->getOwnerData() : array();
        return array_merge( $this->grouptypemeta, $ownerdata );
    }

    public function getOwnerData() {
        if ( $o = $this->owner ) {
            return array(
                'id' => $o->getId(),
                'login' => $o->getLogin(),
            );
        }
        return null;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setOwner( User $owner ) {
        $this->owner = $owner;
    }

    public function getConfig( $key ) {
        $flagdata = $this->getFlagData();

        if ( ! array_key_exists( $key, $this->config ) ) {
            return null;
        }

        $cfgval = $this->config[ $key ];

        if ( preg_match( '/.*{(.*?)}.*/', $cfgval, $matches ) ) {
            foreach ( $matches as $match ) {
                if ( array_key_exists( $match, $flagdata ) ) {
                    $cfgval = preg_replace( "/$match/", $flagdata[ $match ], $cfgval );
                }
            }
        }

        return $cfgval;
    }
}