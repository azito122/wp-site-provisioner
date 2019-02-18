<?php

namespace WPSP\siteengine;

use WPSP\siteengine\SiteEngine as SiteEngine;

class SingleSiteEngine extends SiteEngine {

    use \WPSP\traits\GetterSetter;

    protected $siteid;
    protected $owner;
    protected $config = array(
        'path'    => '',
        'title'   => '{owner_firstname} {owner_lastname}\'s Site',
        'tagline' => '',
    );

    protected $users;

    public function __sleep() {
        return array(
            'label',
            'siteid',
            'owner',
            'config',
            'grouptypemeta'
        );
    }

    public function __construct( $initialusers = null, $ownerid = null ) {
        if ( isset( $ownerid ) ) {
            $this->setOwner( $initialusers->findById( $ownerid ) );
        }
    }

    public function update( UserList $userlist ) {
        $this->users = $userlist;

        if ( empty( $this->siteid ) ) {
            $this->siteid = $this->createSite();
        }
        $this->updateSite( $userlist, $grouptypemeta );
    }

    public function deleteSite( $id ) {
        return wpmu_delete_blog( $id, true );
    }

    public function createSite( $siteinfo = array() ) {

        $domain = site_url();
        $path = array_key_exists( 'path', $siteinfo ) ? $siteinfo[ 'path' ] : $this->resolveSiteProp( 'path' );
        $title = array_key_exists( 'title', $siteinfo[ 'title' ] ) ? $siteinfo[ 'title' ] : $this->resolveSiteProp( 'title' );
        $adminuserid = array_key_exists( 'adminuserid', $siteinfo[ 'adminuserid' ] ) ? $siteinfo[ 'adminuserid' ] : $this->resolveOwner()->id;

        $siteid = wpmu_create_blog( $domain, $path, $title, $adminuserid );
        update_blog_option( $siteid, 'blogdescription', $this->resolveSiteProp( 'tagline' ) );
    }

    public function updateSite( $userlist, $grouptypemeta ) {
        $this->updateSiteAccess( $userlist->getUsers() );

        $currenttitle = get_blog_option( $this->siteid, 'blogname' );
        $updatedtitle = $this->resolveSiteProp( 'title' );

        if ( $currenttitle != $updatedtitle ) {
            set_blog_option( $this->siteid, 'blogname', $this->resolveSiteProp( 'title' ) );
        }

        $currenttagline = get_blog_option( $this->siteid, 'blogdescription' );
        $updatedtagline = $this->resolveSiteProp( 'tagline' );

        if ( $currenttagline != $updatedtagline ) {
            set_blog_option( $this->siteid, 'blogdescription', $this->resolveSiteProp( 'tagline' ) );
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

    public function resolveOwner() {
        if ( isset( $this->owner ) ) {
            return $this->owner;
        }

        $role = $this->config[ 'owner_role' ];
        $users = $this->users->findByRole( $role );
        return $users[0];
    }

    public function getConfig( $cfgkey, $resolve = true ) {
        $flagdata = $this->getFlagData();

        if ( ! array_key_exists( $cfgkey, $this->config ) ) {
            return null;
        }

        $cfgval = $this->config[ $cfgkey ];

        if ( $resolve ) {
            $matches = array();
            if ( preg_match_all( '/\{([a-zA-Z0-9_]*)\}/', $cfgval, $matches ) ) {
                foreach ( $matches[1] as $flag ) {
                    $replace = isset( $flagdata[ $flag ] ) ? $flagdata[ $flag ] : '';
                    $cfgval = preg_replace( "/\{$flag\}/", $replace, $cfgval );
                }
            }
        }

        return $cfgval;
    }

    public function setConfig( $cfgkey, $value ) {
        $this->config[ $cfgkey ] = $value;
    }
}