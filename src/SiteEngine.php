<?php

namespace WPSP;

abstract class SiteEngine {

    private $config;
    private $data;

    public function __construct__( $config ) {
        $this->config = $config;
    }

    public function createSite( $siteinfo = array() ) {

        $domain = 'd';
        $path = array_key_exists( 'path', $siteinfo ) ? $siteinfo[ 'path' ] : $this->resolveConfig( 'path' );
        $title = array_key_exists( 'title', $siteinfo[ 'title' ] ) ? $siteinfo[ 'title' ] : $this->resolveConfig( 'title' );
        $adminuserid = array_key_exists( 'adminuserid', $siteinfo[ 'adminuserid' ] ) ? $siteinfo[ 'adminuserid' ] : $this->resolveConfig( 'adminuserid' );

        $siteid = wpmu_create_blog( $domain, $path, $title, $adminuserid );
        update_blog_option( $siteid, 'blogdescription', $this->resolveConfig( 'tagline' ) );
    }

    public function deleteSite( $id ) {
        return wpmu_delete_blog( $id, true );
    }

    abstract public function updateSites(Userlist $userlist );

    public function updateSite( $id ) {
        $this->updateSiteAccess( $id, $userlist->getUsers() );

        $currenttitle = get_blog_option( $id, 'blogname' );
        $updatedtitle = $this->resolveConfig( 'title' );

        if ( $currenttitle != $updatedtitle ) {
            set_blog_option( $id, 'blogname', $this->resolveConfig( 'title' ) );
        }

        $currenttagline = get_blog_option( $id, 'blogdescription' );
        $updatedtagline = $this->resolveConfig( 'tagline' );

        if ( $currenttagline != $updatedtagline ) {
            set_blog_option( $id, 'blogdescription', $this->resolveConfig( 'tagline' ) );
        }
    }

    public function resolveConfig( $key, $extradata = array() ) {
        $data = array_merge( $this->data, $extradata );

        if ( ! array_key_exists( $key, $this->config ) ) {
            return null;
        }

        $cfgval = $this->config[ $key ];

        if ( preg_match( '/.*{(.*?)}.*/', $cfgval, $matches ) ) {
            foreach ( $matches as $match ) {
                if ( array_key_exists( $match, $data ) ) {
                    $cfgval = preg_replace( "/$match/", $data[ $match ], $cfgval );
                }
            }
        }

        return $cfgval;
    }
}