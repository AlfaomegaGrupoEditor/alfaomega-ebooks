<div class="wrap">
<h1><?php esc_html_e("Refresh eBooks", 'alfaomega-ebooks'); ?></h1>
    <div class="alfaomega_ebooks-about-text">
        <p>
            <?php esc_html_e("Resfresh the eBooks information pulling the current eBook data in Publisher Panel and search the relative product to create a link using the Digital ISBN.", 'alfaomega-ebooks') ?>
        </p>
    </div>

    <?php
        $service = new Alfaomega_Ebooks_Service(false);
        $queueStatus = $service->queueStatus('alfaomega_ebooks_queue_refresh');
    ?>

    <div class="alfaomega_ebooks-pagebody">
        <div class="alfaomega_ebooks-success-msg" style="display: none;"></div>
        <div class="alfaomega_ebooks-error-msg" style="display: none;"></div>
        <form method="post"
              id="alfaomega_ebooks_form"
              class="alfaomega_ebooksCol-9"
        >
            <input type="hidden" name="action" value="alfaomega_ebooks_form" />
            <input type="hidden" name="endpoint" value="refresh_ebooks" />
            <input type="hidden" name="alfaomega_ebook_nonce" value="<?=wp_create_nonce('alfaomega_ebook_nonce')?>" />

            <h2>
                <?php esc_html_e("Queue status", 'alfaomega-ebooks'); ?>:
                <span id="queue_status"><?php echo $queueStatus['pending'] > 0 ? esc_html__("Working", 'alfaomega-ebooks') : esc_html__("Idle", 'alfaomega-ebooks'); ?></span>
            </h2>
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
                        <div id="queue-complete" class="divTableCell">
                            <?php echo $queueStatus['complete'] ?>
                        </div>
                    </div>
                    <div class="divTableRow">
                        <div class="divTableCell"><?php esc_html_e("Failed", 'alfaomega-ebooks'); ?></div>
                        <div id="queue-failed" class="divTableCell">
                            <?php echo $queueStatus['failed'] ?>
                        </div>
                    </div>
                    <div class="divTableRow">
                        <div class="divTableCell"><?php esc_html_e("Pending", 'alfaomega-ebooks'); ?></div>
                        <div id="queue-pending" class="divTableCell">
                            <?php echo $queueStatus['pending'] ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <input class="alfaomega_ebooks-btn btnFade alfaomega_ebooks-btnBlueGreen alfaomega_ebooks_refresh_ebooks"
                   id="form_submit"
                   type="submit"
                    <?php echo $queueStatus['pending'] > 0 ? 'disabled="disabled"' : ''; ?>
                   name="alfaomega_ebooks_refresh_ebooks"
                   value="<?php esc_html_e("Refresh eBooks", 'alfaomega-ebooks') ?>"
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
