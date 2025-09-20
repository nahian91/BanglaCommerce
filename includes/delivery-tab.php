<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * BCaW Delivery Scheduler Settings (No Menu)
 */
function bcaw_delivery_tab() {
    $saved    = get_option( 'bcaw_delivery_scheduler', [] );
    $delivery = wp_parse_args( $saved, [
        'enabled'        => true,
        'time_slots'     => [],
        'blackout_dates' => [],
    ] );

    // Defaults
    if ( empty( $delivery['time_slots'] ) ) {
        $delivery['time_slots'] = [
            __('9 AM - 12 PM','banglacommerce-all-in-one-woocommerce'),
            __('12 PM - 3 PM','banglacommerce-all-in-one-woocommerce'),
            __('3 PM - 6 PM','banglacommerce-all-in-one-woocommerce'),
            __('6 PM - 9 PM','banglacommerce-all-in-one-woocommerce'),
            __('9 PM - 11 PM','banglacommerce-all-in-one-woocommerce'),
        ];
    }

    if ( empty( $delivery['blackout_dates'] ) ) {
        for ( $i = 1; $i <= 5; $i++ ) {
            $delivery['blackout_dates'][] = date( 'Y-m-d', strtotime( "+$i day" ) );
        }
    }

    settings_fields( 'bcaw_delivery_group' );
    do_settings_sections( 'bcaw_delivery_group' );
    ?>
    <div class="bcaw-delivery-card" style="background:#fff; padding:25px; border-radius:12px; max-width:700px;">
        <h2 style="margin-bottom:20px;"><?php _e('Delivery Scheduler','banglacommerce-all-in-one-woocommerce'); ?></h2>

        <!-- Enable Scheduler -->
        <label class="bcaw-switch">
            <input type="hidden" name="bcaw_delivery_scheduler[enabled]" value="0">
            <input type="checkbox" name="bcaw_delivery_scheduler[enabled]" value="1" <?php checked( $delivery['enabled'], true ); ?>>
            <span class="bcaw-slider round"></span> <?php _e('Enable Delivery Scheduler','banglacommerce-all-in-one-woocommerce'); ?>
        </label>

        <!-- Time Slots -->
        <h4 style="margin-top:25px;"><?php _e('Available Time Slots','banglacommerce-all-in-one-woocommerce'); ?></h4>
        <div id="bcaw-time-slots">
            <?php foreach ( $delivery['time_slots'] as $slot ) : ?>
                <div class="bcaw-repeater-item" style="margin-bottom:10px; display:flex; gap:10px; align-items:center;">
                    <input type="text" name="bcaw_delivery_scheduler[time_slots][]" value="<?php echo esc_attr( $slot ); ?>" style="flex:1; padding:5px;">
                    <button type="button" class="button bcaw-remove-item"><?php _e('Remove','banglacommerce-all-in-one-woocommerce'); ?></button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button" id="bcaw-add-slot"><?php _e('Add Time Slot','banglacommerce-all-in-one-woocommerce'); ?></button>

        <!-- Blackout Dates -->
        <h4 style="margin-top:25px;"><?php _e('Blackout Dates','banglacommerce-all-in-one-woocommerce'); ?></h4>
        <div id="bcaw-blackout-dates">
            <?php foreach ( $delivery['blackout_dates'] as $date ) : ?>
                <div class="bcaw-repeater-item" style="margin-bottom:10px; display:flex; gap:10px; align-items:center;">
                    <input type="text" class="bcaw-date-picker" name="bcaw_delivery_scheduler[blackout_dates][]" value="<?php echo esc_attr( $date ); ?>" style="flex:1; padding:5px;">
                    <button type="button" class="button bcaw-remove-item"><?php _e('Remove','banglacommerce-all-in-one-woocommerce'); ?></button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button" id="bcaw-add-date"><?php _e('Add Blackout Date','banglacommerce-all-in-one-woocommerce'); ?></button>

        <?php submit_button( __('Save Delivery Settings','banglacommerce-all-in-one-woocommerce'), 'primary', 'submit', true ); ?>
    </div>

    <style>
        .bcaw-switch { position: relative; display: inline-block; width: 50px; height: 24px; margin-bottom:10px; }
        .bcaw-switch input { opacity: 0; width: 0; height: 0; }
        .bcaw-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
        .bcaw-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .bcaw-slider { background-color: #0073aa; }
        input:checked + .bcaw-slider:before { transform: translateX(26px); }
        h4 { margin-bottom:10px; }
        .bcaw-repeater-item input { margin-right:10px; }
    </style>

    <?php
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-style', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');
    ?>
    <script>
        jQuery(document).ready(function($){
            function initDatePicker() {
                $('.bcaw-date-picker').datepicker({ dateFormat: 'yy-mm-dd', minDate: 0 });
            }
            initDatePicker();

            $('#bcaw-add-slot').on('click', function(){
                $('#bcaw-time-slots').append('<div class="bcaw-repeater-item" style="margin-bottom:10px; display:flex; gap:10px; align-items:center;"><input type="text" name="bcaw_delivery_scheduler[time_slots][]" style="flex:1; padding:5px;"><button type="button" class="button bcaw-remove-item"><?php echo esc_js(__('Remove','banglacommerce-all-in-one-woocommerce')); ?></button></div>');
            });

            $('#bcaw-add-date').on('click', function(){
                $('#bcaw-blackout-dates').append('<div class="bcaw-repeater-item" style="margin-bottom:10px; display:flex; gap:10px; align-items:center;"><input type="text" class="bcaw-date-picker" name="bcaw_delivery_scheduler[blackout_dates][]" style="flex:1; padding:5px;"><button type="button" class="button bcaw-remove-item"><?php echo esc_js(__('Remove','banglacommerce-all-in-one-woocommerce')); ?></button></div>');
                initDatePicker();
            });

            $(document).on('click','.bcaw-remove-item',function(){
                $(this).parent().remove();
            });
        });
    </script>
    <?php
}

