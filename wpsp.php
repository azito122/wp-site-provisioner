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

use WPSP\query\response\QueryResponse as QueryResponse;
use WPSP\query\response\QueryResponseMapping as QueryResponseMapping;
use WPSP\render\Renderer as Renderer;
use WPSP\GroupType as GroupType;
use WPSP\Group as Group;
use WPSP\Store as Store;
use WPSP\query\Query as Query;
use WPSP\query\Remote as Remote;
use WPSP\query\params\QueryParam as QueryParam;
use WPSP\AjaxHandler as AjaxHandler;

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
        case 'Remote':
            $namespace = 'query\\';
            break;
        case 'QueryResponse':
        case 'QueryResponseMapping':
        case 'UserResponse':
            $namespace = 'query\response\\';
            break;
        case 'QueryParam':
        case 'QueryParams':
            $namespace = 'query\params\\';
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

include_once( __DIR__ . '/AjaxHandler.php' );
include_once( __DIR__ . '/src/classes/Store.php' );

class SiteProvisioner {
    public function __construct() {
        // Add to network admin menu.
        add_action('network_admin_menu', function() {
            add_menu_page(
                "wpsp-settings", // Title (tag)
                "WPSP Settings", // Menu text
                '',              // Capability
                'wpsp-settings', // Slug
                function() {
                }
            );
            add_submenu_page(
                'wpsp-settings', // Parent slug
                'wpsp-group-types',  // Title (tag)
                'Group Types',  // Menu text
                '',              // Capability
                'wpsp-group-types',   // Slug
                function() {
                    $this->shortcode( 'group_types' );
                }
            );
            add_submenu_page(
                'wpsp-settings', // Parent slug
                'wpsp-remotes',  // Title (tag)
                'Remotes',  // Menu text
                '',              // Capability
                'wpsp-remotes',   // Slug
                function() {
                    $this->shortcode( 'remotes' );
                }
            );
        });

        // Page shortcodes.
        $this->add_shortcode( 'group_types' );
        $this->add_shortcode( 'settings' );
        $this->add_shortcode( 'remotes' );
        $this->add_shortcode( 'my_groups' );
        $this->add_shortcode( 'debug' );

        // Ajax backend wiring.
        add_action('wp_ajax_wpsp_render',              array( '\WPSP\AjaxHandler', 'render' ) );
        add_action('wp_ajax_wpsp_store',               array( '\WPSP\AjaxHandler', 'store' ) );
        add_action('wp_ajax_wpsp_makegroup',           array( '\WPSP\AjaxHandler', 'makeGroup' ) );
        add_action('wp_ajax_wpsp_addsinglesiteengine', array( '\WPSP\AjaxHandler', 'addSingleSiteEngine' ) );

        // Assets (CSS & JS)
        add_action( 'init', array( $this, 'js_init' ) );
        add_action( 'init', array( $this, 'css_init') );

        // Query var thingy.
        add_filter( 'query_vars', function( $vars ) {
            $vars[] = "action";
            $vars[] = "grouptypeid";
            $vars[] = "metaid";
            return $vars;
        } );

        // Create database tables!
        register_activation_hook( __FILE__, array( $this, 'create_database_tables' ) );

        // Cron.
        $this->cron_init();
    }

    public function add_shortcode( $name ) {
        add_shortcode("wpsp_$name", function() use ( $name ) {
            $this->shortcode( $name );
        });
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

    public function cron_init() {
        if ( ! wp_next_scheduled( 'wpsp_cron' ) ) {
            wp_schedule_event( time(), 'daily', 'wpsp_cron' );
        }
        add_action( 'wpsp_cron', array( $this, 'cron' ) );
    }

    public function cron() {
        $groups = Store::unstore( 'Group' );
        foreach( $groups as $group ) {
            $group->update();
        }
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
            $this->page( $name );
        }
    }

    public function page( $name ) {
        $fnname = ucwords( $name, '_' );
        $fnname = str_replace( '_', '', $fnname );

        echo "<div class=\"wpsp-page page-$name\">";
        echo Renderer::{"page$fnname"}();
        echo "</div>";
    }

    public function page_debug() {
        global $Store;

        echo "<pre>";
        $remote = new Remote();
        $remote->url = 'http://andycodesthings.com:3000/users';
        $map = array(
            // new QueryResponseMapping( 'alastname', 'lastname' ),
            // new QueryResponseMapping( 'afirstname', 'firstname' ),
            // new QueryResponseMapping( 'login', 'username' ),
            // new QueryResponseMapping( 'id', '' ),
        );
        $response = new QueryResponse( $map );

        $params = array();

        $grouptype = $Store->unstoreEntity('GroupType')[0];

        $course = $grouptype->generatePossibleMetas()[1];
        $group = $grouptype->makeGroup($course);
        $group->__wakeup();
        print_r($group->loadUsers());
        echo "</pre>";
    }

    public function page_group_types() {
        echo Renderer::pageGroupTypes();
    }

    public function page_remotes() {
        echo Renderer::pageRemotes();
    }

    public function page_my_groups() {
        echo Renderer::pageMyGroups();
    }
}

new SiteProvisioner();

