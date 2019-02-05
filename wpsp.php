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

use WPSP\query\response\QueryResponseMap as QueryResponseMap;
use WPSP\query\response\QueryResponseMapping as QueryResponseMapping;
// use WPSP\query\Query as Query;

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
        case 'QueryResponseMap':
        case 'QueryResponseMapping':
        case 'UserResponse':
            $namespace = 'query\response\\';
            break;
        case 'Renderer':
        case 'TemplateVariables':
            $namespace = 'render\\';
            break;
        case 'GroupTypeRenderer':
        case 'QueryRenderer':
        case 'RemoteRenderer':
            $namespace = 'render\entity\\';
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
use WPSP\query\QueryParam as QueryParam;

include_once( __DIR__ . '/src/classes/Store.php');

class SiteProvisioner {
    public function __construct() {
        add_action('network_admin_menu', array($this, 'add_network_menu'));
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

    public function add_network_menu() {
        add_menu_page( "wpsp-settings", "WPSP Settings", '', 'wpsp-settings', array($this, 'network_menu_page'));
    }

    public function network_menu_page() {
        $this->shortcode('group_types');
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
        wp_register_script( 'wpsp_main', WP_PLUGIN_URL . '/wp-site-provisioner/js/main.js', array( 'jquery' ) );
        wp_localize_script( 'wpsp_main', 'WPSP_AJAX', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        wp_enqueue_script( 'wpsp_main' );
        wp_register_script( 'wpsp_page_entities', WP_PLUGIN_URL . '/wp-site-provisioner/js/page-entities.js', array( 'jquery' ) );
        wp_enqueue_script( 'wpsp_page_entities' );
    }

    public function css_init() {
        wp_register_style( 'wpsp_style', WP_PLUGIN_URL . '/wp-site-provisioner/css/main.css' );
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
            echo Renderer::renderTemplate( $template );
        } else if ( $type == 'entity' ) {
            $name = Renderer::classnameFrontToBack( $_REQUEST[ 'entity' ] );
            $classname = resolve_classname( $name );
            $id = array_key_exists( 'entityid', $_REQUEST ) ? $_REQUEST[ 'entityid' ] : false;
            if ( $id ) {
                $object = $Store->unstoreEntity( $name, $id );
            } else {
                $object = new $classname();
            }
            echo Renderer::renderEntity( $object );
        }
        die();
    }

    public function ajax_store() {
        global $Store;
        header('Content-Type: application/json');

        $type = Renderer::classnameFrontToBack( $_REQUEST[ 'type' ] );
        $data = $_REQUEST[ 'data' ];

        // wp_send_json($data);

        $derendered = Renderer::derenderEntity( $data, $type );

        if ( ! $derendered ) {
            die();
        }

        $stored = $Store->storeEntity( $derendered );
        $object = $stored['object'];

        $return = array(
            'id' => $stored['id'],
        );

        $rerenderid = $_REQUEST[ 'rerenderid' ];

        if ( ! empty( $rerenderid ) ) {
            $return[ 'rerenderid' ] = $rerenderid;
            $return[ 'rerendered' ] = Renderer::renderEntity( $object );
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
                serial longblob NOT NULL
                ) $charset_collate;";

        dbDelta( $sql );
    }

    public function shortcode( $name ) {
        if ( ! is_user_logged_in()) {
            $login_url = wp_login_url( get_permalink() );
            echo sprintf( __( 'You do not have access to this page.', 'wpsp' ), $login_url );
        } else {
            $this->{"page_$name"}();
        }
    }

    public function page_debug() {
        global $Store;
        // $Store = new Store();

        echo "<pre>";
        $remote = new Remote();
        $remote->url = 'http://andycodesthings.com:3000/users';
        // $remoteid = $Store->storeEntity($remote);
        // print_r($remoteid);

        $map = array(
            // new QueryResponseMapping( 'alastname', 'lastname' ),
            // new QueryResponseMapping( 'afirstname', 'firstname' ),
            // new QueryResponseMapping( 'login', 'username' ),
            // new QueryResponseMapping( 'id', '' ),
        );
        $response = new QueryResponseMap( $map );

        $params = array();

        // $query = new Query( $response, $remoteid, $params );
        // $query->setRemote( $remote );

        // $result = $query->run();

        $grouptype = $Store->unstoreEntity('GroupType')[0];
        // $grouptype->getMetaQuery()->
        // $Store->storeEntity($grouptype);
        // $param = new QueryParam( 'courseid', '{id}' );
        // $userquery = $grouptype->userquery;
        // $userquery->remote = $remote;
        // $userquery->addParam( $param );

        // $data = array(
        //     'id' => 124,
        // );
        // $results = $userquery->run( $data );

        // print_r($response->mappings);
        $course = $grouptype->generatePossibleMetas()[1];
        // print_r($grouptype->generatePossibleMetas()[0]);
        $group = $grouptype->makeGroup($course);
        $group->__wakeup();
        // print_r($group);
        print_r($group->loadUsers());
        // echo Renderer::renderEntity($group);
        // $Store->storeEntity($grouptype);
        // echo Renderer::renderEntity( $grouptype );
        // print_r($results);
        echo "</pre>";

        // $query = new Query($remoteid);
        // $queryid = $Store->storeEntity($query);

        // $group = new Group( array(), $queryid);
        // $groupid = $Store->storeEntity($group);

        // // $group2 = $Store->unstoreEntity( 'Group', 'fd02d65b137ffe92e0c3dea3813ca472' );
        // $group2 = $Store->unstoreEntity( 'Group' );
        // echo "<pre>";
        // print_r($group2);
        // echo "</pre>";


        // print_r($Store->getRequiredTypes('Remote'));
        // print_r(Store::store($remote));
        // $remote2 = $Store->unstoreEntity( 'Remote', '03c8daeeee8cba218012b321e5290938');
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

