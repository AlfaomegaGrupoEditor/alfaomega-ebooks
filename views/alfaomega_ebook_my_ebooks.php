<div class="my-ebook-content">
    <?php if (is_user_logged_in()) : ?>
        <div id="ao-my-ebooks-app">
            <div class="ao-container">
                <div class="card">
                    <?php $standard_logo = Avada()->images->get_logo_image_srcset( 'logo', 'logo_retina' ); ?>
                    <img src="<?php echo esc_url_raw( $standard_logo['url'] ); ?>" alt="Logo" />
                    <h1><?php esc_html_e("Alfaomega eBooks", 'alfaomega-ebooks')?></h1>
                    <h2><?php esc_html_e("A personal Digital Library closer to you.", 'alfaomega-ebooks')?></h2>

                    <div class="loader"></div>
                    <p id="ao-loading-text">
                        <?php esc_html_e("Please wait a few seconds while loading...", 'alfaomega-ebooks')?>
                    </p>

                    <p class="message" id="ao-error-message">
                        <?php esc_html_e("Sorry, something went wrong. Please reload the page or contact customer service.", 'alfaomega-ebooks')?>
                    </p>
                </div>
            </div>
        </div>
    <?php else : ?>
        <?php
            // Redirect to WooCommerce login page and pass the current URL for redirection after login
            $redirect_url = get_permalink(); // Get the current page URL
            $login_url = wc_get_page_permalink( 'myaccount' ); // Get the WooCommerce 'My Account' page URL
            wp_redirect( add_query_arg( 'redirect_to', urlencode( $redirect_url ), $login_url ) );
            exit;
        ?>
    <?php endif; ?>
</div>
