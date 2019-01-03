<?php

namespace WPSP;

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
            return unserialize( $this->cache[ $id ][ 'data' ] );
        } else {
            $data = array_filter( $this->cache, function( $d ) use ( $type ) {
                return $d[ 'type' ] == $type;
            });
            $results = array();
            foreach ( $data as $entity ) {
                array_push( $results, unserialize( $entity[ 'data' ] ) );
            }
            return $results;
        }
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
        print_r($object);
        if ( ! property_exists( $object, 'storeid' ) ) {
            return false;
        }

        $id = $object->storeid;
        $serial = serialize( $object );

        if ( $id ) {
            return self::update( $id, $serial );
        } else {
            $reflection =new \ReflectionClass( $object );
            $classname = $reflection->getShortName();
            return self::insert( $classname, $serial );
        }
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