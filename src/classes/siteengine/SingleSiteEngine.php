<?php

namespace WPSP\siteengine;

use WPSP\siteengine\SiteEngine as SiteEngine;
use WPSP\User as User;
use WPSP\UserList as UserList;

class SingleSiteEngine extends SiteEngine {

    use \WPSP\traits\GetterSetter;

    protected $siteid;
    protected $owner;
    protected $config = array(
        'path'    => '',
        'title'   => "{owner_firstname} {owner_lastname}'s Site",
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

    public function __construct( $ownerid = null ) {
        $owner = isset( $ownerid ) ? wp_get_user_by( 'ID', $ownerid ) : wp_get_current_user();
        $this->owner = new User( $owner->data );
    }

    public function update( UserList $userlist ) {
        $this->users = $userlist;

        if ( empty( $this->siteid ) ) {
            $this->siteid = $this->createSite();
        }
        $this->updateSite( $userlist );
    }

    public function deleteSite( $id ) {
        return wpmu_delete_blog( $id, true );
    }

    public function createSite( $siteinfo = array() ) {
        $domain = wp_parse_url( site_url(), PHP_URL_HOST );
        $path = $this->getConfig( 'path' );
        $title = $this->getConfig( 'title' );
        $adminuserid = $this->owner->id;

        $siteid = wpmu_create_blog( $domain, $path, $title, $adminuserid );
        update_blog_option( $siteid, 'blogdescription', $this->getConfig( 'tagline' ) );
        return $siteid;
    }

    public function updateSite( $userlist ) {
        // $this->updateSiteAccess( $userlist->getUsers() );

        $currenttitle = get_blog_option( $this->siteid, 'blogname' );
        $updatedtitle = $this->getConfig( 'title' );

        if ( $currenttitle != $updatedtitle ) {
            update_blog_option( $this->siteid, 'blogname', $updatedtitle );
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
                'owner_id'        => $o->id,
                'owner_login'     => $o->login,
                'owner_firstname' => $o->firstname,
                'owner_lastname'  => $o->lastname,
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