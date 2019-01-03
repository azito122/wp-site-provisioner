<?php

namespace WPSP\render;

include_once(__DIR__ . '/../GroupType.php');
include_once(__DIR__ . '/GroupTypeRenderer.php');
include_once(__DIR__ . '/QueryRenderer.php');
include_once(__DIR__ . '/RemoteRenderer.php');
include_once(__DIR__ . '/TemplateVariables.php');
include_once(__DIR__ . '/../infrastructure/Store.php');

abstract class Renderer {

    private $dataobject;

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
        return self::template( 'page-remotes', array( 'remotes' => $remotes ) );
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

    public static function textinput( $id, $label, $default = '', $placeholder = '' ) {
        $o = "<input type=\"text\" name=\"$id\" datakey=\"$id\" value=\"$default\" placeholder=\"$placeholder\">";
        return $o;
    }

    public static function entity( $data ) {
        if ( is_array( $data ) ) {
            $o = "";
            foreach( $data as $entity ) {
                $o .= self::entity( $entity );
            }
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