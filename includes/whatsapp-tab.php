<?php
if (!defined('ABSPATH')) exit;

function bcaw_whatsapp_tab() {
    $defaults = [
        'enabled'      => 0,
        'number'       => '+8801XXXXXXXXX', // Default demo number
        'button_text'  => 'Order via WhatsApp',
        'message'      => "Hello! I am interested in purchasing the following product:\n\nProduct: {product}\nQuantity: [Please specify]\n\nKindly provide the price, availability, and delivery details. Thank you!",
        'show_single'  => 1,
        'show_archive' => 1,
        'color'        => '#25D366',
    ];

    $settings = wp_parse_args(get_option('bes_whatsapp_settings', []), $defaults);

    // Enqueue color picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    ?>

    <h2><?php esc_html_e('WhatsApp Order Settings', 'banglacommerce-all-in-one-woocommerce'); ?></h2>
    <form method="post">
        <?php wp_nonce_field('bes_whatsapp_save', 'bes_whatsapp_nonce'); ?>

        <div class="bcaw-card">

            <div class="bcaw-field">
                <label><?php esc_html_e('Enable WhatsApp Button', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <label class="bcaw-switch">
                    <input type="checkbox" name="bes_whatsapp_settings[enabled]" value="1" <?php checked($settings['enabled'], 1); ?>>
                    <span class="bcaw-slider"></span>
                </label>
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Phone Number', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <input type="text" name="bes_whatsapp_settings[number]" value="<?php echo esc_attr($settings['number']); ?>" class="bcaw-input" placeholder="+8801XXXXXXXXX">
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Button Text', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <input type="text" name="bes_whatsapp_settings[button_text]" value="<?php echo esc_attr($settings['button_text']); ?>" class="bcaw-input">
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Message Template', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <textarea name="bes_whatsapp_settings[message]" rows="8" class="bcaw-textarea"><?php echo esc_textarea($settings['message']); ?></textarea>
                <small><?php esc_html_e('Use {product} to include the product name.', 'banglacommerce-all-in-one-woocommerce'); ?></small>
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Show on Single Product Page', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <label class="bcaw-switch">
                    <input type="checkbox" name="bes_whatsapp_settings[show_single]" value="1" <?php checked($settings['show_single'], 1); ?>>
                    <span class="bcaw-slider"></span>
                </label>
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Show on Shop / Archive Pages', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <label class="bcaw-switch">
                    <input type="checkbox" name="bes_whatsapp_settings[show_archive]" value="1" <?php checked($settings['show_archive'], 1); ?>>
                    <span class="bcaw-slider"></span>
                </label>
            </div>

            <div class="bcaw-field">
                <label><?php esc_html_e('Button Color', 'banglacommerce-all-in-one-woocommerce'); ?></label>
                <input type="text" name="bes_whatsapp_settings[color]" value="<?php echo esc_attr($settings['color']); ?>" class="bcaw-color-picker">
            </div>

        </div>

        <?php submit_button(__('Save WhatsApp Settings', 'banglacommerce-all-in-one-woocommerce')); ?>
    </form>

<?php
}

// Save Settings
add_action('admin_init', function() {
    if (isset($_POST['bes_whatsapp_settings']) && check_admin_referer('bes_whatsapp_save', 'bes_whatsapp_nonce')) {
        $data = $_POST['bes_whatsapp_settings'];
        $settings = [
            'enabled'      => !empty($data['enabled']) ? 1 : 0,
            'number'       => sanitize_text_field($data['number'] ?? '+8801XXXXXXXXX'),
            'button_text'  => sanitize_text_field($data['button_text'] ?? 'Order via WhatsApp'),
            'message'      => sanitize_textarea_field($data['message'] ?? "Hello! I am interested in purchasing the following product:\n\nProduct: {product}\nQuantity: [Please specify]\n\nKindly provide the price, availability, and delivery details. Thank you!"),
            'show_single'  => !empty($data['show_single']) ? 1 : 0,
            'show_archive' => !empty($data['show_archive']) ? 1 : 0,
            'color'        => sanitize_hex_color($data['color'] ?? '#25D366'),
        ];
        update_option('bes_whatsapp_settings', $settings);
    }
});

// Frontend WhatsApp Button
function bes_whatsapp_button_output() {
    $settings = wp_parse_args(get_option('bes_whatsapp_settings', []), [
        'enabled'=>0,
        'number'=>'+8801XXXXXXXXX',
        'button_text'=>'Order via WhatsApp',
        'message'=>"Hello! I am interested in purchasing the following product:\n\nProduct: {product}\nQuantity: [Please specify]\n\nKindly provide the price, availability, and delivery details. Thank you!",
        'color'=>'#25D366'
    ]);

    if (empty($settings['enabled']) || empty($settings['number'])) return;

    global $product;
    if (!$product) return;

    $product_name = $product->get_name();

    // Format phone number
    $number = preg_replace('/\D/', '', $settings['number']);
    if (strpos($number, '880') !== 0) {
        $number = '880' . ltrim($number, '0');
    }

    // Replace {product} placeholder in the message
    $message_text = str_replace('{product}', $product_name, $settings['message']);

    // Convert newlines to WhatsApp line breaks and URL-encode
    $message = rawurlencode($message_text);

    $color = esc_attr($settings['color']);
    $text  = esc_html($settings['button_text']);

    echo '<div class="bes-whatsapp-wrap">
        <a href="https://wa.me/'.$number.'?text='.$message.'" target="_blank" class="bes-whatsapp-btn" style="background:'.$color.';">'.$text.'</a>
    </div>';
}

// WooCommerce Hooks
add_action('woocommerce_after_add_to_cart_button', function () {
    $s = get_option('bes_whatsapp_settings', []);
    if (!empty($s['show_single'])) bes_whatsapp_button_output();
});
add_action('woocommerce_after_shop_loop_item', function () {
    $s = get_option('bes_whatsapp_settings', []);
    if (!empty($s['show_archive'])) bes_whatsapp_button_output();
});
