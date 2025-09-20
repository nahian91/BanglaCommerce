<?php
if (!defined('ABSPATH')) exit;

add_action('admin_init', function() {
    register_setting('bcaw_cart_group', 'bcaw_cart_settings', 'bcaw_sanitize_cart_settings');
});

function bcaw_sanitize_cart_settings($input) {
    $output = [];
    $fields = [
        'enable_coupons','show_cart_totals','ajax_update',
        'custom_message','min_order_amount','max_order_amount',
        'enable_cross_sells','hide_qty_input','hide_remove_button','cart_banner'
    ];
    foreach ($fields as $field) {
        if (isset($input[$field])) {
            if (in_array($field, ['enable_coupons','show_cart_totals','ajax_update','enable_cross_sells','hide_qty_input','hide_remove_button'])) {
                $output[$field] = !empty($input[$field]) ? 1 : 0;
            } elseif (in_array($field, ['min_order_amount','max_order_amount'])) {
                $output[$field] = floatval($input[$field]);
            } else {
                $output[$field] = sanitize_text_field($input[$field]);
            }
        }
    }
    return $output;
}

function bcaw_cart_tab() {
    $defaults = [
        'enable_coupons'=>1,'show_cart_totals'=>1,'ajax_update'=>1,
        'custom_message'=>'','min_order_amount'=>'','max_order_amount'=>'',
        'enable_cross_sells'=>1,'hide_qty_input'=>0,'hide_remove_button'=>0,'cart_banner'=>''
    ];
    $settings = wp_parse_args(get_option('bcaw_cart_settings', []), $defaults);
    ?>
    <h2><?php esc_html_e('Cart Settings','banglacommerce-all-in-one-woocommerce'); ?></h2>
    <form method="post" action="options.php">
        <?php settings_fields('bcaw_cart_group'); ?>
        <?php foreach([
            'enable_coupons'=>'Enable Coupons',
            'show_cart_totals'=>'Show Cart Totals',
            'ajax_update'=>'Enable AJAX Cart Update',
            'enable_cross_sells'=>'Enable Cross-Sells',
            'hide_qty_input'=>'Hide Quantity Input',
            'hide_remove_button'=>'Hide Remove Item Button'
        ] as $key=>$label): ?>
            <div class="bcaw-card">
                <label class="bcaw-toggle">
                    <input type="hidden" name="bcaw_cart_settings[<?php echo esc_attr($key); ?>]" value="0">
                    <input type="checkbox" name="bcaw_cart_settings[<?php echo esc_attr($key); ?>]" value="1" <?php checked($settings[$key],1); ?>>
                    <span class="bcaw-slider"></span>
                    <?php echo esc_html__($label,'banglacommerce-all-in-one-woocommerce'); ?>
                </label>
            </div>
        <?php endforeach; ?>
        <div class="bcaw-card">
            <label><?php esc_html_e('Custom Cart Message','banglacommerce-all-in-one-woocommerce'); ?></label>
            <textarea name="bcaw_cart_settings[custom_message]" style="width:100%;padding:6px;" rows="2"><?php echo esc_textarea($settings['custom_message']); ?></textarea>
        </div>
        <div class="bcaw-card">
            <label><?php esc_html_e('Minimum Order Amount (BDT)','banglacommerce-all-in-one-woocommerce'); ?></label>
            <input type="number" step="0.01" name="bcaw_cart_settings[min_order_amount]" value="<?php echo esc_attr($settings['min_order_amount']); ?>" style="width:100%;padding:6px;">
        </div>
        <div class="bcaw-card">
            <label><?php esc_html_e('Maximum Order Amount (BDT)','banglacommerce-all-in-one-woocommerce'); ?></label>
            <input type="number" step="0.01" name="bcaw_cart_settings[max_order_amount]" value="<?php echo esc_attr($settings['max_order_amount']); ?>" style="width:100%;padding:6px;">
        </div>
        <div class="bcaw-card">
            <label><?php esc_html_e('Custom Cart Banner (HTML allowed)','banglacommerce-all-in-one-woocommerce'); ?></label>
            <textarea name="bcaw_cart_settings[cart_banner]" style="width:100%;padding:6px;" rows="3"><?php echo esc_textarea($settings['cart_banner']); ?></textarea>
        </div>
        <?php submit_button(__('Save Cart Settings','banglacommerce-all-in-one-woocommerce')); ?>
    </form>
    <style>
    .bcaw-card{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,0.1);padding:15px;margin-bottom:15px}
    .bcaw-toggle{position:relative;display:inline-block;width:50px;height:24px;margin-top:8px}
    .bcaw-toggle input{opacity:0;width:0;height:0}
    .bcaw-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#ccc;transition:.4s;border-radius:24px}
    .bcaw-slider:before{position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background-color:white;transition:.4s;border-radius:50%}
    .bcaw-toggle input:checked+.bcaw-slider{background-color:#0073aa}
    .bcaw-toggle input:checked+.bcaw-slider:before{transform:translateX(26px)}
    </style>
<?php } 

add_action('woocommerce_before_cart', function(){
    $s=get_option('bcaw_cart_settings',[]);
    if(!empty($s['custom_message'])) echo '<div class="bcaw-cart-message">'.wp_kses_post($s['custom_message']).'</div>';
    if(!empty($s['cart_banner'])) echo '<div class="bcaw-cart-banner">'.wp_kses_post($s['cart_banner']).'</div>';
});

add_action('woocommerce_check_cart_items', function(){
    $s=get_option('bcaw_cart_settings',[]);
    if(!empty($s['min_order_amount']) && WC()->cart->total<$s['min_order_amount']) wc_add_notice(sprintf(__('Minimum order amount is %s BDT','banglacommerce-all-in-one-woocommerce'),$s['min_order_amount']),'error');
    if(!empty($s['max_order_amount']) && WC()->cart->total>$s['max_order_amount']) wc_add_notice(sprintf(__('Maximum order amount is %s BDT','banglacommerce-all-in-one-woocommerce'),$s['max_order_amount']),'error');
});

add_filter('woocommerce_cart_item_quantity', function($product_quantity,$cart_item,$cart_item_key){
    $s=get_option('bcaw_cart_settings',[]);
    if(!empty($s['hide_qty_input'])) return $cart_item['quantity'];
    return $product_quantity;
},10,3);

add_filter('woocommerce_cart_item_remove_link', function($link,$cart_item_key){
    $s=get_option('bcaw_cart_settings',[]);
    if(!empty($s['hide_remove_button'])) return '';
    return $link;
},10,2);
