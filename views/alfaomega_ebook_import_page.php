<div class="wrap">
    <h1><?php esc_html_e("Import eBook", 'alfaomega-ebooks'); ?></h1>
    <div class="alfaomega_ebooks-about-text">
        <p>
            <?php esc_html_e("Pull new eBooks from the Publisher Panel, update the list of eBooks and search the relative product for the new ebooks to create a link using the Digital ISBN.", 'alfaomega-ebooks') ?>
        </p>
    </div>

    <div class="alfaomega_ebooks-pagebody">
        <div class="alfaomega_ebooks-success-msg" style="display: none;"></div>
        <div class="alfaomega_ebooks-error-msg" style="display: none;"></div>
        <form method="post"
              id="alfaomega_ebooks_import_ebooks"
              class="alfaomega_ebooksCol-9"
        >
            <input type="hidden" name="action" value="alfaomega_ebooks_form" />
            <input type="hidden" name="endpoint" value="import_ebooks" />
            <input type="hidden" name="alfaomega_ebook_nonce" value="<?=wp_create_nonce('alfaomega_ebook_nonce')?>" />

            <input class="alfaomega_ebooks-btn btnFade alfaomega_ebooks-btnBlueGreen alfaomega_ebooks_import_ebooks"
                   type="submit"
                   name="alfaomega_ebooks_import_ebooks"
                   value="<?php esc_html_e("Import eBooks", 'alfaomega-ebooks') ?>"
            >
        </form>

        <div class="alfaomega_ebooks-footer">
        </div>
    </div>
</div>
