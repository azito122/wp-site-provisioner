<?php

namespace WPSP\render\entity;

interface InterfaceEntityRenderer {

    public static function render( $instance );

    public static function derender( $data );
}