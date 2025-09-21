<?php
if (!defined('ABSPATH')) exit;

function bcaw_general_tab() {

    // Default tab states
    $defaults = [
        'delivery'     => 1,
        'payments'     => 1,
        'package'      => 1,
        'whatsapp'     => 1,
        'cart'         => 1,
        'checkout'     => 1,
        'shop'         => 1,
        'products'     => 1,
        'sequential'   => 1,
        'media_check'  => 1,
        'system'       => 1,
    ];

    $general = get_option('bcaw_general_settings', $defaults);
    $general = wp_parse_args($general, $defaults);

    // Tab definitions
    $tabs = [
        'whatsapp'     => ['label' => __('WhatsApp','banglacommerce-all-in-one-woocommerce')],
        'delivery'     => ['label' => __('Delivery','banglacommerce-all-in-one-woocommerce')],
        'package'      => ['label' => __('Package/Invoice','banglacommerce-all-in-one-woocommerce')],
        'media_check'  => ['label' => __('Image/Video Check','banglacommerce-all-in-one-woocommerce')],
        'system'       => ['label' => __('System Info','banglacommerce-all-in-one-woocommerce')],
        'cart'         => ['label' => __('Cart','banglacommerce-all-in-one-woocommerce')],
        'checkout'     => ['label' => __('Checkout','banglacommerce-all-in-one-woocommerce')],
        'shop'         => ['label' => __('Shop','banglacommerce-all-in-one-woocommerce')],
        'products'     => ['label' => __('Products','banglacommerce-all-in-one-woocommerce')],
        'sequential'   => ['label' => __('Sequential Number','banglacommerce-all-in-one-woocommerce')],
    ];
    ?>

    <form method="post" action="options.php">
        <?php settings_fields('bcaw_general_group'); ?>
        <?php do_settings_sections('bcaw_general_group'); ?>

        <h2 class="bcaw-card-title"><?php _e('Enable/Disable Plugin Tabs','banglacommerce-all-in-one-woocommerce'); ?></h2>

        <div class="bcaw-row">
            <?php foreach ($tabs as $key => $tab): 
                $checked = !empty($general[$key]) ? 1 : 0; 
            ?>
                <div class="bcaw-col-3">
                    <div class="bcaw-card bcaw-card-padding">
                        <h3 class="bcaw-text-center"><?php echo esc_html($tab['label']); ?></h3>
                        <div class="bcaw-text-center bcaw-mt-10">
                            <label class="bcaw-toggle">
                                <input type="hidden" name="bcaw_general_settings[<?php echo esc_attr($key); ?>]" value="0">
                                <input type="checkbox" name="bcaw_general_settings[<?php echo esc_attr($key); ?>]" value="1" <?php checked($checked,1); ?>>
                                <span class="bcaw-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="bcaw-mt-20">
            <?php submit_button(__('Save Settings','banglacommerce-all-in-one-woocommerce'), 'primary', 'bcaw-save-btn'); ?>
        </div>
    </form>

    <style>
        /* Simple Toggle CSS */
        .bcaw-toggle { position: relative; display: inline-block; width: 50px; height: 24px; }
        .bcaw-toggle input { opacity: 0; width: 0; height: 0; }
        .bcaw-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
                       background-color: #ccc; transition: .4s; border-radius: 24px; }
        .bcaw-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
                              background-color: white; transition: .4s; border-radius: 50%; }
        .bcaw-toggle input:checked + .bcaw-slider { background-color: #0073aa; }
        .bcaw-toggle input:checked + .bcaw-slider:before { transform: translateX(26px); }
        .bcaw-text-center { text-align: center; }
        .bcaw-mt-10 { margin-top: 10px; }
        .bcaw-mt-20 { margin-top: 20px; }
        .bcaw-row { display: flex; flex-wrap: wrap; gap: 15px; }
        .bcaw-col-3 { flex: 0 0 24%;}
        .bcaw-card { background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); padding: 15px; }
        .bcaw-card-padding { padding: 15px; }
        .bcaw-card-title { margin-bottom: 20px; }
    </style>

<?php
}
?>
