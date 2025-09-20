<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// -------------------- Register Settings --------------------
add_action('admin_init', function() {
    register_setting('bcaw_sequential_group', 'bcaw_sequential_settings', 'bcaw_sanitize_sequential_settings');
});

// -------------------- Sanitize Function --------------------
function bcaw_sanitize_sequential_settings($input){
    $output = [];
    $output['enable']  = !empty($input['enable']) ? 1 : 0;
    $output['prefix']  = isset($input['prefix']) ? sanitize_text_field($input['prefix']) : 'ORD-';
    $output['start']   = isset($input['start']) ? intval($input['start']) : 1000;
    $output['padding'] = isset($input['padding']) ? max(1, intval($input['padding'])) : 6;
    return $output;
}

// -------------------- Helper Functions --------------------
function bcaw_render_toggle($name, $value, $label = '') {
    ?>
    <label class="bcaw-toggle">
        <input type="hidden" name="<?php echo esc_attr($name); ?>" value="0">
        <input type="checkbox" name="<?php echo esc_attr($name); ?>" value="1" <?php checked($value,1); ?>>
        <span class="bcaw-slider"></span>
        <?php if($label): ?><span style="margin-left:8px;"><?php echo esc_html($label); ?></span><?php endif; ?>
    </label>
    <?php
}

function bcaw_render_input($name, $value, $type = 'text', $placeholder = '', $desc = '') {
    ?>
    <input type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($name); ?>" class="bcaw-input" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>">
    <?php if($desc): ?><p class="description"><?php echo esc_html($desc); ?></p><?php endif; ?>
    <?php
}

// -------------------- Sequential Tab --------------------
function bcaw_sequential_tab() {
    $defaults = [
        'enable' => 1,
        'prefix' => 'ORD-',
        'start'  => 1000,
        'padding'=> 6,
    ];
    $settings = wp_parse_args(get_option('bcaw_sequential_settings', []), $defaults);
    ?>
    <form method="post" action="options.php">
        <?php settings_fields('bcaw_sequential_group'); ?>
        <h2 class="bcaw-card-title"><?php esc_html_e('Sequential Order Numbers','banglacommerce-all-in-one-woocommerce'); ?></h2>

        <div class="bcaw-card bcaw-card-padding bcaw-mb-15">
            <h3><?php esc_html_e('Enable Sequential Orders','banglacommerce-all-in-one-woocommerce'); ?></h3>
            <?php bcaw_render_toggle('bcaw_sequential_settings[enable]', $settings['enable']); ?>
        </div>

        <div class="bcaw-card bcaw-card-padding bcaw-mb-15">
            <h3><?php esc_html_e('Order Prefix','banglacommerce-all-in-one-woocommerce'); ?></h3>
            <?php bcaw_render_input('bcaw_sequential_settings[prefix]', $settings['prefix'], 'text', 'ORD-'); ?>
        </div>

        <div class="bcaw-card bcaw-card-padding bcaw-mb-15">
            <h3><?php esc_html_e('Start Number','banglacommerce-all-in-one-woocommerce'); ?></h3>
            <?php bcaw_render_input('bcaw_sequential_settings[start]', $settings['start'], 'number'); ?>
        </div>

        <div class="bcaw-card bcaw-card-padding bcaw-mb-15">
            <h3><?php esc_html_e('Number Padding','banglacommerce-all-in-one-woocommerce'); ?></h3>
            <?php bcaw_render_input('bcaw_sequential_settings[padding]', $settings['padding'], 'number', '', 'Number of digits, e.g., 000001'); ?>
        </div>

        <?php submit_button(__('Save Settings','banglacommerce-all-in-one-woocommerce'), 'primary', 'bcaw-save-btn'); ?>
    </form>

    <style>
        .bcaw-card { background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); padding: 15px; margin-bottom: 15px; }
        .bcaw-card-title { margin-bottom: 20px; font-size: 18px; }
        .bcaw-input { width: 100%; padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; }
        .bcaw-toggle { position: relative; display: inline-block; width: 50px; height: 24px; }
        .bcaw-toggle input { opacity: 0; width: 0; height: 0; }
        .bcaw-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
                       background-color: #ccc; transition: .4s; border-radius: 24px; }
        .bcaw-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
                              background-color: white; transition: .4s; border-radius: 50%; }
        .bcaw-toggle input:checked + .bcaw-slider { background-color: #0073aa; }
        .bcaw-toggle input:checked + .bcaw-slider:before { transform: translateX(26px); }
        .description { font-size: 12px; color: #555; margin-top: 4px; }
    </style>
<?php }

// -------------------- WooCommerce Order Number --------------------
add_filter('woocommerce_order_number', function($order_id) {
    $settings = get_option('bcaw_sequential_settings', []);
    if(!empty($settings['enable'])){
        $prefix = $settings['prefix'] ?? 'ORD-';
        $start = $settings['start'] ?? 1000;
        $padding = $settings['padding'] ?? 6;
        return $prefix . str_pad($order_id + $start - 1, $padding, '0', STR_PAD_LEFT);
    }
    return $order_id;
});
