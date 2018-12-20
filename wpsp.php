<?php
/**
 * Plugin Name:     Wpsp
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     wpsp
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wpsp
 */
namespace WPSP;

include_once(__DIR__ . '/src/render/Renderer.php');
include_once(__DIR__ . '/src/GroupType.php');

class SiteProvisioner {
    public function __construct() {
        add_shortcode('wp_site_provisioner', array($this, 'shortcode'));
        add_action('wp_ajax_wpsp_render', array( $this, 'ajax_render' ) );
        add_action( 'init', array( $this, 'js_init' ) );
    }

    public function js_init() {
        wp_register_script( 'wpsp_main', WP_PLUGIN_URL . '/wpsp/src/js/main.js', array( 'jquery' ) );
        wp_localize_script( 'wpsp_main', 'WPSP_AJAX', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        wp_enqueue_script( 'wpsp_main' );
    }

    public function ajax_render() {
        // $_REQUEST = array(
        //     'type' => 'entity',
        //     'entity' => 'GroupType',
        // );
        $type = $_REQUEST[ 'type' ];
        if ( $type == 'template' ) {
            echo render\Renderer::template( $template );
        } else if ( $type == 'entity' ) {
            $classname = '\WPSP\\' . $_REQUEST[ 'entity' ];
            $id = array_key_exists( 'entityid', $_REQUEST ) ? $_REQUEST[ 'entityid' ] : false;
            if ( $id ) {
                $object = Store::getEntity( $classname, $id );
            } else {
                $object = new $classname();
            }
            echo render\Renderer::entity( $object );
        }
        die();
    }

    public function create_database_tables() {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'wpsp_grouptypes';

        $sql = "CREATE TABLE $table_name(
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                label varchar(255) NOT NULL,
                ) $charset_collate;";

        dbDelta( $sql );
    }

    public function shortcode() {
        if( !is_user_logged_in()) {
            $login_url = wp_login_url( get_permalink() );
            echo sprintf( __( 'You do not have access to this page.' ), $login_url );
        }
        else {
            $this->admin_form();
        }
    }

    public function admin_form() {
        echo render\Renderer::template( 'group-types' );
    }
}

new SiteProvisioner();

