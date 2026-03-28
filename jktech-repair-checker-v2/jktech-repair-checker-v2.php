<?php
/**
 * Plugin Name: JK Tech Repair Price Checker V2
 * Plugin URI:  https://jktechsolutions.ca
 * Description: Repair price checker with inline booking flow.
 * Version:     2.1.3
 * Author:      JK Tech Solutions
 * Text Domain: jktech-repair-checker
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'JKRC_VERSION',    '2.1.3' );
define( 'JKRC_PATH',       plugin_dir_path( __FILE__ ) );
define( 'JKRC_URL',        plugin_dir_url( __FILE__ ) );
define( 'JKRC_DB_KEY',     'jkrc_price_data' );
define( 'JKRC_BOOK_TABLE', 'jkrc_bookings' );

require_once JKRC_PATH . 'includes/default-data.php';
require_once JKRC_PATH . 'includes/admin-page.php';
require_once JKRC_PATH . 'includes/shortcode.php';
require_once JKRC_PATH . 'includes/ajax.php';

register_activation_hook( __FILE__, 'jkrc_activate' );
function jkrc_activate() {
    if ( ! get_option( JKRC_DB_KEY ) ) {
        update_option( JKRC_DB_KEY, jkrc_default_data() );
    }
    jkrc_create_bookings_table();
}

function jkrc_create_bookings_table() {
    global $wpdb;
    $table   = $wpdb->prefix . JKRC_BOOK_TABLE;
    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        status      VARCHAR(20) NOT NULL DEFAULT 'new',
        full_name   VARCHAR(120) NOT NULL,
        email       VARCHAR(120) NOT NULL,
        phone       VARCHAR(40) NOT NULL,
        device      VARCHAR(200) NOT NULL,
        repair      VARCHAR(200) NOT NULL,
        visit_type  VARCHAR(40) NOT NULL,
        address     TEXT,
        pref_date   DATE,
        pref_time   VARCHAR(20),
        description TEXT,
        PRIMARY KEY (id)
    ) $charset;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

add_action( 'admin_menu', 'jkrc_add_menu' );
function jkrc_add_menu() {
    add_menu_page(
        'Repair Price Checker V2',
        'Repair Checker',
        'manage_options',
        'jkrc-settings',
        'jkrc_admin_page',
        'dashicons-hammer',
        58
    );
}

add_action( 'admin_enqueue_scripts', 'jkrc_admin_assets' );
function jkrc_admin_assets( $hook ) {
    if ( $hook !== 'toplevel_page_jkrc-settings' ) return;
    wp_enqueue_style(  'jkrc-admin-css', JKRC_URL . 'admin/css/admin.css', array(), JKRC_VERSION );
    wp_enqueue_script( 'jkrc-admin-js',  JKRC_URL . 'admin/js/admin.js',  array( 'jquery' ), JKRC_VERSION, true );
    wp_localize_script( 'jkrc-admin-js', 'jkrcAdmin', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'jkrc_nonce' ),
        'data'    => jkrc_get_data(),
    ) );
}

add_action( 'wp_enqueue_scripts', 'jkrc_public_assets' );
function jkrc_public_assets() {
    wp_enqueue_style(  'jkrc-public-css', JKRC_URL . 'public/css/checker.css', array(), JKRC_VERSION );
    wp_enqueue_script( 'jkrc-public-js',  JKRC_URL . 'public/js/checker.js',   array(), JKRC_VERSION, true );
    wp_localize_script( 'jkrc-public-js', 'jkrcData', jkrc_get_data() );
    wp_localize_script( 'jkrc-public-js', 'jkrcAjax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'jkrc_book_nonce' ),
    ) );
}

function jkrc_get_data() {
    $data = get_option( JKRC_DB_KEY );
    if ( ! $data ) {
        $data = jkrc_default_data();
    }
    return $data;
}
