<div class="wrap">
    <h1><?php esc_html_e("Link Products", 'alfaomega-ebooks'); ?></h1>
    <div class="alfaomega_ebooks-about-text">
        <p>
            <?php esc_html_e("Using the list of eBooks already imported search the relative product to create a link using the Digital ISBN.", 'alfaomega-ebooks') ?>
        </p>
    </div>

    <div class="alfaomega_ebooks-pagebody">
        <div class="alfaomega_ebooks-success-msg" style="display: none;"></div>
        <div class="alfaomega_ebooks-error-msg" style="display: none;"></div>
        <form method="post"
              id="alfaomega_ebooks_form"
              class="alfaomega_ebooksCol-9"
        >
            <input type="hidden" name="action" value="alfaomega_ebooks_form" />
            <input type="hidden" name="endpoint" value="link_products" />
            <input type="hidden" name="alfaomega_ebook_nonce" value="<?=wp_create_nonce('alfaomega_ebook_nonce')?>" />

            <h2><?php esc_html_e("Queue status", 'alfaomega-ebooks'); ?></h2>
            <div class="divTable blueTable">
                <div class="divTableHeading">
                    <div class="divTableRow">
                        <div class="divTableHead"><?php esc_html_e("Status", 'alfaomega-ebooks'); ?></div>
                        <div class="divTableHead"><?php esc_html_e("Count", 'alfaomega-ebooks'); ?></div>
                    </div>
                </div>
                <div class="divTableBody">
                    <div class="divTableRow">
                        <div class="divTableCell"><?php esc_html_e("Completed", 'alfaomega-ebooks'); ?></div>
                        <div id="queue-link-completed" class="divTableCell">0</div>
                    </div>
                    <div class="divTableRow">
                        <div class="divTableCell"><?php esc_html_e("Failed", 'alfaomega-ebooks'); ?></div>
                        <div id="queue-link-failed" class="divTableCell">0</div>
                    </div>
                    <div class="divTableRow">
                        <div class="divTableCell"><?php esc_html_e("Pending", 'alfaomega-ebooks'); ?></div>
                        <div id="queue-link-scheduled" class="divTableCell">0</div>
                    </div>
                </div>
            </div>
            
            <input class="alfaomega_ebooks-btn btnFade alfaomega_ebooks-btnBlueGreen alfaomega_ebooks_link_products"
                   type="submit"
                   name="alfaomega_ebooks_link_ebooks"
                   value="<?php esc_html_e("Link Products", 'alfaomega-ebooks') ?>"
            >
            <a class="alfaomega_ebooks-btn btnFade alfaomega_ebooks-btnBlueGreenAnchor"
               href="<?php esc_url(admin_url('admin.php')) ?> ?page=alfaomega_ebooks_admin"
            >
                <?php esc_html_e("Back", 'alfaomega-ebooks') ?>
            </a>
        </form>

        <div class="alfaomega_ebooks-footer">
        </div>
    </div>
</div>
