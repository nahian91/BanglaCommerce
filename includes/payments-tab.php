<?php
if (!defined('ABSPATH')) exit;

function bcaw_payments_tab() {

    $allowed_gateways = ['bkash', 'nagad', 'upay', 'rocket'];
    $gateways = WC()->payment_gateways->payment_gateways();

    // Display success message if transient exists
    if ($message = get_transient('bcaw_success_message')) {
        echo '<div class="notice notice-success is-dismissible bcaw-notice"><p>' . esc_html($message) . '</p></div>';
        delete_transient('bcaw_success_message');
    }

    // Get all completed orders
    $orders = wc_get_orders(['limit' => -1, 'status' => 'completed']);
    $total_amount = 0;
    foreach ($orders as $order) $total_amount += $order->get_total();
    ?>

    <div class="bcaw-container">
        <h2><?php esc_html_e('Payment Method Statistics', 'banglacommerce-all-in-one-woocommerce'); ?></h2>
        <table class="wp-list-table widefat fixed striped bcaw-payment-methods-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Payment Method', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php esc_html_e('Total Amount', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php esc_html_e('Status', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php esc_html_e('Action', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($gateways as $gateway):
                if (!in_array($gateway->id, $allowed_gateways)) continue;

                $total_gateway_amount = 0;
                $gateway_orders = wc_get_orders([
                    'limit' => -1,
                    'status' => 'completed',
                    'payment_method' => $gateway->id,
                ]);
                foreach ($gateway_orders as $order) $total_gateway_amount += $order->get_total();

                $status = ($gateway->enabled === 'yes') ? 'Active' : 'Inactive';
                $status_class = ($gateway->enabled === 'yes') ? 'bcaw-status-active' : 'bcaw-status-inactive';
                $edit_link = admin_url('admin.php?page=wc-settings&tab=checkout&section=' . $gateway->id);
            ?>
                <tr>
                    <td><?php echo esc_html($gateway->get_title()); ?></td>
                    <td><?php echo wp_kses_post(wc_price($total_gateway_amount)); ?></td>
                    <td class="<?php echo esc_attr($status_class); ?>"><?php echo esc_html($status); ?></td>
                    <td><a href="<?php echo esc_url($edit_link); ?>" target="_blank" class="button button-primary"><?php esc_html_e('Edit', 'banglacommerce-all-in-one-woocommerce'); ?></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h2><?php esc_html_e('Transaction Information', 'banglacommerce-all-in-one-woocommerce'); ?></h2>
        <table id="bcaw-transaction-info-table" class="wp-list-table widefat fixed striped bcaw-transaction-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Order ID', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php esc_html_e('Payment Method', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php esc_html_e('Transaction ID', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php esc_html_e('Phone Number', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php esc_html_e('Amount', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php esc_html_e('Date', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php esc_html_e('Order Status', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                    <th><?php esc_html_e('Action', 'banglacommerce-all-in-one-woocommerce'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order):
                $method = $order->get_payment_method();
                $transaction_id = get_post_meta($order->get_id(), "_{$method}_transaction_id", true) ?: 'N/A';
                $phone = get_post_meta($order->get_id(), "_{$method}_phone", true) ?: 'N/A';
            ?>
                <tr>
                    <td><?php echo esc_html($order->get_id()); ?></td>
                    <td><?php echo esc_html(ucwords($method)); ?></td>
                    <td><?php echo esc_html($transaction_id); ?></td>
                    <td><?php echo esc_html($phone); ?></td>
                    <td><?php echo wp_kses_post(wc_price($order->get_total())); ?></td>
                    <td><?php echo esc_html($order->get_date_created()->date('d F Y')); ?></td>
                    <td><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></td>
                    <td><a href="<?php echo esc_url($order->get_edit_order_url()); ?>" class="button button-primary"><?php esc_html_e('Edit Order', 'banglacommerce-all-in-one-woocommerce'); ?></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h3><?php esc_html_e('Total Amount: ', 'banglacommerce-all-in-one-woocommerce'); echo wp_kses_post(wc_price($total_amount)); ?></h3>
    </div>

    <!-- Initialize DataTables -->
    <script type="text/javascript">
    jQuery(document).ready(function($){
        $('#bcaw-transaction-info-table').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "columnDefs": [
                { "orderable": false, "targets": -1 } // Disable ordering on Action column
            ]
        });
    });
    </script>

<?php
}
