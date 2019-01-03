<?php

namespace WPSP\render;

include_once(__DIR__ . '/../GroupType.php');
include_once(__DIR__ . '/GroupTypeRenderer.php');
include_once(__DIR__ . '/QueryRenderer.php');
include_once(__DIR__ . '/RemoteRenderer.php');
include_once(__DIR__ . '/TemplateVariables.php');
include_once(__DIR__ . '/../infrastructure/Store.php');

abstract class Renderer {

    private static $defaultinputprops = array(
        'name' => '',
        'label' => '',
        'placeholder' => '',
        'default' => '',
        'required' => false,
    );

    abstract public function __construct( $object );

    public static function derender( $type, $data ) {
        $classname = "\WPSP\\render\\{$type}Renderer";

        if ( class_exists( $classname ) ) {
            return $classname::derender( $type, $data );
        }
    }

    public static function pageGroupTypes() {
        global $Store;

        $grouptypes = self::entity( $Store->unstore( 'GroupType' ) );
        return self::template( 'page-group-types', array( 'group-types' => $grouptypes ) );
    }

    public static function pageRemotes() {
        global $Store;

        $remotes = self::entity( $Store->unstore( 'Remote' ) );
        return self::template( 'page-entities', array(
                'existing-entities' => $remotes,
                'entity-type' => 'remote',
                'entity-type-name' => 'Remote',
            )
        );
    }

    public static function select( $id, $label, $options, $default ) {
        $o = "<select name=\"$id\" id=\"$id\">";
        foreach ( $options as $value => $name ) {
            $default = $default == $value ? 'select="selected"' : '';
            $o .= "<option value=\"$value\" $default>$name</option>";
        }
        $o .= "</select>";
        $o .= "<label for=\"$id\">$label</label>";
        return $o;
    }

    public static function checkbox( $label, $name, $default = 0 ) {
        $checked = $default ? 'checked' : '';
        $o = '<input type="checkbox" ' . $checked . ' >';
        $o .= '<label for="' . $name . '">' . $label . '</label>';
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
        return $string;
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
        $D = new \WPSP\render\TemplateVariables( $data );
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