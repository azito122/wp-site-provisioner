<?php

namespace WPSP\render;

use WPSP\render\TemplateVariables as TemplateVariables;

abstract class Renderer {

    public static function derenderEntity( $data, $type  = '' ) {
        $classname = "\WPSP\\render\\entity\\{$type}Renderer";

        if ( class_exists( $classname ) ) {
            return $classname::derender( $data );
        }
    }

    public static function renderEntity( $data ) {
        if ( is_array( $data ) ) {
            $o = "";
            foreach( $data as $entity ) {
                $o .= Renderer::renderEntity( $entity );
            }
            return $o;
        } else {
            $reflect = new \ReflectionClass( $data );
            $classname = "\WPSP\\render\\entity\\" . $reflect->getShortName() . "Renderer";
            if ( class_exists( $classname ) ) {
                return $classname::render( $data );
            }
            return '';
        }
    }

    public static function renderTemplate( $type, $name, $data = array() ) {
        $typecheck = file_exists( __DIR__ . "/../../templates/$type" );
        $namecheck = file_exists( __DIR__ . "/../../templates/$type/{$type}_$name.php");
        if ( ! $typecheck ) {
            $trace = debug_backtrace();
            trigger_error(
                'Failed to find template type: ' . "$type/$name" .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
            return '';
        } else if ( ! $namecheck ) {
            $trace = debug_backtrace();
            trigger_error(
                'Failed to find template name: ' . "$type/$name" .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
            return '';
        }
        ob_start();
        $D = new TemplateVariables( $data );
        $R = __CLASS__;
        $W = '\WPSP\render\Writer';
        include __DIR__ . "/../../templates/$type/{$type}_$name.php";
        return ob_get_clean();
    }

    public static function pageGroupTypes() {
        global $Store;

        $grouptypes = self::renderEntity( $Store->unstoreEntity( 'GroupType' ) );
        return self::renderTemplate( 'page', 'entities', array(
                'existing-entities' => $grouptypes,
                'entity-type' => 'group-type',
                'entity-type-name' => 'Group Type',
            )
        );
    }

    public static function pageRemotes() {
        global $Store;

        $remotes = self::renderEntity( $Store->unstoreEntity( 'Remote' ) );
        return self::renderTemplate( 'page', 'entities', array(
                'existing-entities' => $remotes,
                'entity-type' => 'remote',
                'entity-type-name' => 'Remote',
            )
        );
    }

    public static function pageMyGroups() {
        global $Store;

        $groupids = $Store->unstoreEntityUserGroupIds();
        $mygroups = $Store->unstoreEntity( 'Group', $groupids );
        // $grouptypes = $Store->unstoreEntity( 'GroupType' );
        // $grouptypemenus = array();
        // foreach ( $grouptypes as $grouptype ) {
        //     array_push( $grouptypemenus, $grouptype->generatePossibleMetas );
        // }
        return Renderer::renderTemplate( 'page', 'entities', array(
            'existing-entities' => $mygroups,
            'entity-type'       => 'group',
            'entity-type-name'  => 'Group',
        ) );
    }

    public static function classnameFrontToBack( $string ) {
        $string = ucwords( $string, '-' );
        $string = str_replace( '-', '', $string );
        return $string;
    }

    public static function classnameBackToFront( $string ) {
        $string = preg_replace( '/(?<!^)([A-Z])/', '-$1', $string );
        $string = strtolower( $string );
        return $string;
    }

    // public static function tplvar( $data, $name, $default = '' ) {
    //     if ( array_key_exists( $name, $data ) ) {
    //         return $data[ $name ];
    //     }
    //     return $default;
    // }
}