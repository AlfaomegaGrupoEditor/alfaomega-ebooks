<?php
    /**
     * Alfaomega eBooks access code Template
     *
     * @package Alfaomega_Ebooks
     */
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    do_action( 'woocommerce_email_header', $email_heading, $email );
?>

    <p><?php _e( 'Hello, dear customer.', 'alfaomega-ebook' ); ?></p>

    <p><?php _e( 'We cordially welcome you to our select group of Alfaomega readers. In this message we provide you with courtesy keys for accessing digital books on the Alfaomega Platform.', 'alfaomega-ebook' ); ?></p>

    <p>
        <?php _e( 'Please copy the following code and paste in the your personal digital library in ', 'alfaomega-ebook' ); ?>
        <a href="<?php echo get_site_url() ?>"><?php _e('Alfaomega Portal', 'alfaomega-ebook') ?></a>
    </p>

    <p>
        <?php _e( 'Access code:', 'alfaomega-ebook') ?> <strong> <?php echo $sample['code'] ?> </strong>
        <br/>
        <?php if(!empty($sample['due_date'])): ?>
            (<?php _e( 'Valid until:', 'alfaomega-ebook') ?>
            <?php echo Carbon\Carbon::parse($sample['due_date'])->format("d/m/Y") ?>)
        <?php endif ?>
    </p>

    <h2><?php _e( 'Access Details', 'alfaomega-ebook' ); ?></h2>

    <table class="td" cellspacing="0" cellpadding="6" border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align:left;"><?php _e( 'Digital book', 'alfaomega-ebook' ); ?></th>
                <th style="text-align:left;"><?php _e( 'Access', 'alfaomega-ebook' ); ?></th>
                <th style="text-align:left;"><?php _e( 'Online', 'alfaomega-ebook' ); ?></th>
                <th style="text-align:left;"><?php _e( 'Download', 'alfaomega-ebook' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $sample['payload'] as $access ) : ?>
                <tr>
                    <td><?php echo esc_html( $access['title'] . ' (' . $access['isbn'] . ')' ); ?></td>
                    <td><?php echo esc_html( $access['access_time_desc'] ); ?></td>
                    <td><?php $access['read'] ? _e( 'Yes', 'alfaomega-ebook' ) : _e( 'No', 'alfaomega-ebook' )?></td>
                    <td><?php $access['download'] ? _e( 'Yes', 'alfaomega-ebook' ) : _e( 'No', 'alfaomega-ebook' ) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p></p>
    <p></p>
    <p></p>
    <p><?php _e( 'We hope you enjoy your reading experience.', 'alfaomega-ebook' ); ?></p>

<?php
// Load the email footer
do_action( 'woocommerce_email_footer', $email );
?>
