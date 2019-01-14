<?php

namespace WPSP\siteengine;

use WPSP\siteengine\SiteEngine as SiteEngine;

class SingleSiteEngine extends SiteEngine {

    protected $siteid;
    protected $owner;
    protected $grouptypemeta;
    protected $config = array(
        'path'    => '',
        'title'   => '{owner_firstname} {owner_lastname}\'s Site',
        'tagline' => '',
    );

    public function __construct( $initialusers, $ownerid = null ) {
        if ( isset( $ownerid ) ) {
            $this->setOwner( $initialusers->findById( $ownerid ) );
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
        $adminuserid = array_key_exists( 'adminuserid', $siteinfo[ 'adminuserid' ] ) ? $siteinfo[ 'adminuserid' ] : $this->getConfig( 'owner_id' );

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
                'owner_id'        => $o->getId(),
                'owner_login'     => $o->getLogin(),
                'owner_firstname' => $o->getFirstname(),
                'owner_lastname'  => $o->getLastname(),
                'owner_fullname'  => $o->getFullname(),
            );
        }
        return null;
    }

    public function getConfig( $cfgkey ) {
        $flagdata = $this->getFlagData();

        if ( ! array_key_exists( $cfgkey, $this->config ) ) {
            return null;
        }

        $cfgval = $this->config[ $cfgkey ];

        $matches = array();
        if ( preg_match( '/.*{([a-zA-Z0-9_]*)}.*/', $cfgval, $matches ) ) {
            foreach ( $matches as $flag ) {
                if ( array_key_exists( $flag, $flagdata ) ) {
                    $cfgval = preg_replace( "/{$flag}/", $flagdata[ $flag ], $cfgval );
                }
            }
        }

        return $cfgval;
    }
}