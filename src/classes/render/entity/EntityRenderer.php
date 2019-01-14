<?php

namespace WPSP\render\entity;

interface EntityRenderer {

    public static function render( $instance );

    public static function derender( $data );
}