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

spl_autoload_register( function( $class ) {
    if ( strpos( $class, 'WPSP' ) === 0 ) {
        $path = str_replace( 'WPSP\\', '/src/classes/', $class );
        $path = str_replace( '\\', '/', $path);
        include __DIR__ . "$path.php";
    }
});

function resolve_classname( $class ) {
    $namespace = '';
    switch ( $class ) {
        case 'Query':
        case 'QueryParam':
        case 'Remote':
            $namespace = 'query\\';
            break;
        case 'Response':
        case 'ResponseMapping':
        case 'UserResponse':
            $namespace = 'query\response\\';
            break;
        case 'Renderer':
        case 'GroupTypeRenderer':
        case 'QueryRenderer':
        case 'RemoteRenderer':
        case 'TemplateVariables':
            $namespace = 'render\\';
            break;
        case 'SiteEngine':
        case 'SingleSiteEngine':
        case 'MultiSiteEngine':
            $namespace = 'siteengine\\';
            break;
    }
    return "WPSP\\$namespace$class";
}

use WPSP\render\Renderer as Renderer;
use WPSP\GroupType as GroupType;
use WPSP\Group as Group;
use WPSP\Store as Store;
use WPSP\query\Query as Query;
use WPSP\query\Remote as Remote;

include_once( __DIR__ . '/src/classes/Store.php');

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
        wp_register_script( 'wpsp_page_entities', WP_PLUGIN_URL . '/wpsp/js/page-entities.js', array( 'jquery' ) );
        wp_enqueue_script( 'wpsp_page_entities' );
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
        $type = $_REQUEST[ 'rendertype' ];
        if ( $type == 'template' ) {
            echo Renderer::template( $template );
        } else if ( $type == 'entity' ) {
            $name = Renderer::classnameFrontToBack( $_REQUEST[ 'entity' ] );
            $classname = resolve_classname( $name );
            $id = array_key_exists( 'entityid', $_REQUEST ) ? $_REQUEST[ 'entityid' ] : false;
            if ( $id ) {
                $object = $Store->unstore( $name, $id );
            } else {
                $object = new $classname();
            }
            echo Renderer::entity( $object );
        }
        die();
    }

    public function ajax_store() {
        global $Store;
        header('Content-Type: application/json');

        $type = Renderer::classnameFrontToBack( $_REQUEST[ 'type' ] );
        $data = $_REQUEST[ 'data' ];

        $derendered = Renderer::derender( $type, $data );

        if ( ! $derendered ) {
            die();
        }

        $stored = $Store->store( $derendered );
        $object = $stored['object'];

        $return = array(
            'id' => $stored['id'],
        );

        $rerenderid = $_REQUEST[ 'rerenderid' ];

        if ( ! empty( $rerenderid ) ) {
            $return[ 'rerenderid' ] = $rerenderid;
            $return[ 'rerendered' ] = Renderer::entity( $object );
        }
        echo json_encode( $return );

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
        echo Renderer::pageGroupTypes();
    }

    public function page_remotes() {
        echo Renderer::pageRemotes();
    }
}

new SiteProvisioner();

