<?php

namespace WPSP\render;

include_once(__DIR__ . '/../GroupType.php');
include_once(__DIR__ . '/GroupTypeRenderer.php');
include_once(__DIR__ . '/TemplateVariables.php');

abstract class Renderer {

    private $dataobject;

    abstract public function __construct( $object );

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

    public static function textinput( $id, $label, $default = '' ) {
        return '<input type="text" name="' . $id . '" value="' . $default . '">';
    }

    public static function entity( $object ) {
        $reflect = new \ReflectionClass( $object );
        $classname = "\WPSP\\render\\" . $reflect->getShortName() . "Renderer";
        if ( class_exists( $classname ) ) {
            return $classname::render( $object );
        }
        return '';
    }

    public static function template( $name, $data = array() ) {
        ob_start();
        $D = new \WPSP\render\TemplateVariables( $data );
        $R = __CLASS__;
        include __DIR__ . '/../templates/' . $name . '.php';
        return ob_get_clean();
    }

    // public static function tplvar( $data, $name, $default = '' ) {
    //     if ( array_key_exists( $name, $data ) ) {
    //         return $data[ $name ];
    //     }
    //     return $default;
    // }
}