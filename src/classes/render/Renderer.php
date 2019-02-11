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

        $action = get_query_var( 'action', '' );

        if ( $action == 'add-group' ) {
            return Renderer::pageAddGroup();
        }

        $groupids = $Store->unstoreUserGroupIds();
        $mygroups = $Store->unstoreEntity( 'Group', $groupids );
        return Renderer::renderTemplate( 'page', 'entities', array(
            'existing-entities' => Renderer::renderEntity( $mygroups ),
            'entity-type'       => 'group',
            'entity-type-name'  => 'Group',
            'add-button-href'   => '?action=add-group',
        ) );
    }

    public static function pageAddGroup() {
        global $Store;

        $user = wp_get_current_user();
        $grouptypes = $Store->unstoreGroupTypesByUserRole( $user->roles );
        $grouptypeblocks = array();
        foreach ( $grouptypes as $grouptype ) {
            $possiblemetas = [];
            foreach ( $grouptype->generatePossibleMetas() as $possiblemeta ) {
                $possiblemetas[ $possiblemeta[ 'meta_id' ] ] = $possiblemeta[ 'meta_displayname' ];
            }
            array_push( $grouptypeblocks, Renderer::renderTemplate( 'special', 'my-group-type-block', array(
                'group-type-id' => $grouptype->storeid,
                'name' => $grouptype->label,
                'possible-metas' => $possiblemetas,
            )));
        }
        return Renderer::renderTemplate( 'page', 'add-group', [
            'group-type-blocks' => $grouptypeblocks,
        ]);
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