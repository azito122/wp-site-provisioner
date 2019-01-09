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

    /**
     * Unstore a storable Entity.
     *
     * @param string $type A string representing the type of the entity (matches class shortname).
     * @param int|null $id The id of the entity to unstore.
     *
     * @return object|array The entity or entities matching the given criteria.
     */
    public function unstoreEntity( $type, $id = null ) {
        // Find which types we need to load for unserialization.
        $allreqtypes = $this->getRequiredTypes( $type );
        $reqtypes = array_diff( $allreqtypes, $this->cachedtypes );

        if ( count( $reqtypes ) > 0 ) {
            $this->cache( $this->getDataForTypes( $reqtypes ) );
            $this->setCachedTypes( $reqtypes );
        }

        if ( $id ) {
            // If unstoring a single entity, grab from cache.
            return $this->reconstitute( $this->cache[ $id ][ 'serial' ], $id );
        } else {
            // Get data for all matching type entries in the cache.
            $cacheentries = array_filter( $this->cache, function( $cacheentry ) use ( $type ) {
                return $cacheentry[ 'type' ] == $type;
            });
            $results = array();
            // Reconstitute and return.
            foreach ( $cacheentries as $cacheentry ) {
                array_push( $results, $this->reconstitute( $cacheentry[ 'serial' ], $cacheentry[ 'id' ] ) );
            }
            return $results;
        }
    }

    /**
     * Store a given serialized data object, with id and type.
     *
     * @param string $serial An object that has been serialized.
     * @param string $id The id to store this serialized data under.
     * @param string $type The type which identifies this serialized data (should match classname if applicable).
     */
    private function store( $serial, $type, $id = null ) {
        if ( $id ) {
            $storedid = self::update( $id, $serial );
        } else {
            $storedid = self::insert( $type, $serial );
        }

        return $storedid;
    }

    public function storeEntity( $object ) {
        // If this is not a storable entity, die.
        if ( ! property_exists( $object, 'storeid' ) ) {
            return false;
        }

        // Get entity type.
        $reflect = new \ReflectionClass( $object );
        $type = $reflect->getShortName();

        // Find any sub-entities that need stored first.
        if ( array_key_exists( $type, $this->subentitymap ) ) {
            foreach ( $this->subentitymap[ $type ] as $prop ) {
                // Store them and update parent object.
                $stored = $this->storeEntity( $object->{"get$prop"}() );
                $object->{"set$prop"}( $stored[ 'object' ] );
            }
        }

        // Get entity id and serial.
        $id = $object->storeid;
        $serial = serialize( $object );

        $storedid = $this->store( $serial, $type, $id );

        $object->storeid = $storedid;

        return array(
            'id' => $storedid,
            'object' => $object,
        );
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

    public function unstoreUserGroupIds( $user = null ) {
        if ( ! isset( $user ) ) {
            $user = wp_get_current_user();
        }
        $serial = $this->select( array( 'id' => $user->id, 'type' => 'usergroupids' ) )[ 'serial'];
        return unserialize( $serial );
    }

    public function storeUserGroupIds( $ids, $user = null ) {
        if ( ! isset( $user ) ) {
            $user = wp_get_current_user();
        }
        $serial = serialize( $ids );
        $this->store( $serial, 'usergroupids', $user->id );
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

    // public function store_grouptype( $object, $rerenderid = '' ) {
    //     global $Store;

    //     $metaqueryid = $Store->storeEntity( $object->getMetaQuery() );
    //     $userqueryid = $Store->storeEntity( $object->getUserQuery() );

    //     $object->setMetaQueryId( $metaqueryid );
    //     $object->setUserQueryId( $userqueryid );

    //     return $this->store( $object, $rerenderid );
    // }

    public function update( $id, $serial ) {

        global $wpdb;

        $update = array(
            'id' => $id,
            'serial' => $serial,
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
            'serial' => $serial,
        );

        $wpdb->insert( $this->tblname, $insert );
        return $hash;
    }

    public function select( $conditions = array(), $table = null ) {
        global $wpdb;

        if ( ! isset( $table ) ) {
            $table = $this->tblname;
        }

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