// Register setting
add_action( 'admin_init', function () {
    register_setting( 'bcaw_delivery_group', 'bcaw_delivery_scheduler' );
} );

// WooCommerce Checkout Fields
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
    $settings = get_option( 'bcaw_delivery_scheduler', [] );
    $settings = wp_parse_args( $settings, [ 'enabled' => true, 'time_slots' => [] ] );

    if ( empty( $settings['enabled'] ) ) return $fields;

    if ( empty( $settings['time_slots'] ) ) {
        $settings['time_slots'] = [
            '9 AM - 12 PM',
            '12 PM - 3 PM',
            '3 PM - 6 PM',
            '6 PM - 9 PM',
            '9 PM - 11 PM',
        ];
    }

    // Delivery Date
    $fields['billing']['delivery_date'] = [
        'type'        => 'text',
        'label'       => __( 'Delivery Date', 'banglacommerce-all-in-one-woocommerce' ),
        'placeholder' => __( 'YYYY-MM-DD', 'banglacommerce-all-in-one-woocommerce' ),
        'required'    => true,
        'class'       => [ 'form-row-first' ],
        'input_class' => [ 'bcaw-delivery-date' ],
    ];

    // Delivery Time
    $fields['billing']['delivery_time'] = [
        'type'        => 'select',
        'label'       => __( 'Delivery Time', 'banglacommerce-all-in-one-woocommerce' ),
        'required'    => true,
        'options'     => array_combine( $settings['time_slots'], $settings['time_slots'] ),
        'class'       => [ 'form-row-last' ],
        'input_class' => [ 'bcaw-delivery-time' ],
    ];

    return $fields;
} );

// Save order meta
add_action( 'woocommerce_checkout_update_order_meta', function ( $order_id ) {
    if ( ! empty( $_POST['delivery_date'] ) ) {
        update_post_meta( $order_id, '_delivery_date', sanitize_text_field( $_POST['delivery_date'] ) );
    }
    if ( ! empty( $_POST['delivery_time'] ) ) {
        update_post_meta( $order_id, '_delivery_time', sanitize_text_field( $_POST['delivery_time'] ) );
    }
} );

// Show in admin order details
add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
    $date = get_post_meta( $order->get_id(), '_delivery_date', true );
    $time = get_post_meta( $order->get_id(), '_delivery_time', true );

    if ( $date || $time ) : ?>
        <p><strong><?php esc_html_e( 'Delivery Date', 'banglacommerce-all-in-one-woocommerce' ); ?>:</strong> <?php echo esc_html( $date ); ?></p>
        <p><strong><?php esc_html_e( 'Delivery Time', 'banglacommerce-all-in-one-woocommerce' ); ?>:</strong> <?php echo esc_html( $time ); ?></p>
    <?php endif;
} );

// Enqueue datepicker on checkout
add_action( 'wp_enqueue_scripts', function(){
    if( !is_checkout() ) return;

    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css');

    wp_add_inline_script('jquery-ui-datepicker', "
        jQuery(function($){
            function init_bcaw_datepicker(){
                $('.bcaw-delivery-date').datepicker({ dateFormat: 'yy-mm-dd', minDate: 0 });
            }
            init_bcaw_datepicker();
            $(document.body).on('updated_checkout', function(){
                init_bcaw_datepicker();
            });
        });
    ");
});
