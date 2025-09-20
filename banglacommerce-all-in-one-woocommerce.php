<?php
/**
 * Plugin Name: banglacommerce-backup-main
 * Plugin URI:  https://devnahian.com/banglacommerce
 * Description: WooCommerce Checkout & Cart Advanced Settings + Analytics + Delivery Scheduler + Product & Shop Control + Sequential Order Numbers + Mobile Payments
 * Version: 1.8.1
 * Author: Abdullah Nahian
 * Author URI: https://devnahian.com
 * Text Domain: banglacommerce-all-in-one-woocommerce
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

// -------------------- Define Plugin Constants --------------------
if(!defined('BCAW_PLUGIN_DIR')) define('BCAW_PLUGIN_DIR', plugin_dir_path(__FILE__));
if(!defined('BCAW_PLUGIN_URL')) define('BCAW_PLUGIN_URL', plugin_dir_url(__FILE__));
if(!defined('BCAW_PLUGIN_VERSION')) define('BCAW_PLUGIN_VERSION', '1.8.1');

// -------------------- Load Includes --------------------
foreach (glob(BCAW_PLUGIN_DIR . "includes/*.php") as $tab_file) {
    include_once $tab_file;
}

// -------------------- Register Settings --------------------
add_action('admin_init', function() {
    register_setting('bcaw_general_group', 'bcaw_general_settings');
    register_setting('bcaw_checkout_group', 'bcaw_checkout_fields');
    register_setting('bcaw_cart_group', 'bcaw_cart_settings');
    register_setting('bcaw_delivery_group', 'bcaw_delivery_scheduler');
    register_setting('bcaw_package_group', 'bcaw_package_settings');
    register_setting('bcaw_whatsapp_group', 'bcaw_whatsapp_settings');
    register_setting('bcaw_sequential_group', 'bcaw_sequential_settings');
    register_setting('bcaw_payments_group', 'bcaw_payments_settings');
});

// -------------------- Activation Hook --------------------
register_activation_hook(__FILE__, function(){
    if(!get_option('bcaw_general_settings')){
        $defaults = [
            'checkout'=>1,'cart'=>1,'delivery'=>1,'package'=>1,'whatsapp'=>1,
            'system'=>1,'media_check'=>1,
            'shop'=>1
        ];
        update_option('bcaw_general_settings', $defaults);
    }
});

// -------------------- Admin Menu --------------------
add_action('admin_menu', 'bcaw_add_main_menu');
function bcaw_add_main_menu() {
    if(!current_user_can('manage_options')) return;

    add_menu_page(
        esc_html__('BanglaCommerce Settings', 'banglacommerce-all-in-one-woocommerce'),
        esc_html__('BanglaCommerce', 'banglacommerce-all-in-one-woocommerce'),
        'manage_options',
        'bcaw-settings',
        'bcaw_settings_page',
        'dashicons-admin-tools',
        56
    );
}

// -------------------- Frontend Assets --------------------
add_action('wp_enqueue_scripts', function() {

    // Enqueue CSS
    wp_enqueue_style(
        'bcaw-frontend-css',
        BCAW_PLUGIN_URL . 'assets/css/bcaw-frontend.css',
        [], // dependencies
        filemtime(BCAW_PLUGIN_DIR . 'assets/css/bcaw-frontend.css')
    );

    // Enqueue JS
    // wp_enqueue_script(
    //     'bcaw-frontend-js',
    //     BCAW_PLUGIN_URL . 'assets/js/bcaw-frontend.js',
    //     ['jquery'], // dependencies
    //     filemtime(BCAW_PLUGIN_DIR . 'assets/js/bcaw-frontend.js'),
    //     true // load in footer
    // );
});


// -------------------- Admin Assets --------------------
add_action('admin_enqueue_scripts', function($hook){
    if(!current_user_can('manage_options')) return;
    if($hook !== 'toplevel_page_bcaw-settings') return;

    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

    // Enqueue global assets
    wp_enqueue_style('bcaw-datatable-css', BCAW_PLUGIN_URL.'assets/css/dataTables.min.css', [], filemtime(BCAW_PLUGIN_DIR.'assets/css/dataTables.min.css'));
    wp_enqueue_style('bcaw-admin-css', BCAW_PLUGIN_URL.'assets/css/bcaw-admin.css', [], filemtime(BCAW_PLUGIN_DIR.'assets/css/bcaw-admin.css'));
    wp_enqueue_script('bcaw-datatable-js', BCAW_PLUGIN_URL.'assets/js/dataTables.min.js', ['jquery'], filemtime(BCAW_PLUGIN_DIR.'assets/js/dataTables.min.js'), true);
    wp_enqueue_script('bcaw-admin-js', BCAW_PLUGIN_URL.'assets/js/bcaw-admin.js', ['jquery'], filemtime(BCAW_PLUGIN_DIR.'assets/js/bcaw-admin.js'), true);

    wp_localize_script('bcaw-admin-js', 'bcawSettings', [
    'activeTab' => $active_tab,
    'ajax_url'  => admin_url('admin-ajax.php'),
    'nonce'     => wp_create_nonce('bcaw_media_nonce'),
    'success'   => __('Media updated successfully!', 'banglacommerce-all-in-one-woocommerce'),
    'error'     => __('Failed to update media.', 'banglacommerce-all-in-one-woocommerce')
]);


    // Tab-specific assets
    $css_file_path = BCAW_PLUGIN_DIR."assets/css/bcaw-{$active_tab}.css";
    if(file_exists($css_file_path)){
        wp_enqueue_style("bcaw-{$active_tab}-css", BCAW_PLUGIN_URL."assets/css/bcaw-{$active_tab}.css", [], filemtime($css_file_path));
    }
    $js_file_path = BCAW_PLUGIN_DIR."assets/js/bcaw-{$active_tab}.js";
    if(file_exists($js_file_path)){
        wp_enqueue_script("bcaw-{$active_tab}-js", BCAW_PLUGIN_URL."assets/js/bcaw-{$active_tab}.js", ['jquery'], filemtime($js_file_path), true);
        wp_localize_script("bcaw-{$active_tab}-js", 'bcawSettings', ['activeTab' => $active_tab]);
    }
});

// -------------------- Settings Page --------------------
function bcaw_settings_page(){
    if(!current_user_can('manage_options')) return;

    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
    $general = get_option('bcaw_general_settings', []);

    echo '<div class="wrap">';
    echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
    echo '<p>' . esc_html__('Welcome to BanglaCommerce – All-in-One for WooCommerce settings.', 'banglacommerce-all-in-one-woocommerce') . '</p>';

    // Tabs
    echo '<h2 class="nav-tab-wrapper">';
    $tabs = [
        'general'=>__('General', 'banglacommerce-all-in-one-woocommerce'),
        'payments'=>__('Payments', 'banglacommerce-all-in-one-woocommerce'),
        'delivery'=>__('Delivery', 'banglacommerce-all-in-one-woocommerce'),
        'package'=>__('Package/Invoice', 'banglacommerce-all-in-one-woocommerce'),
        'whatsapp'=>__('WhatsApp', 'banglacommerce-all-in-one-woocommerce'),
        'media_check'=>__('Image/Video Check', 'banglacommerce-all-in-one-woocommerce'),
        'system'=>__('System Info', 'banglacommerce-all-in-one-woocommerce'),
    ];

    foreach($tabs as $key => $label){
        if($key === 'general' || !isset($general[$key]) || $general[$key]){
            echo '<a href="?page=bcaw-settings&tab='.esc_attr($key).'" class="nav-tab '.($active_tab==$key?'nav-tab-active':'').'">'.esc_html($label).'</a>';
        }
    }
    echo '</h2>';

    // Form
    echo '<form method="post" action="options.php">';
    switch($active_tab){
        case 'general': settings_fields('bcaw_general_group'); bcaw_general_tab(); break;bcaw_checkout_tab(); break;
        case 'payments': settings_fields('bcaw_payments_group'); bcaw_payments_tab(); break;
        case 'delivery': settings_fields('bcaw_delivery_group'); bcaw_delivery_tab(); break;
        case 'package': settings_fields('bcaw_package_group'); bcaw_package_tab(); break;
        case 'whatsapp': settings_fields('bcaw_whatsapp_group'); bcaw_whatsapp_tab(); break;
        case 'system': bcaw_system_info_tab(); break;
        case 'media_check': bcaw_media_check_tab(); break;
        case 'shop': bcaw_shop_tab(); break;
    }
    echo '</form></div>';
}

// -------------------- Admin Notices --------------------
add_action('admin_notices', function(){
    if(isset($_GET['settings-updated'], $_GET['tab']) && $_GET['settings-updated']==='true'){
        $tab = sanitize_text_field($_GET['tab']);
        $messages = [
            'general'=>__('General settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'delivery'=>__('Delivery settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'package'=>__('Package/Invoice settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'whatsapp'=>__('WhatsApp settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'system'=>__('System Info settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'media_check'=>__('Image/Video Check settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'shop'=>__('Shop settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'payments'=>__('Payments settings saved successfully!', 'banglacommerce-all-in-one-woocommerce')
        ];
        if(isset($messages[$tab])){
            echo '<div class="notice notice-success is-dismissible"><p>'.esc_html($messages[$tab]).'</p></div>';
        }
    }
});

// -------------------- WooCommerce Integration --------------------
add_action('plugins_loaded', 'bcaw_payments_check_woocommerce');
function bcaw_payments_check_woocommerce() {
    if (!class_exists('WC_Payment_Gateway')) {
        add_action('admin_notices', 'bcaw_payments_woocommerce_missing_notice');
        return;
    }

    if(is_admin()){
        require_once BCAW_PLUGIN_DIR . 'includes/payments/class-bkash-gateway.php';
        require_once BCAW_PLUGIN_DIR . 'includes/payments/class-nagad-gateway.php';
        require_once BCAW_PLUGIN_DIR . 'includes/payments/class-rocket-gateway.php';
        require_once BCAW_PLUGIN_DIR . 'includes/payments/class-upay-gateway.php';
    }

    add_filter('woocommerce_payment_gateways', 'bcaw_add_gateway_class');
    add_filter('manage_edit-shop_order_columns', 'bcaw_add_custom_order_column');
    add_action('manage_shop_order_posts_custom_column', 'bcaw_custom_order_column_content', 10, 2);
    add_filter('manage_edit-shop_order_sortable_columns', 'bcaw_make_custom_order_column_sortable');
}

function bcaw_add_gateway_class($gateways) {
    $gateways[] = 'WC_Gateway_bKash';
    $gateways[] = 'WC_Gateway_Nagad';
    $gateways[] = 'WC_Gateway_Rocket';
    $gateways[] = 'WC_Gateway_Upay';
    return $gateways;
}

function bcaw_payments_woocommerce_missing_notice(){
    echo '<div class="notice notice-error"><p>'.esc_html__('BanglaCommerce: WooCommerce is not installed or activated. Please install/activate WooCommerce to use this plugin.', 'banglacommerce-all-in-one-woocommerce').'</p></div>';
}
