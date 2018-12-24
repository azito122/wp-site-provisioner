<?php

namespace WPSP;

define( 'WPSP_TBLNAME', 'wpsp' );

class Store {

    public static function unstore( $type, $id = null ) {
        $conditions = array( 'type' => $type );
        if ( $id ) {
            $conditions[ 'id' ] = $id;
            $data = self::select( $conditions );
            return unserialize( $data[0]['data'] );
        } else {
            $results = array();
            $data = self::select( $conditions );
            foreach ( $data as $row ) {
                array_push( $results, unserialize( $row['data'] ) );
            }
            return $results;
        }
    }

    public static function store( $object ) {
        if ( ! property_exists( $object, 'storeid' ) ) {
            return false;
        }

        $id = $object->storeid;
        $serial = serialize( $object );

        if ( $id ) {
            self::update( $id, $serial );
        } else {
            $reflection =new \ReflectionClass( $object );
            $classname = $reflection->getShortName();
            self::insert( $classname, $serial );
        }
    }

    public static function update( $id, $serial ) {
        global $wpdb;

        $table_name = $wpdb->prefix . WPSP_TBLNAME;

        $update = array(
            'id' => $id,
            'data' => $serial,
        );

        return $wpdb->update( $table_name, $update, array( 'id' => $id ) );
    }

    public static function insert( $classname, $serial ) {
        global $wpdb;

        $hash = md5( $serial );

        $table_name = $wpdb->prefix . WPSP_TBLNAME;

        $insert = array(
            'id' => $hash,
            'type' => $classname,
            'data' => $serial,
        );

        return $wpdb->insert( $table_name, $insert );
    }

    public static function select( $conditions = array() ) {
        global $wpdb;

        $table_name = $wpdb->prefix . WPSP_TBLNAME;

        $conditionjoin = array();
        foreach ( $conditions as $key => $val ) {
            if ( is_string( $key ) && is_scalar( $val ) ) {
                $conditionjoin[] = "$key = '$val'";
            }
        }

        $conditionstring = implode( ' AND ', $conditionjoin );
        return $wpdb->get_results( "SELECT * FROM $table_name WHERE $conditionstring", ARRAY_A );
    }

}