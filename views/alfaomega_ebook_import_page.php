<div class="wrap">
    <h1><?php use AlfaomegaEbooks\Services\eBooks\Service;

        esc_html_e("Import eBook", 'alfaomega-ebooks'); ?></h1>
    <div class="alfaomega_ebooks-about-text">
        <p>
            <?php esc_html_e("Pull new eBooks from the Publisher Panel, update the list of eBooks and search the relative product for the new ebooks to create a link using the Digital ISBN.", 'alfaomega-ebooks') ?>
        </p>
    </div>

    <?php
        $queueStatus = Service::make()->queue()
            ->status('alfaomega_ebooks_queue_import');
    ?>
    <div class="alfaomega_ebooks-pagebody">
        <div style="min-height: 60px;">
            <div class="alfaomega_ebooks-success-msg" style="display: none;"></div>
            <div class="alfaomega_ebooks-error-msg" style="display: none;"></div>
        </div>
        <form method="post"
              id="alfaomega_ebooks_form"
              class="alfaomega_ebooksCol-9"
        >
            <input type="hidden" name="endpoint" value="import-ebooks" />

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
                        <div class="divTableCell"><?php esc_html_e("Complete", 'alfaomega-ebooks'); ?></div>
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

            <input class="alfaomega_ebooks-btn btnFade alfaomega_ebooks-btnBlueGreen alfaomega_ebooks_import_ebooks"
                   type="submit"
                   id="form_submit"
                    <?php echo $queueStatus['pending'] > 0 ? 'disabled="disabled"' : ''; ?>
                   name="alfaomega_ebooks_import_ebooks"
                   value="<?php esc_html_e("Import eBooks", 'alfaomega-ebooks') ?>"
            >

            <button id="clear-queue" class="alfaomega_ebooks-btn btnFade alfaomega_ebooks-btnBlueGreenAnchor">
                <?php esc_html_e("Clear queue", 'alfaomega-ebooks') ?>
            </button>

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
