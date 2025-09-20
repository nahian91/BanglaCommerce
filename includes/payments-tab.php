<?php
if (!defined('ABSPATH')) exit;

// -------------------- Register Settings --------------------
add_action('admin_init', function() {
    register_setting('bcaw_payments_group', 'bcaw_mobile_payments', 'bcaw_sanitize_payments_settings');
});

// -------------------- Sanitize Input --------------------
function bcaw_sanitize_payments_settings($input){
    $gateways = ['bkash','nagad','upay','rocket'];
    $output = [];
    foreach($gateways as $g){
        $output[$g] = [
            'enable' => !empty($input[$g]['enable']) ? 1 : 0,
            'mobile' => isset($input[$g]['mobile']) ? sanitize_text_field($input[$g]['mobile']) : '',
            'fee'    => isset($input[$g]['fee']) ? floatval($input[$g]['fee']) : 0
        ];
    }
    return $output;
}

// -------------------- Payments Settings Page Tab --------------------
function bcaw_payments_tab() {
    $options = get_option('bcaw_mobile_payments', []);
    $gateways = ['bkash','nagad','upay','rocket'];
    ?>
    <h2><?php esc_html_e('Mobile Payments Settings','banglacommerce-all-in-one-woocommerce'); ?></h2>
    <form method="post" action="options.php">
        <?php settings_fields('bcaw_payments_group'); ?>

        <?php foreach($gateways as $g): 
            $data = isset($options[$g]) ? $options[$g] : ['enable'=>0,'mobile'=>'','fee'=>0]; ?>
            <div style="border:1px solid #ccc;padding:15px;margin-bottom:15px;border-radius:8px;">
                <h3><?php echo esc_html(ucfirst($g)); ?></h3>
                
                <label>
                    <input type="hidden" name="bcaw_mobile_payments[<?php echo esc_attr($g); ?>][enable]" value="0">
                    <input type="checkbox" name="bcaw_mobile_payments[<?php echo esc_attr($g); ?>][enable]" value="1" <?php checked($data['enable'],1); ?>>
                    <?php esc_html_e('Enable','banglacommerce-all-in-one-woocommerce'); ?>
                </label><br><br>

                <label><?php esc_html_e('Receiver Mobile Number','banglacommerce-all-in-one-woocommerce'); ?></label><br>
                <input type="text" name="bcaw_mobile_payments[<?php echo esc_attr($g); ?>][mobile]" value="<?php echo esc_attr($data['mobile']); ?>" style="width:100%;padding:6px;"><br><br>

                <label><?php esc_html_e('Charge Fee (BDT)','banglacommerce-all-in-one-woocommerce'); ?></label><br>
                <input type="number" step="0.01" name="bcaw_mobile_payments[<?php echo esc_attr($g); ?>][fee]" value="<?php echo esc_attr($data['fee']); ?>" style="width:100%;padding:6px;">
            </div>
        <?php endforeach; ?>

        <?php submit_button(__('Save Settings','banglacommerce-all-in-one-woocommerce')); ?>
    </form>
    <?php
}

// -------------------- Checkout Fields --------------------
add_action('woocommerce_after_order_notes', function($checkout){
    $options = get_option('bcaw_mobile_payments', []);
    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

    foreach($options as $key => $data){
        if($data['enable'] && isset($available_gateways[$key])){
            woocommerce_form_field($key.'_txn', [
                'type' => 'text',
                'class' => ['form-row-wide','bcaw-mobile-field'],
                'label' => ucfirst($key).' Transaction Number',
                'placeholder' => 'Enter transaction number',
                'required' => true,
            ]);
        }
    }
});

// -------------------- JS Toggle Field --------------------
add_action('wp_footer', function(){
    if(!is_checkout()) return;
    ?>
    <script>
    jQuery(function($){
        function toggleMobileFields(){
            var selected = $('input[name="payment_method"]:checked').val();
            $('.bcaw-mobile-field').closest('p.form-row').hide();
            if(selected) $('#'+selected+'_txn_field').closest('p.form-row').show();
        }
        toggleMobileFields();
        $('form.checkout').on('change', 'input[name="payment_method"]', toggleMobileFields);
    });
    </script>
    <?php
});

// -------------------- Save Transaction Number --------------------
add_action('woocommerce_checkout_update_order_meta', function($order_id){
    $options = get_option('bcaw_mobile_payments', []);
    foreach($options as $key => $data){
        if(isset($_POST[$key.'_txn']) && !empty($_POST[$key.'_txn'])){
            update_post_meta($order_id, $key.'_txn', sanitize_text_field($_POST[$key.'_txn']));
        }
    }
});

// -------------------- Add Fee to Cart --------------------
add_action('woocommerce_cart_calculate_fees', function($cart){
    if(is_admin() && !defined('DOING_AJAX')) return;
    $options = get_option('bcaw_mobile_payments', []);
    if(isset($_POST['payment_method'])){
        $method = sanitize_text_field($_POST['payment_method']);
        if(!empty($options[$method]['enable']) && floatval($options[$method]['fee'])>0){
            $cart->add_fee(ucfirst($method).' Payment Fee', floatval($options[$method]['fee']));
        }
    }
});

// -------------------- Show Transaction Number in Admin --------------------
add_action('woocommerce_admin_order_data_after_billing_address', function($order){
    $options = get_option('bcaw_mobile_payments', []);
    foreach($options as $key=>$data){
        $txn = get_post_meta($order->get_id(), $key.'_txn', true);
        if($txn) echo '<p><strong>'.ucfirst($key).' Transaction:</strong> '.esc_html($txn).'</p>';
    }
});

// -------------------- Show Transaction Number in Emails --------------------
add_filter('woocommerce_email_order_meta_keys', function($keys){
    $keys[] = 'bkash_txn';
    $keys[] = 'nagad_txn';
    $keys[] = 'upay_txn';
    $keys[] = 'rocket_txn';
    return $keys;
});
