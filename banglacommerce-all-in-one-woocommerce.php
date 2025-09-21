<?php
/**
 * Plugin Name: banglacommerce-backup-main
 * Plugin URI:  https://devnahian.com/banglacommerce
 * Description: WooCommerce Checkout & Cart Advanced Settings + Analytics + Delivery Scheduler + Product & Shop Control + Sequential Order Numbers + Mobile Payments
 * Version: 1.9.0
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
if(!defined('BCAW_PLUGIN_VERSION')) define('BCAW_PLUGIN_VERSION', '1.9.0');

// -------------------- Load Includes --------------------
foreach (glob(BCAW_PLUGIN_DIR . "includes/*.php") as $tab_file) {
    include_once $tab_file;
}

// -------------------- Register Settings --------------------
add_action('admin_init', function() {
    register_setting('bcaw_general_group', 'bcaw_general_settings');
    register_setting('bcaw_delivery_group', 'bcaw_delivery_scheduler');
    register_setting('bcaw_package_group', 'bcaw_package_settings');
    register_setting('bcaw_whatsapp_group', 'bcaw_whatsapp_settings');
    register_setting('bcaw_cart_group', 'bcaw_cart_settings');
    register_setting('bcaw_checkout_group', 'bcaw_checkout_settings');
    register_setting('bcaw_shop_group', 'bcaw_shop_settings');
    register_setting('bcaw_products_group', 'bcaw_products_settings');
    register_setting('bcaw_sequential_group', 'bcaw_sequential_settings');
});

// -------------------- Activation Hook --------------------
register_activation_hook(__FILE__, function(){
    if(!get_option('bcaw_general_settings')){
        $defaults = [
            'delivery'=>1,'package'=>1,'whatsapp'=>1,
            'system'=>1,'media_check'=>1
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

// -------------------- Assets --------------------
add_action('admin_enqueue_scripts', function($hook){
    if(!current_user_can('manage_options')) return;
    if($hook !== 'toplevel_page_bcaw-settings') return;

    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

    wp_enqueue_style('bcaw-admin-css', BCAW_PLUGIN_URL.'assets/css/bcaw-admin.css', [], BCAW_PLUGIN_VERSION);
    wp_enqueue_script('bcaw-admin-js', BCAW_PLUGIN_URL.'assets/js/bcaw-admin.js', ['jquery'], BCAW_PLUGIN_VERSION, true);

    // ট্যাব স্পেসিফিক অ্যাসেট
    $css_file_path = BCAW_PLUGIN_DIR."assets/css/bcaw-{$active_tab}.css";
    if(file_exists($css_file_path)){
        wp_enqueue_style("bcaw-{$active_tab}-css", BCAW_PLUGIN_URL."assets/css/bcaw-{$active_tab}.css", [], filemtime($css_file_path));
    }
    $js_file_path = BCAW_PLUGIN_DIR."assets/js/bcaw-{$active_tab}.js";
    if(file_exists($js_file_path)){
        wp_enqueue_script("bcaw-{$active_tab}-js", BCAW_PLUGIN_URL."assets/js/bcaw-{$active_tab}.js", ['jquery'], filemtime($js_file_path), true);
    }
});

// -------------------- Settings Page --------------------
function bcaw_settings_page(){
    if(!current_user_can('manage_options')) return;

    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
    $general = get_option('bcaw_general_settings', []);

    // Tabs
    $tabs = [
    'general'     => __('General Settings', 'banglacommerce-all-in-one-woocommerce'), // Site-wide general settings
    'delivery'    => __('Delivery Scheduler', 'banglacommerce-all-in-one-woocommerce'), // Configure delivery dates, fees
    'package'     => __('Package / Invoice', 'banglacommerce-all-in-one-woocommerce'), // Invoice templates, packing slips
    'whatsapp'    => __('WhatsApp', 'banglacommerce-all-in-one-woocommerce'), // Customer notifications via WhatsApp
    'media_check' => __('Image / Video Check', 'banglacommerce-all-in-one-woocommerce'), // Validate product media
    'system'      => __('System Info', 'banglacommerce-all-in-one-woocommerce'), // Server, WP & plugin info
    'cart'        => __('Cart', 'banglacommerce-all-in-one-woocommerce'), // Customize cart page
    'checkout'    => __('Checkout', 'banglacommerce-all-in-one-woocommerce'), // Checkout page fields & options
    'shop'        => __('Shop', 'banglacommerce-all-in-one-woocommerce'), // Shop page layout & controls
    'products'    => __('Product', 'banglacommerce-all-in-one-woocommerce'), // Individual product controls
    'sequential'  => __('Sequential Numbers', 'banglacommerce-all-in-one-woocommerce'), // Configure WooCommerce sequential order numbers
];

    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p><?php esc_html_e('Welcome to BanglaCommerce – All-in-One for WooCommerce settings.', 'banglacommerce-all-in-one-woocommerce'); ?></p>

        <h2 class="nav-tab-wrapper">
            <?php foreach($tabs as $key => $label): 
                if($key === 'general' || !isset($general[$key]) || $general[$key]): ?>
                    <a href="?page=bcaw-settings&tab=<?php echo esc_attr($key); ?>" 
                       class="nav-tab <?php echo ($active_tab==$key?'nav-tab-active':''); ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                <?php endif; 
            endforeach; ?>
        </h2>

        <form method="post" action="options.php">
            <?php
            switch($active_tab){
                case 'general':
                    settings_fields('bcaw_general_group');
                    bcaw_general_tab();
                break;

                case 'delivery':
                    settings_fields('bcaw_delivery_group');
                    bcaw_delivery_tab();
                break;

                case 'package':
                    settings_fields('bcaw_package_group');
                    bcaw_package_tab();
                break;

                case 'whatsapp':
                    settings_fields('bcaw_whatsapp_group');
                    bcaw_whatsapp_tab();
                break;

                case 'system':
                    bcaw_system_info_tab();
                break;

                case 'media_check':
                    bcaw_media_check_tab();
                break;

                case 'cart':
                    settings_fields('bcaw_cart_group');
                    bcaw_cart_tab();
                break;

                case 'checkout':
                    settings_fields('bcaw_checkout_group');
                    bcaw_checkout_tab();
                break;

                case 'shop':
                    settings_fields('bcaw_shop_group');
                    bcaw_shop_tab();
                break;

                case 'products':
                    settings_fields('bcaw_products_group');
                    bcaw_products_tab();
                break;

                case 'sequential':
                    settings_fields('bcaw_sequential_group');
                    bcaw_sequential_tab();
                break;
            }
            ?>
        </form>
    </div>
    <?php
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
            'cart'=>__('Cart settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'checkout'=>__('Checkout settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'shop'=>__('Shop settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'products'=>__('Products settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
            'sequential'=>__('Sequential Order Numbers settings saved successfully!', 'banglacommerce-all-in-one-woocommerce'),
        ];
        if(isset($messages[$tab])){
            echo '<div class="notice notice-success is-dismissible"><p>'.esc_html($messages[$tab]).'</p></div>';
        }
    }
});
