<?php

namespace WPSP\render;

abstract class Writer {

    private static $defaultinputprops = array(
        'name' => '',
        'label' => '',
        'placeholder' => '',
        'default' => '',
        'required' => false,
    );

    public static function select( $props = array() ) {
        $props = array_merge( self::$defaultinputprops, $props );
        $name = $props[ 'name' ];
        $options = is_array( $props[ 'options' ] ) ? $props[ 'options' ] : array();
        $default = $props['default'];
        $label = $props['label'];

        $o = "<select name=\"$name\">";
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
        $default = $props['default'];
        $placeholder = $props['placeholder'];
        $required = $props['required'] ? 'required="required"' : '';
        $class = isset($props['class']) ? $props['class'] : '';
        $o = "<input class=\"$class\" type=\"text\" name=\"$name\" value=\"$default\" placeholder=\"$placeholder\" $required>";
        return $o;
    }

    public static function hidden( $id, $value ) {
        $o = "<input type=\"hidden\" name=\"$id\" value=\"$value\">";
        return $o;
    }
}