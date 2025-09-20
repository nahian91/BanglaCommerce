<?php
if (!defined('ABSPATH')) exit;

add_action('admin_init', function(){
    register_setting('bcaw_checkout_group', 'bcaw_checkout_settings', function($input){
        $output = [];
        $order = isset($input['_order']) ? explode(',', sanitize_text_field($input['_order'])) : [];
        if(is_array($input)){
            foreach($order as $key){
                if(isset($input[$key])){
                    $fval = $input[$key];
                    $output[$key] = [
                        'label'       => sanitize_text_field($fval['label'] ?? ucfirst($key)),
                        'placeholder' => sanitize_text_field($fval['placeholder'] ?? ''),
                        'required'    => !empty($fval['required']) ? 1 : 0
                    ];
                }
            }
        }
        return $output;
    });
});

function bcaw_checkout_tab(){
    $defaults = [
        'billing_first_name'=>['label'=>'First Name','placeholder'=>'Enter your first name','required'=>1],
        'billing_last_name'=>['label'=>'Last Name','placeholder'=>'Enter your last name','required'=>1],
        'billing_email'=>['label'=>'Email','placeholder'=>'Enter your email','required'=>1],
        'billing_phone'=>['label'=>'Phone','placeholder'=>'Enter your phone','required'=>1],
        'billing_address_1'=>['label'=>'Address Line 1','placeholder'=>'Street address','required'=>1],
        'billing_address_2'=>['label'=>'Address Line 2','placeholder'=>'Apartment, suite, etc.','required'=>0],
        'billing_city'=>['label'=>'City','placeholder'=>'City','required'=>1],
        'billing_postcode'=>['label'=>'Postcode','placeholder'=>'Postcode / ZIP','required'=>1],
        'billing_country'=>['label'=>'Country','placeholder'=>'Select country','required'=>1],
        'billing_state'=>['label'=>'State','placeholder'=>'Select state','required'=>1],
    ];
    $settings = wp_parse_args(get_option('bcaw_checkout_settings', []), $defaults);
    $order_keys = array_keys($settings);
    ?>
    <form method="post" action="options.php">
        <?php settings_fields('bcaw_checkout_group'); ?>
        <div id="bcaw-fields-list" style="display:flex;flex-direction:column;gap:10px;">
            <?php foreach($settings as $key=>$f): ?>
            <div class="bcaw-card" data-key="<?php echo esc_attr($key); ?>" style="display:flex;align-items:center;gap:10px;">
                <span style="width:150px;font-weight:bold;"><?php echo esc_html($f['label']); ?></span>
                <input type="text" name="bcaw_checkout_settings[<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($f['label']); ?>" placeholder="Label" style="flex:1; padding:5px 8px;">
                <input type="text" name="bcaw_checkout_settings[<?php echo esc_attr($key); ?>][placeholder]" value="<?php echo esc_attr($f['placeholder']); ?>" placeholder="Placeholder" style="flex:1; padding:5px 8px;">
                <label class="bcaw-toggle" style="margin:0;">
                    <input type="hidden" name="bcaw_checkout_settings[<?php echo esc_attr($key); ?>][required]" value="0">
                    <input type="checkbox" name="bcaw_checkout_settings[<?php echo esc_attr($key); ?>][required]" value="1" <?php checked($f['required'],1); ?>>
                    <span class="bcaw-slider"></span>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="bcaw_checkout_settings[_order]" id="bcaw_fields_order" value="<?php echo implode(',', $order_keys); ?>">
        <?php submit_button(__('Save Checkout Settings','banglacommerce-all-in-one-woocommerce')); ?>
    </form>
    <script>
    jQuery(function($){
        $('#bcaw-fields-list').sortable({
            axis:'y',
            update:function(){
                var order = [];
                $('#bcaw-fields-list .bcaw-card').each(function(){
                    order.push($(this).data('key'));
                });
                $('#bcaw_fields_order').val(order.join(','));
            }
        });
    });
    </script>
    <style>
    #bcaw-fields-list .bcaw-card { padding:10px 15px; background:#fff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); cursor:move; }
    .bcaw-toggle { position: relative; display: inline-block; width:50px; height:24px; }
    .bcaw-toggle input { opacity:0;width:0;height:0; }
    .bcaw-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#ccc; transition:.4s; border-radius:24px; }
    .bcaw-slider:before { position:absolute; content:""; height:18px;width:18px; left:3px; bottom:3px; background-color:white; transition:.4s; border-radius:50%; }
    .bcaw-toggle input:checked + .bcaw-slider { background-color:#0073aa; }
    .bcaw-toggle input:checked + .bcaw-slider:before { transform:translateX(26px); }
    </style>
<?php }

add_filter('woocommerce_checkout_fields', function($fields){
    $settings = get_option('bcaw_checkout_settings', []);
    if(empty($settings)) return $fields;
    $new_fields = [];
    foreach($settings as $key=>$f){
        $new_fields[$key] = [
            'label'       => $f['label'],
            'placeholder' => $f['placeholder'],
            'required'    => !empty($f['required']),
            'class'       => ['form-row-wide'],
            'clear'       => true,
        ];
    }
    $fields['billing'] = $new_fields;
    return $fields;
}, 999, 1);
