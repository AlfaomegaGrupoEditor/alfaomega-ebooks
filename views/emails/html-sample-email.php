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
        <?php _e( 'Access code:', 'alfaomega-ebooks') ?>
            <?php if(is_string($sample['code'])): ?>
                <strong> <?php echo $sample['code'] ?> </strong>
            <?php else: ?>
                <ul>
                    <?php foreach($sample['code'] as $code): ?>
                        <li><strong> <?php echo $code ?> </strong></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif ?>
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

    <table>
        <tr>
            <td>
                <h3 style="font-size: 14px;">Instrucciones para acceder a tu libro:</h3>
            </td>
        </tr>
        <tr>
            <td style="padding-left: 30px; padding-right: 30px;">
                <table cellpadding="3" cellspacing="0" style="border-collapse: collapse; border: 1px solid #808080;">
                    <tbody>
                        <tr style="border: 1px solid #808080;">
                            <td  width="50%" style="background-color: #A4C2F4; font-weight: bold; border: 1px solid #808080;">
                                <span>Para leer en l&iacute;nea tu libro sigue los siguientes pasos</span>
                            </td>
                            <td  width="50%" style="background-color: #FCE5CD; font-weight: bold; border: 1px solid #808080;">
                                <span class="c14 c20">Para descargar tu libro a cualquier dispositivo (PC, M&oacute;vil, Tablet, etc)</span>
                            </td>
                        </tr>
                        <tr style="border: 1px solid #808080; vertical-align: top">
                            <td width="50%" style="border: 1px solid #808080;">
                                <span>
                                    <ol>
                                        <li>Accede a
                                            <a href="<?php echo esc_url(home_url()); ?>"><?php echo esc_url(home_url()); ?></a>
                                        </li>
                                        <li>Inicia sesi&oacute;n, si ya posees una cuenta.</a> </li>
                                        <li>Accede a la opci&oacute;n "Mis eBooks", en
                                            <a href="<?php echo esc_url(home_url()); ?>/my-ao-ebooks"> <?php echo esc_url(home_url()); ?>
                                                /my-ao-ebooks
                                            </a>
                                        </li>
                                        <li>El c&oacute;digo recibido colocarlo en la casilla "Agregar muestras" y dar
                                            clic en "Aplicar"
                                        </li>
                                        <li>Despu&eacute;s de aplicar el c&oacute;digo se debe de mostrar la imagen de
                                            su libro, le da clic a la imagen y le da la opci&oacute;n de leer en l&iacute;nea
                                            o descargarlo.
                                        </li>
                                    </ol>
                                </span>
                                <div style="margin-top: 20px">
                                    <strong>Nota:</strong>
                                    Para facilitar el acceso a la lectura offline, en la sessi&oacute;n "Mis eBooks" esta disponible el acceso a la descarga del PDF.
                                    Haz clic en "Descarga" y sigue las instrucciones <b>Para descargar tu libro a cualquiera de tus dispositivos</b>.
                                </div>
                            </td>
                            <td width="50%" style="border: 1px solid #808080;">
                                <span>
                                    <ol>
                                        <li>Haz clic en Descargar y selecciona un lugar para guardar el eBook. El archivo se guardar&aacute; en formato ACSM.</li>
                                        <li>Localiza el archivo ACSM guardado.</li>
                                        <li>Haz clic con el bot&oacute;n derecho en el archivo ACSM y selecciona Abrir con > <a href="https://www.adobe.com/mx/solutions/ebook/digital-editions/download.html" target="_blank">Adobe Digital Editions.</a></li>
                                        <li>Haz clic en el icono Biblioteca para ver tu colecci&oacute;n de eBooks.</li>
                                        <li>Haz clic en la flecha hacia abajo y selecciona A&ntilde;adir elemento a la biblioteca.</li>
                                        <li>Busca el eBook protegido por DRM y selecci&oacute;nalo. A continuaci&oacute;n, aparecer&aacute; en la vista de la Biblioteca ADE, donde podr&aacute;s seleccionarlo.</li>
                                        <li>Haz doble clic en la imagen del eBook para abrirlo y comenzar a leerlo.</li>
                                    </ol>
                                    <p><b>Nota 1:</b> Adobe Digital Editions tambi&eacute;n se puede utilizar para abrir y leer eBooks sin protecci&oacute;n DRM en tus ordenadores y dispositivos m&oacute;viles autorizados.</p>
                                    <p><b>Nota 2:</b> Debes autorizar al lector de eBooks con un <a href="http://crear-cuentas.com/adobe-id/" target="_blank">adobe id.</a></p>
                                    <p><b>Nota 3:</b> En dispositivos Android o iOS puedes usar otros lectores como Adliko, Universal Book Reader, FBReader, AlReader, Moon+ Reader, Bluefire.</p>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        <!-- [footer starts here] -->
        <tr>
            <!--<td><a href="{{var store_url}}ayuda/paso-a-paso-compras2.html" target="_blank"><strong>Ver instructivo paso a paso</strong></a></td>-->
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td>
                Ante cualquier duda ponte en contacto con nuestro
                <a href="mailto:libroweb@alfaomega.com.mx"><strong>servicio de atenci&oacute;n al
                                                                            cliente</strong></a>,
                siempre ser&aacute; un placer atenderte de forma personal. <br/><br/> Te esperamos en <a
                    target="_blank" href="http://www.alfaomega.com.mx/">Alfaomega Grupo Editor</a>.
            </td>
        </tr>
        <tr>
            <td></td>
        </tr>
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
