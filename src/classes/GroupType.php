<?php

namespace WPSP;

include_once(__DIR__ . '/Query/Query.php');

class GroupType {

    private $label;
    private $metaqueryid;
    private $userqueryid;

    private $metaquery;
    private $userquery;

    public function __sleep() {
        return array(
            'label',
            'metaqueryid',
            'userqueryid',
        );
    }

    public function __wakeup() {
        global $Store;
        $this->metaquery = $Store->unstore( 'Query', $this->metaqueryid );
        $this->userquery = $Store->unstore( 'Query', $this->userqueryid );
    }

    public function __construct() {
        $this->userquery = new Query();
        $this->metaquery = new Query();
    }

    public function makeGroup( $meta ) {
        $group = new Group( $meta, $this->userqueryid );
        return $group;
    }

    public function getPossibleMetas() {
        $options = array();
        $currentuser = wp_get_current_user();
        $metadata = $this->getMetaQuery()->run( array(
            'userid' => $currentuser->ID,
            'userlogin' => $currentuser->login,
        ));

        foreach ( $metadata as $m ) {
            array_push( $options, $m );
        }
        return $options;
    }

    public function getLabel() {
        return $this->label;
    }

    public function setLabel( $label ) {
        if ( is_string( $label ) && ! empty( $label ) ) {
            $this->label = $label;
        }
    }

    public function getMetaQuery() {
        return $this->metaquery;
    }

    public function setMetaQuery( Query $query ) {
        $this->metaqueryid = $query->storeid;
        $this->metaquery = $query;
    }

    public function getUserQuery() {
        return $this->userquery;
    }

    public function setUserQuery( Query $query ) {
        $this->userqueryid = $query->storeid;
        $this->userquery = $query;
    }

    public function getData() {
        return $this->metaquery->run();
    }
}