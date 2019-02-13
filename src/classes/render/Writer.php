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

    private static function open_formwrapper( $type ) {
        return "<div class=\"form-wrapper $type-wrapper\">";
    }

    private static function close_formwrapper() {
        return '</div>';
    }

    public static function label( $props = array() ) {
        $name = isset($props['for']) ? $props['for'] : '';
        $text = isset($props['text']) ? $props['text'] : '';
        return '<label for="' . $name . '">' . $text . '</label>';
    }

    public static function select( $props = array() ) {
        $props = array_merge( self::$defaultinputprops, $props );
        $name = $props[ 'name' ];
        $options = is_array( $props[ 'options' ] ) ? $props[ 'options' ] : array();
        $default = $props['default'];
        $label = $props['label'];

        $o = '';
        $o .= self::open_formwrapper( 'select' );
        $o .= "<label for=\"$name\">$label</label>";
        $o .= "<select name=\"$name\">";
        foreach ( $options as $value => $opname ) {
            $isdefault = $default == $value ? 'selected="selected"' : '';
            $o .= "<option value=\"$value\" $isdefault>$opname</option>";
        }
        $o .= "</select>";
        $o .= self::close_formwrapper();
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
        $label = $props['label'];
        $default = $props['default'];
        $placeholder = $props['placeholder'];
        $required = $props['required'] ? 'required="required"' : '';
        $class = isset($props['class']) ? $props['class'] : '';
        $o = '';
        $o .= self::open_formwrapper( 'text' );
        $o .= $label ? self::label( [ 'for' => $name, 'text' => $label ] ) : '';
        $o .= "<input class=\"$class\" type=\"text\" name=\"$name\" value=\"$default\" placeholder=\"$placeholder\" $required>";
        $o .= self::close_formwrapper();
        return $o;
    }

    public static function button( $text, $href, $props = array() ) {
        $o = '';
        $o .= $href ? "<a href=\"$href\">" : "<a>";
        $o .= '<button';
        $props['class'] = isset( $props[ 'class' ] ) ? $props[ 'class' ] . ' button' : 'button';
        foreach ( $props as $propname => $propval ) {
            $o .= ' ' . $propname . '="' . $propval . '"';
        }
        $o .= ">$text</button>";
        return $o;
    }

    public static function hidden( $id, $value ) {
        $o = "<input type=\"hidden\" name=\"$id\" value=\"$value\">";
        return $o;
    }
}