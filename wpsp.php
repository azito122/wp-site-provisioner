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

include_once(__DIR__ . '/src/classes/render/Renderer.php');
include_once(__DIR__ . '/src/classes/GroupType.php');
include_once(__DIR__ . '/src/classes/Group.php');
include_once(__DIR__ . '/src/classes/infrastructure/Store.php');
include_once(__DIR__ . '/src/classes/Remote.php');

class SiteProvisioner {
    public function __construct() {
        $this->add_shortcode( 'group_types' );
        $this->add_shortcode( 'settings' );
        $this->add_shortcode( 'remotes' );
        $this->add_shortcode( 'my_groups' );
        $this->add_shortcode( 'debug' );
        add_action('wp_ajax_wpsp_render', array( $this, 'ajax_render' ) );
        add_action('wp_ajax_wpsp_store', array( $this, 'ajax_store' ) );
        add_action( 'init', array( $this, 'js_init' ) );
        add_action( 'init', array( $this, 'css_init') );

        register_activation_hook( __FILE__, array( $this, 'create_database_tables' ) );
        $this->cron_init();
    }

    public function add_shortcode( $name ) {
        add_shortcode("wpsp_$name", function() use ( $name ) {
            $this->shortcode( $name );
        });
    }

    public function cron_init() {
        if ( ! wp_next_scheduled( 'wpsp_cron' ) ) {
            wp_schedule_event( time(), 'daily', 'wpsp_cron' );
        }
        add_action( 'wpsp_cron', array( $this, 'cron' ) );
    }

    public function js_init() {
        wp_register_script( 'wpsp_main', WP_PLUGIN_URL . '/wpsp/js/main.js', array( 'jquery' ) );
        wp_localize_script( 'wpsp_main', 'WPSP_AJAX', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        wp_enqueue_script( 'wpsp_main' );
        wp_register_script( 'wpsp_grouptypes', WP_PLUGIN_URL . '/wpsp/js/group-types.js', array( 'jquery' ) );
        wp_enqueue_script( 'wpsp_grouptypes' );
    }

    public function css_init() {
        wp_register_style( 'wpsp_style', WP_PLUGIN_URL . '/wpsp/css/main.css' );
        wp_enqueue_style( 'wpsp_style' );
    }

    public function cron() {
        $groups = Store::unstore( 'Group' );
        foreach( $groups as $group ) {
            $group->update();
        }
    }

    public function ajax_render() {
        global $Store;
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
                $object = $Store->unstore( $classname, $id );
            } else {
                $object = new $classname();
            }
            echo render\Renderer::entity( $object );
        }
        die();
    }

    public function ajax_store() {
        global $Store;

        $type = $_REQUEST[ 'type' ];
        $data = $_REQUEST[ 'data' ];

        $derendered = render\Renderer::derender( $type, $data );

        $try = $Store->store( $derendered );

        die();
    }

    public function create_database_tables() {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . WPSP_TBLNAME;

        $sql = "CREATE TABLE $table_name (
                id varchar(255) NOT NULL,
                type varchar(255) NOT NULL,
                data longblob NOT NULL
                ) $charset_collate;";

        dbDelta( $sql );
    }

    public function shortcode( $name ) {
        if ( ! is_user_logged_in()) {
            $login_url = wp_login_url( get_permalink() );
            echo sprintf( __( 'You do not have access to this page.' ), $login_url );
        } else {
            $this->{"page_$name"}();
        }
    }

    public function page_debug() {
        global $Store;
        // $Store = new Store();

        $remote = new Remote();
        $remote->setFullUrl('https://moodle.lafayette.edu/ret/path/get/stuff');
        $remoteid = $Store->store($remote);

        $query = new Query($remoteid);
        $queryid = $Store->store($query);

        $group = new Group( array(), $queryid);
        $groupid = $Store->store($group);

        // $group2 = $Store->unstore( 'Group', 'fd02d65b137ffe92e0c3dea3813ca472' );
        $group2 = $Store->unstore( 'Group' );
        echo "<pre>";
        print_r($group2);
        echo "</pre>";


        // print_r($Store->getRequiredTypes('Remote'));
        // print_r(Store::store($remote));
        // $remote2 = $Store->unstore( 'Remote', '03c8daeeee8cba218012b321e5290938');
        // print_r($remote2);
    }

    public function page_group_types() {
        echo render\Renderer::pageRemotes();
    }

    // public function page_remotes() {
    //     echo render\Renderer::
    // }
}

new SiteProvisioner();

