<?php

namespace WPSP;

use WPSP\query\Query as Query;

define( 'WPSP_TBLNAME', 'wpsp' );

class Store {

    private $cache = array();
    private $cachedtypes = array();
    private $tblname;

    private $typemap = array(
        'Group' => array(
            'Query',
        ),
        'GroupType' => array(
            'Query',
        ),
        'Query' => array(
            'Remote',
        )
    );

    private $subentitymap = array(
        'GroupType' => array(
            'MetaQuery',
            'UserQuery',
        )
    );

    public function __construct() {
        global $wpdb;

        $this->tblname = $wpdb->prefix . WPSP_TBLNAME;
    }

    public function unstore( $type, $id = null ) {
        $allreqtypes = array_merge( $this->getRequiredTypes( $type ) );
        $reqtypes = array_diff( $allreqtypes, $this->cachedtypes );

        if ( count( $reqtypes ) > 0 ) {
            $this->cache( $this->getDataForTypes( $reqtypes ) );
            $this->setCachedTypes( $reqtypes );
        }

        if ( $id ) {
            return $this->reconstitute( $this->cache[ $id ][ 'data' ], $id );
        } else {
            $data = array_filter( $this->cache, function( $d ) use ( $type ) {
                return $d[ 'type' ] == $type;
            });
            $results = array();
            foreach ( $data as $entity ) {
                array_push( $results, $this->reconstitute( $entity[ 'data' ], $entity[ 'id' ] ) );
            }
            return $results;
        }
    }

    public function reconstitute( $serial, $id ) {
        $object = unserialize( $serial );
        $object->storeid = $id;
        return $object;
    }

    public function getDataForTypes( $types ) {
        return $this->select( array( 'type' => $types ) );
    }

    public function cache( $data ) {
        $processed = array();
        foreach ( $data as $entity ) {
            $processed[ $entity[ 'id' ] ] = $entity;
        }
        $this->cache = array_merge( $this->cache, $processed );
    }

    public function setCachedTypes( $types ) {
        foreach ( $types as $type ) {
            if ( ! in_array( $type, $this->cachedtypes ) ) {
                array_push( $this->cachedtypes, $type );
            }
        }
    }

    public function getRequiredTypes( $type ) {
        $result = array( $type );
        if ( array_key_exists( $type, $this->typemap ) ) {
            foreach ( $this->typemap[ $type ] as $rt ) {
                $result = array_merge( $result, $this->getRequiredTypes( $rt ) );
            }
        }
        return $result;
    }

    public function store( $object ) {
        if ( ! property_exists( $object, 'storeid' ) ) {
            return false;
        }

        $reflect = new \ReflectionClass( $object );
        $classname = $reflect->getShortName();

        if ( array_key_exists( $classname, $this->subentitymap ) ) {
            foreach ( $this->subentitymap[ $classname ] as $prop ) {
                $stored = $this->store( $object->{"get$prop"}() );
                $object->{"set$prop"}( $stored[ 'object' ] );
            }
        }

        $id = $object->storeid;
        $serial = serialize( $object );

        if ( $id ) {
            $storedid = self::update( $id, $serial );
        } else {
            $reflection =new \ReflectionClass( $object );
            $classname = $reflection->getShortName();
            $storedid = self::insert( $classname, $serial );
        }

        $object->storeid = $storedid;

        return array(
            'id' => $storedid,
            'object' => $object,
        );
    }

    public function store_grouptype( $object, $rerenderid = '' ) {
        global $Store;

        $metaqueryid = $Store->store( $object->getMetaQuery() );
        $userqueryid = $Store->store( $object->getUserQuery() );

        $object->setMetaQueryId( $metaqueryid );
        $object->setUserQueryId( $userqueryid );

        return $this->store( $object, $rerenderid );
    }

    public function update( $id, $serial ) {
        global $wpdb;

        $update = array(
            'id' => $id,
            'data' => $serial,
        );

        $wpdb->update( $this->tblname, $update, array( 'id' => $id ) );
        return $id;
    }

    public function insert( $classname, $serial ) {
        global $wpdb;

        $hash = md5( $serial );

        $check = $this->select( array( 'id' => $hash ) );

        if ( ! empty( $check ) ) {
            return $this->update( $hash, $serial );
        }

        $insert = array(
            'id' => $hash,
            'type' => $classname,
            'data' => $serial,
        );

        $wpdb->insert( $this->tblname, $insert );
        return $hash;
    }

    public function select( $conditions = array() ) {
        global $wpdb;

        $conditionjoin = array();
        foreach ( $conditions as $key => $val ) {
            if ( is_string( $key ) && is_scalar( $val ) ) {
                $conditionjoin[] = "$key = '$val'";
            } else if ( is_string( $key ) && is_array( $val ) ) {
                $or = "$key = '" . implode( "' OR $key = '", $val );
                $conditionjoin[] = "($or')";
            }
        }

        $conditionstring = implode( ' AND ', $conditionjoin );
        return $wpdb->get_results( "SELECT * FROM $this->tblname WHERE $conditionstring", ARRAY_A );
    }

}

global $Store;
$Store = new Store();