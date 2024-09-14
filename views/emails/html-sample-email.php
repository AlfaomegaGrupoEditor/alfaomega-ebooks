<?php
/**
 * Custom Email Template
 *
 * @package WooCommerce/Templates/Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Load the email header
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

    <p><?php _e( 'Hello,', 'your-text-domain' ); ?></p>

    <p><?php _e( 'We wanted to notify you of a custom event related to your order.', 'your-text-domain' ); ?></p>

    <p><?php printf( __( 'Order number: %s', 'your-text-domain' ), '11111' ); ?></p>

    <p><?php _e( 'Here are the details of your order:', 'your-text-domain' ); ?></p>

    <h2><?php _e( 'Order Details', 'your-text-domain' ); ?></h2>

    <table class="td" cellspacing="0" cellpadding="6" border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align:left;"><?php _e( 'Product', 'your-text-domain' ); ?></th>
                <th style="text-align:left;"><?php _e( 'Quantity', 'your-text-domain' ); ?></th>
                <th style="text-align:left;"><?php _e( 'Price', 'your-text-domain' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php /*foreach ( $order->get_items() as $item_id => $item ) : */?><!--
                <tr>
                    <td><?php /*echo esc_html( $item->get_name() ); */?></td>
                    <td><?php /*echo esc_html( $item->get_quantity() ); */?></td>
                    <td><?php /*echo wc_price( $item->get_total() ); */?></td>
                </tr>
            --><?php /*endforeach; */?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" style="text-align:left;"><?php _e( 'Total:', 'your-text-domain' ); ?></th>
                <th style="text-align:left;"><?php echo "test" ?></th>
            </tr>
        </tfoot>
    </table>

    <p><?php _e( 'Thank you for your order!', 'your-text-domain' ); ?></p>

<?php
// Load the email footer
do_action( 'woocommerce_email_footer', $email );
?>
