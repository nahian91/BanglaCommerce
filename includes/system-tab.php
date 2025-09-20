<?php
if (!defined('ABSPATH')) exit;

function bcaw_system_info_tab() {
    global $wpdb;

    // ----------------- Server & Site Info -----------------
    $system_info = [
        __('WordPress Version', 'banglacommerce-all-in-one-woocommerce')   => get_bloginfo('version'),
        __('WooCommerce Version', 'banglacommerce-all-in-one-woocommerce') => defined('WC_VERSION') ? WC_VERSION : __('Not Installed', 'banglacommerce-all-in-one-woocommerce'),
        __('PHP Version', 'banglacommerce-all-in-one-woocommerce')         => phpversion(),
        __('MySQL Version', 'banglacommerce-all-in-one-woocommerce')       => $wpdb->db_version(),
        __('Memory Limit', 'banglacommerce-all-in-one-woocommerce')        => WP_MEMORY_LIMIT,
        __('Max Upload Size', 'banglacommerce-all-in-one-woocommerce')     => size_format(wp_max_upload_size()),
        __('Debug Mode', 'banglacommerce-all-in-one-woocommerce')          => (defined('WP_DEBUG') && WP_DEBUG) ? __('Enabled', 'banglacommerce-all-in-one-woocommerce') : __('Disabled', 'banglacommerce-all-in-one-woocommerce'),
        __('Active Theme', 'banglacommerce-all-in-one-woocommerce')        => wp_get_theme()->get('Name') . ' ' . wp_get_theme()->get('Version'),
        __('Site URL', 'banglacommerce-all-in-one-woocommerce')            => get_site_url(),
    ];
    ?>

    <div class="wrap">

        <!-- Server & Site Info Table -->
        <h2><?php echo esc_html__('Server & Site Info', 'banglacommerce-all-in-one-woocommerce'); ?></h2>
        <table class="widefat striped">
            <tbody>
            <?php foreach ($system_info as $key => $value): ?>
                <tr>
                    <th><?php echo esc_html($key); ?></th>
                    <td><?php echo esc_html($value); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
}
