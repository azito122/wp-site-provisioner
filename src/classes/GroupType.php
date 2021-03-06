<?php

namespace WPSP;

use WPSP\query\Query as Query;
use WPSP\query\response\UserResponse as UserResponse;
use WPSP\Group as Group;
use WPSP\query\response\QueryResponse as QueryResponse;
use WPSP\query\response\QueryResponseMapping as QueryResponseMapping;

class GroupType {

    use \WPSP\traits\GetterSetter;
    use \WPSP\traits\Storable;

    protected $label;
    protected $metaqueryid;
    protected $userqueryid;

    protected $metaquery;
    protected $userquery;

    protected $sleeplist = [
        'label',
        'metaqueryid',
        'userqueryid',
    ];

    public function __wakeup() {
        global $Store;
        $this->metaquery = $Store->unstoreEntity( 'Query', $this->metaqueryid );
        $this->userquery = $Store->unstoreEntity( 'Query', $this->userqueryid );
    }

    public function __construct() {
        $userresponse = new UserResponse();
        $this->userquery = new Query( 'User Query', $userresponse );
        $metamappings = array(
            new QueryResponseMapping( 'meta_id', 'id' ),
            new QueryResponseMapping( 'meta_displayname', 'displayname' ),
        );
        $metamap = new QueryResponse( $metamappings );
        $this->metaquery = new Query( 'Meta Query', $metamap );
    }

    public function makeGroup( $meta, $user = null ) {
        $user = isset( $user ) ? $user : wp_get_current_user();
        $group = new Group( $meta, $this->userqueryid );
        return $group;
    }

    public function generatePossibleMetas() {
        $options = array();
        $metadata = $this->loadMeta();

        foreach ( $metadata as $m ) {
            $options[ $m[ 'meta_id' ] ] = $m;
        }

        return $options;
    }

    public function loadUsers( $meta ) {
        $query = $this->userquery;
        $userdata = $query->run( $meta );
        if ( $this->config[ 'roledata' ] == 'userquery' ) {
            $userlist = new UserList( $userdata );
        } else if ( $this->config[ 'roledata' ] == 'rolequery' ) {
            // $rolequery = $this->getRoleQuery();
            // $roledata = $rolequery->run( $meta );
            // $joinfield = $this->config[ 'rolejoinfield' ];
            // foreach ( $userdata as $ud ) {
            //     $ud[ 'roles' ] =
            // }
            // $userdata = $this->
            // $userlist = new UserList( $userdata );
        }
        return $userlist;
    }

    public function loadMeta() {
        $currentuser = wp_get_current_user();

        return $this->metaquery->run( array(
            'userid' => $currentuser->ID,
            'userlogin' => $currentuser->login,
        ) );
    }

    // Getters & setters ------------------------
    public function set_metaquery( Query $query ) {
        $this->metaqueryid = $query->uid;
        $this->metaquery = $query;
    }

    public function set_userquery( Query $query ) {
        $this->userqueryid = $query->uid;
        $this->userquery = $query;
    }

    public function set_metaqueryid( $id ) {
        $this->metaqueryid = $id;
    }

    public function set_userqueryid( $id ) {
        $this->userqueryid = $id;
    }
    //+++++++++++++++++++++++++++++++++++++++++++
}