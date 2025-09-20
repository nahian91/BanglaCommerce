<?php
if (!defined('ABSPATH')) exit;

/**
 * ===========================
 * Package / Invoice Admin Tab
 * ===========================
 */
function bcaw_package_tab() {

    // Default settings
    $defaults = [
        'company_name' => '',
        'address' => '',
        'phone' => '',
        'email' => '',
        'footer_text' => '',
        'terms' => '',
        'fields_order' => ['company_name','address','phone','email','footer_text','terms'],
    ];

    $settings = wp_parse_args(get_option('bcaw_package_settings', []), $defaults);

    // Active sub-tab
    $active_sub = isset($_GET['sub_tab']) ? sanitize_text_field($_GET['sub_tab']) : 'settings';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Package / Invoice','banglacommerce-all-in-one-woocommerce'); ?></h1>

        <h2 class="nav-tab-wrapper" style="margin-bottom:20px;">
            <a href="?page=bcaw-settings&tab=package&sub_tab=settings" class="nav-tab <?php echo $active_sub=='settings'?'nav-tab-active':''; ?>">
                <?php echo esc_html__('Settings','banglacommerce-all-in-one-woocommerce'); ?>
            </a>
            <a href="?page=bcaw-settings&tab=package&sub_tab=orders" class="nav-tab <?php echo $active_sub=='orders'?'nav-tab-active':''; ?>">
                <?php echo esc_html__('Orders','banglacommerce-all-in-one-woocommerce'); ?>
            </a>
        </h2>

        <?php if($active_sub=='settings'): ?>
            <form method="post" action="options.php">
                <?php settings_fields('bcaw_package_group'); ?>
                <div id="bcaw-fields-container" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px;">
                    <?php foreach ($settings['fields_order'] as $field) : ?>
                        <div class="bcaw-field-item" data-field="<?php echo esc_attr($field); ?>" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:15px;box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                            <?php switch ($field) :
                                case 'company_name': ?>
                                    <label><?php echo esc_html__('Company Name','banglacommerce-all-in-one-woocommerce'); ?></label>
                                    <input type="text" name="bcaw_package_settings[company_name]" value="<?php echo esc_attr($settings['company_name']); ?>" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                                <?php break;

                                case 'address': ?>
                                    <label><?php echo esc_html__('Address','banglacommerce-all-in-one-woocommerce'); ?></label>
                                    <textarea name="bcaw_package_settings[address]" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"><?php echo esc_textarea($settings['address']); ?></textarea>
                                <?php break;

                                case 'phone': ?>
                                    <label><?php echo esc_html__('Phone','banglacommerce-all-in-one-woocommerce'); ?></label>
                                    <input type="text" name="bcaw_package_settings[phone]" value="<?php echo esc_attr($settings['phone']); ?>" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                                <?php break;

                                case 'email': ?>
                                    <label><?php echo esc_html__('Email','banglacommerce-all-in-one-woocommerce'); ?></label>
                                    <input type="text" name="bcaw_package_settings[email]" value="<?php echo esc_attr($settings['email']); ?>" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                                <?php break;

                                case 'footer_text': ?>
                                    <label><?php echo esc_html__('Footer Text','banglacommerce-all-in-one-woocommerce'); ?></label>
                                    <textarea name="bcaw_package_settings[footer_text]" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"><?php echo esc_textarea($settings['footer_text']); ?></textarea>
                                <?php break;

                                case 'terms': ?>
                                    <label><?php echo esc_html__('Terms & Conditions','banglacommerce-all-in-one-woocommerce'); ?></label>
                                    <textarea name="bcaw_package_settings[terms]" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"><?php echo esc_textarea($settings['terms']); ?></textarea>
                                <?php break;
                            endswitch; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <br>
                <?php submit_button(__('Save Package/Invoice Settings','banglacommerce-all-in-one-woocommerce')); ?>
            </form>
        <?php else: // Orders tab ?>
            <table id="bcaw-orders-table" class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Order','banglacommerce-all-in-one-woocommerce'); ?></th>
                        <th><?php echo esc_html__('Customer','banglacommerce-all-in-one-woocommerce'); ?></th>
                        <th><?php echo esc_html__('Total','banglacommerce-all-in-one-woocommerce'); ?></th>
                        <th><?php echo esc_html__('Status','banglacommerce-all-in-one-woocommerce'); ?></th>
                        <th><?php echo esc_html__('Actions','banglacommerce-all-in-one-woocommerce'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $orders = wc_get_orders(['limit'=>50,'orderby'=>'date','order'=>'DESC']);
                if(!empty($orders)):
                    foreach($orders as $order):
                ?>
                    <tr>
                        <td><?php echo esc_html($order->get_order_number()); ?></td>
                        <td><?php echo esc_html($order->get_billing_first_name().' '.$order->get_billing_last_name()); ?></td>
                        <td><?php echo wp_kses_post(wc_price($order->get_total())); ?></td>
                        <td><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></td>
                        <td>
                            <button class="button bcaw-show-invoice" data-id="<?php echo esc_attr($order->get_id()); ?>" data-type="invoice"><?php echo esc_html__('Invoice','banglacommerce-all-in-one-woocommerce'); ?></button>
                            <button class="button bcaw-show-invoice" data-id="<?php echo esc_attr($order->get_id()); ?>" data-type="package"><?php echo esc_html__('Package','banglacommerce-all-in-one-woocommerce'); ?></button>
                        </td>
                    </tr>
                <?php
                    endforeach;
                else:
                    echo '<tr><td colspan="5">'.esc_html__('No orders found','banglacommerce-all-in-one-woocommerce').'</td></tr>';
                endif;
                ?>
                </tbody>
            </table>

            <script>
            jQuery(document).ready(function($){
                if($.fn.DataTable){
                    $('#bcaw-orders-table').DataTable({
                        pageLength: 10,
                        order: [[0,'desc']]
                    });
                }

                $('.bcaw-show-invoice').click(function(e){
                    e.preventDefault();
                    var order_id = $(this).data('id');
                    var type = $(this).data('type');
                    var w = window.open('', '_blank');
                    $.post(ajaxurl,{
                        action:'bcaw_get_invoice_package',
                        order_id:order_id,
                        type:type,
                        _wpnonce:'<?php echo esc_js(wp_create_nonce("bcaw_package_nonce")); ?>'
                    }, function(response){
                        w.document.open();
                        w.document.write(response);
                        w.document.close();
                    });
                });
            });
            </script>
        <?php endif; ?>
    </div>
<?php
}

// ----------------- AJAX Handler: Modern Invoice/Package -----------------
add_action('wp_ajax_bcaw_get_invoice_package', function(){
    check_ajax_referer('bcaw_package_nonce');

    if(!current_user_can('manage_woocommerce')) wp_die('Unauthorized');

    $order_id = intval($_POST['order_id']);
    $type = sanitize_text_field($_POST['type']);
    $settings = wp_parse_args(get_option('bcaw_package_settings', []), [
        'company_name'=>'','address'=>'','phone'=>'','email'=>'','footer_text'=>'','terms'=>''
    ]);
    $order = wc_get_order($order_id);
    if(!$order) wp_die('Invalid order');

    $logo_id = get_theme_mod('custom_logo');
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id,'full') : '';

    $status_colors = [
        'pending'=>'#f39c12','processing'=>'#3498db','on-hold'=>'#9b59b6',
        'completed'=>'#27ae60','cancelled'=>'#e74c3c','refunded'=>'#c0392b','failed'=>'#95a5a6'
    ];

    ob_start(); ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?php echo esc_html($type=='invoice'?'Invoice':'Package'); ?> #<?php echo esc_html($order->get_order_number()); ?></title>
        <style>
            body{font-family:'Segoe UI',sans-serif;margin:0;padding:0;background:#f4f6f8;color:#333;}
            .bcaw-container{max-width:950px;margin:20px auto;background:#fff;padding:30px;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.05);}
            .bcaw-header{display:flex;justify-content:space-between;flex-wrap:wrap;margin-bottom:20px;padding-bottom:20px;border-bottom:2px solid #eee;}
            .bcaw-header img{max-height:70px;}
            .bcaw-company h2{margin:0;color:#2c3e50;font-size:24px;}
            .bcaw-company p{margin:2px 0;font-size:14px;color:#7f8c8d;}
            .bcaw-order{text-align:right;}
            .bcaw-order h3{margin:0 0 10px;color:#2c3e50;font-size:20px;}
            .bcaw-order p{margin:2px 0;font-size:14px;}
            .status-label{display:inline-block;padding:4px 10px;border-radius:6px;color:#fff;font-weight:600;font-size:12px;}
            .bcaw-addresses{display:flex;justify-content:space-between;flex-wrap:wrap;margin-bottom:20px;}
            .bcaw-addresses .card{background:#f7f9fb;padding:15px;border-radius:8px;width:48%;margin-bottom:10px;box-shadow:0 2px 8px rgba(0,0,0,0.05);}
            .bcaw-addresses strong{display:block;margin-bottom:5px;font-weight:600;color:#2c3e50;}
            table.bcaw-items{width:100%;border-collapse:collapse;margin-bottom:20px;box-shadow:0 2px 10px rgba(0,0,0,0.05);}
            table.bcaw-items th, table.bcaw-items td{border:1px solid #e0e0e0;padding:12px;font-size:14px;text-align:center;vertical-align:middle;}
            table.bcaw-items th{background:#ecf0f1;font-weight:600;}
            table.bcaw-items img{max-width:60px;max-height:60px;border-radius:6px;}
            .bcaw-totals{text-align:right;margin-bottom:20px;font-size:15px;}
            .bcaw-totals p{margin:4px 0;}
            .bcaw-totals strong{font-size:16px;}
            .bcaw-footer{font-size:12px;color:#7f8c8d;border-top:1px solid #eee;padding-top:15px;margin-top:20px;line-height:1.5;}
            .bcaw-signature{display:flex;justify-content:space-between;margin-top:40px;flex-wrap:wrap;}
            .bcaw-signature .sign-box{width:48%;border-top:1px solid #ccc;text-align:center;padding-top:8px;font-size:14px;color:#555;margin-bottom:20px;}
            .bcaw-notes{margin-top:20px;background:#f7f9fb;padding:15px;border-radius:6px;font-size:13px;color:#555;line-height:1.4;}
            @media(max-width:768px){.bcaw-addresses .card,.bcaw-signature .sign-box{width:100%;}}
        </style>
    </head>
    <body>
        <div class="bcaw-container">
            <div class="bcaw-header">
                <div class="bcaw-company">
                    <?php if($logo_url): ?><img src="<?php echo esc_url($logo_url); ?>" alt="Logo"><?php endif; ?>
                    <h2><?php echo esc_html($settings['company_name']); ?></h2>
                    <p><?php echo nl2br(esc_html($settings['address'])); ?></p>
                    <p><?php echo esc_html($settings['phone']); ?> | <?php echo esc_html($settings['email']); ?></p>
                </div>
                <div class="bcaw-order">
                    <h3><?php echo esc_html($type=='invoice'?'Invoice':'Package'); ?> #<?php echo esc_html($order->get_order_number()); ?></h3>
                    <p><?php echo esc_html__('Date:','banglacommerce-all-in-one-woocommerce'); ?> <?php echo esc_html($order->get_date_created()->date('Y-m-d H:i')); ?></p>
                    <p><?php echo esc_html__('Status:','banglacommerce-all-in-one-woocommerce'); ?> 
                        <span class="status-label" style="background:<?php echo esc_attr($status_colors[$order->get_status()] ?? '#555'); ?>;">
                            <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                        </span>
                    </p>
                    <p><?php echo esc_html__('Payment:','banglacommerce-all-in-one-woocommerce'); ?> <?php echo esc_html($order->get_payment_method_title()); ?></p>
                    <p><?php echo esc_html__('Shipping:','banglacommerce-all-in-one-woocommerce'); ?> <?php echo esc_html($order->get_shipping_method()); ?></p>
                </div>
            </div>

            <div class="bcaw-addresses">
                <div class="card">
                    <strong><?php echo esc_html__('Billing Address','banglacommerce-all-in-one-woocommerce'); ?></strong>
                    <p><?php echo wp_kses_post($order->get_formatted_billing_address()); ?></p>
                </div>
                <div class="card">
                    <strong><?php echo esc_html__('Shipping Address','banglacommerce-all-in-one-woocommerce'); ?></strong>
                    <p><?php echo wp_kses_post($order->get_formatted_shipping_address()); ?></p>
                </div>
            </div>

            <table class="bcaw-items">
                <tr>
                    <th><?php echo esc_html__('Product','banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php echo esc_html__('Image','banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php echo esc_html__('Qty','banglacommerce-all-in-one-woocommerce'); ?></th>
                    <?php if($type=='invoice'): ?>
                        <th><?php echo esc_html__('Price','banglacommerce-all-in-one-woocommerce'); ?></th>
                        <th><?php echo esc_html__('Total','banglacommerce-all-in-one-woocommerce'); ?></th>
                    <?php endif; ?>
                </tr>
                <?php foreach($order->get_items() as $item):
                    $product = $item->get_product();
                    $img = $product ? wp_get_attachment_image_src($product->get_image_id(),'thumbnail')[0] : '';
                ?>
                <tr>
                    <td><?php echo esc_html($item->get_name()); ?></td>
                    <td><?php if($img): ?><img src="<?php echo esc_url($img); ?>" alt=""><?php endif; ?></td>
                    <td><?php echo esc_html($item->get_quantity()); ?></td>
                    <?php if($type=='invoice'): ?>
                        <td><?php echo wp_kses_post(wc_price($item->get_total()/$item->get_quantity())); ?></td>
                        <td><?php echo wp_kses_post(wc_price($item->get_total())); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </table>

            <?php if($type=='invoice'): ?>
            <div class="bcaw-totals">
                <p><?php echo esc_html__('Subtotal:','banglacommerce-all-in-one-woocommerce'); ?> <?php echo wp_kses_post(wc_price($order->get_subtotal())); ?></p>
                <p><?php echo esc_html__('Discount:','banglacommerce-all-in-one-woocommerce'); ?> <?php echo wp_kses_post(wc_price($order->get_discount_total())); ?></p>
                <p><strong><?php echo esc_html__('Total:','banglacommerce-all-in-one-woocommerce'); ?> <?php echo wp_kses_post(wc_price($order->get_total())); ?></strong></p>
            </div>
            <?php endif; ?>

            <div class="bcaw-notes">
                <?php echo nl2br(esc_html($settings['terms'])); ?>
            </div>

            <div class="bcaw-signature">
                <div class="sign-box"><?php echo esc_html__('Authorized Signature','banglacommerce-all-in-one-woocommerce'); ?></div>
                <div class="sign-box"><?php echo esc_html__('Customer Signature','banglacommerce-all-in-one-woocommerce'); ?></div>
            </div>

            <div class="bcaw-footer">
                <?php echo nl2br(esc_html($settings['footer_text'])); ?>
            </div>
        </div>
    </body>
    </html>
    <?php
    echo ob_get_clean();
    wp_die();
});
