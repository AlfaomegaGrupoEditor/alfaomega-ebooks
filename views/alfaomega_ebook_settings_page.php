<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <?php
    $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_options';
    ?>
    <h2 class="nav-tab-wrapper">
        <a href="?page=alfaomega_ebooks_admin&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e( 'General Configuration', 'alfaomega-ebooks' ); ?>
        </a>
        <a href="?page=alfaomega_ebooks_admin&tab=platform_options" class="nav-tab <?php echo $active_tab == 'platform_options' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e( 'eBooks Platform', 'alfaomega-ebooks' ); ?>
        </a>
        <a href="?page=alfaomega_ebooks_admin&tab=api_options" class="nav-tab <?php echo $active_tab == 'api_options' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e( 'API Settings', 'alfaomega-ebooks' ); ?>
        </a>
    </h2>
    <form action="options.php" method="post">
        <?php
            switch ($active_tab) {
                case 'general_options':
                    //do_settings_sections( 'alfaomega_ebooks_page1' );
                    settings_fields( 'alfaomega_ebooks_general_group' );
                    do_settings_sections( 'alfaomega_ebooks_page2' );
                    break;
                case 'platform_options':
                    settings_fields( 'alfaomega_ebooks_platform_group' );
                    do_settings_sections( 'alfaomega_ebooks_page3' );
                    break;
                case 'api_options':
                    settings_fields( 'alfaomega_ebooks_api_group' );
                    do_settings_sections( 'alfaomega_ebooks_page4' );
                    break;
            }
            submit_button( esc_html__( 'Save Settings', 'alfaomega-ebooks' ) );
        ?>
    </form>
</div>
