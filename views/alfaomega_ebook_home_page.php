<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div class="alfaomega_ebooks-about-text">
        <p>
            <?php esc_html_e("This plugin synchronise the Alfaomega eBooks information with the WooCommerce products using the Digital ISBN.", 'alfaomega-ebooks') ?>
        </p>
    </div>

    <div class="alfaomega_ebooks-about-integrations">
        <div class="alfaomega_ebooks-feature feature-section col three-col">
            <div class="col alfaomega_ebooks-col alfaomega_ebooksPanelWrapper">
                <div class="alfaomega_ebooksPanelHeading"><?php esc_html_e("Import new eBooks", 'alfaomega-ebooks') ?></div>
                <div class="alfaomega_ebooksPanelBody">
                    <p class="alfaomega_ebooksPanelBody_text">
                        <?php esc_html_e("Pull new eBooks from the Publisher Panel, update the list of eBooks and search the relative product for the new ebooks to create a link using the Digital ISBN.", 'alfaomega-ebooks') ?>
                    </p>
                    <p class="alfaomega_ebooksLinkWrapper">
                        <a href="?page=alfaomega_ebooks_import"><?php esc_html_e("Import eBooks", 'alfaomega-ebooks') ?></a>
                    </p>
                </div>
            </div>

            <div class="col alfaomega_ebooks-col alfaomega_ebooksPanelWrapper">
                <div class="alfaomega_ebooksPanelHeading"><?php esc_html_e("Refresh eBooks", 'alfaomega-ebooks') ?></div>
                <div class="alfaomega_ebooksPanelBody">
                    <p class="alfaomega_ebooksPanelBody_text">
                        <?php esc_html_e("Resfresh the eBooks information pulling the current eBook data in Publisher Panel and search the relative product to create a link using the Digital ISBN.", 'alfaomega-ebooks') ?>
                    </p>
                    <p class="alfaomega_ebooksLinkWrapper">
                        <a href="?page=alfaomega_ebooks_refresh"><?php esc_html_e("Refresh eBooks", 'alfaomega-ebooks') ?></a>
                    </p>
                </div>
            </div>

            <div class="col alfaomega_ebooks-col alfaomega_ebooksPanelWrapper">
                <div class="alfaomega_ebooksPanelHeading"><?php esc_html_e("Link Products", 'alfaomega-ebooks') ?></div>
                <div class="alfaomega_ebooksPanelBody">
                    <p class="alfaomega_ebooksPanelBody_text">
                        <?php esc_html_e("Using the list of eBooks already imported search the relative product to create a link using the Digital ISBN.", 'alfaomega-ebooks') ?>
                    </p>
                    <p class="alfaomega_ebooksLinkWrapper">
                        <a href="?page=alfaomega_ebooks_link"><?php esc_html_e("Link Products", 'alfaomega-ebooks') ?></a>
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
