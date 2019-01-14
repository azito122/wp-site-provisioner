<?php

namespace WPSP\render;

use WPSP\render\TemplateVariables as TemplateVariables;

// include_once( __DIR__ . '/../Store.php');

abstract class Renderer {

    private static $defaultinputprops = array(
        'name' => '',
        'label' => '',
        'placeholder' => '',
        'default' => '',
        'required' => false,
    );

    abstract public function __construct( $object );

    public static function derender( $data, $type  = '' ) {
        $classname = "\WPSP\\render\\{$type}Renderer";

        if ( class_exists( $classname ) ) {
            return $classname::derender( $data );
        }
    }

    public static function pageGroupTypes() {
        global $Store;

        $grouptypes = self::entity( $Store->unstoreEntity( 'GroupType' ) );
        return self::template( 'page-entities', array(
                'existing-entities' => $grouptypes,
                'entity-type' => 'group-type',
                'entity-type-name' => 'Group Type',
            )
        );
    }

    public static function pageRemotes() {
        global $Store;

        $remotes = self::entity( $Store->unstoreEntity( 'Remote' ) );
        return self::template( 'page-entities', array(
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
        return self::template( 'page-entities', array(
            'existing-entities' => $mygroups,
            'entity-type'       => 'group',
            'entity-type-name'  => 'Group',
        ) );
    }

    public static function select( $props = array() ) {
        $props = array_merge( self::$defaultinputprops, $props );
        $name = $props[ 'name' ];
        $datakey = $props['name'];
        $options = is_array( $props[ 'options' ] ) ? $props[ 'options' ] : array();
        $default = $props['default'];
        $label = $props['label'];

        $o = "<select name=\"$name\" datakey=\"$datakey\">";
        foreach ( $options as $value => $opname ) {
            $isdefault = $default == $value ? 'selected="selected"' : '';
            $o .= "<option value=\"$value\" $isdefault>$opname</option>";
        }
        $o .= "</select>";
        $o .= "<label for=\"$name\">$label</label>";
        return $o;
    }

    public static function checkbox( $props = array() ) {
        $props = array_merge( self::$defaultinputprops, $props );
        $label = $props['label'];
        $default = $props['default'];
        $name = $props[ 'name' ];

        $checked = $default ? 'checked' : '';
        $o = "<input type=\"checkbox\" name=\"$name\" $checked>";
        $o .= "<label for=\"$name\">$label</label>";
        return $o;
    }

    public static function textinput( $props = array() ) {
        $props = array_merge( self::$defaultinputprops, $props );
        $name = $props['name'];
        $datakey = $props['name'];
        $default = $props['default'];
        $placeholder = $props['placeholder'];
        $required = $props['required'] ? 'required="required"' : '';
        $o = "<input type=\"text\" name=\"$name\" datakey=\"$datakey\" value=\"$default\" placeholder=\"$placeholder\" $required>";
        return $o;
    }

    public static function hidden( $id, $value ) {
        $o = "<input type=\"hidden\" name=\"$id\" datakey=\"$id\" value=\"$value\">";
        return $o;
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

    public static function entity( $data ) {
        if ( is_array( $data ) ) {
            $o = "";
            foreach( $data as $entity ) {
                $o .= self::entity( $entity );
            }
            return $o;
        } else {
            $reflect = new \ReflectionClass( $data );
            $classname = "\WPSP\\render\\" . $reflect->getShortName() . "Renderer";
            if ( class_exists( $classname ) ) {
                return $classname::render( $data );
            }
            return '';
        }
    }

    public static function template( $name, $data = array() ) {
        ob_start();
        $D = new TemplateVariables( $data );
        $R = __CLASS__;
        include __DIR__ . '/../../templates/' . $name . '.php';
        return ob_get_clean();
    }

    // public static function tplvar( $data, $name, $default = '' ) {
    //     if ( array_key_exists( $name, $data ) ) {
    //         return $data[ $name ];
    //     }
    //     return $default;
    // }
}