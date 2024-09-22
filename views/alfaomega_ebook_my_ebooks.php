<div class="my-ebook-content">
    <?php if (wp_get_current_user()->exists()) : ?>
        <div id="ao-my-ebooks-app">
            <!--<b-container>
                <b-row>
                    <b-col>
                        <h2><?php /*esc_html_e("My eBooks loading...", 'alfaomega-ebooks'); */?></h2>
                    </b-col>
                </b-row>
            </b-container>-->
        </div>
    <?php else : ?>
        <?php
            wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
            exit;
        ?>
    <?php endif; ?>
</div>
