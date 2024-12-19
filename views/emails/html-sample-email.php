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

    <p><?php _e( 'Hello, dear customer.', 'alfaomega-ebooks' ); ?></p>

    <p><?php _e( 'We cordially welcome you to our select group of Alfaomega readers. In this message we provide you with courtesy keys for accessing digital books on the Alfaomega Platform.', 'alfaomega-ebooks' ); ?></p>

    <p>
        <?php _e( 'Please copy the following code and paste in the your personal digital library in ', 'alfaomega-ebooks' ); ?>
        <a href="<?php echo get_site_url() ?>"><?php _e('Alfaomega Portal', 'alfaomega-ebooks') ?></a>
    </p>

    <p>
        <?php _e( 'Access code:', 'alfaomega-ebooks') ?> <strong> <?php echo $sample['code'] ?> </strong>
        <br/>
        <?php if(!empty($sample['due_date'])): ?>
            (<?php _e( 'Valid until:', 'alfaomega-ebooks') ?>
            <?php echo Carbon\Carbon::parse($sample['due_date'])->format("d/m/Y") ?>)
        <?php endif ?>
    </p>

    <h2><?php _e( 'eBook samples in this access code', 'alfaomega-ebooks' ); ?></h2>

    <table class="td" cellspacing="0" cellpadding="6" border="0" style="width: 100%; border-collapse: collapse; border: 0">
        <?php foreach ( $sample['payload'] as $access ) : ?>
            <tr>
                <td style="vertical-align: top;">
                    <img src="<?php echo ALFAOMEGA_COVER_PATH . $access['cover'] ?>" width="200" alt="<?php echo $access['title'] ?>"/>
                </td>
                <td style="vertical-align: top;">
                    <h3><?php echo esc_html( $access['title'] . ' (' . $access['isbn'] . ')' ); ?></h3>
                    <h4><?php _e( 'Access details', 'alfaomega-ebooks' ); ?>: </h4>
                    <ul>
                        <li>
                            <?php _e('Access duration:', 'alfaomega-ebooks')?>
                            <?php echo esc_html( $access['access_time_desc'] ); ?>
                        </li>
                        <?php if(!empty($access['read'])): ?>
                            <li><?php _e( 'Read online.', 'alfaomega-ebooks' ) ?></li>
                        <?php endif ?>
                        <?php if(!empty($access['download'])): ?>
                            <li><?php _e( 'Pdf download with DRM.', 'alfaomega-ebooks' ) ?></li>
                        <?php endif ?>
                    </ul>

                    <?php if(!empty($access['details'])): ?>
                        <p><?php echo $access['details']; ?></p>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p></p>
    <p></p>
    <p></p>

    <?php if($additional_content): ?>
        <?php echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) ); ?>
    <?php else: ?>
        <p><?php _e( 'We hope you enjoy your reading experience.', 'alfaomega-ebooks' ); ?></p>
    <?php endif ?>

    <p><strong><?php echo get_bloginfo('name'); ?></strong></p>

<?php
    // Load the email footer
    do_action( 'woocommerce_email_footer', $email );
?>
