<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <?php
    $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'main_options';
    ?>
    <h2 class="nav-tab-wrapper">
        <a href="?page=mv_slider_admin&tab=general_options" class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General Configuration', 'alfaomega-ebooks' ); ?></a>
        <a href="?page=mv_slider_admin&tab=platform_options" class="nav-tab <?php echo $active_tab == 'platform_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'eBooks Platform', 'alfaomega-ebooks' ); ?></a>
        <a href="?page=mv_slider_admin&tab=api_options" class="nav-tab <?php echo $active_tab == 'api_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Api Settings', 'alfaomega-ebooks' ); ?></a>
    </h2>
    <form action="options.php" method="post">
        <?php
        if( $active_tab == 'general_options' ){
            settings_fields( 'alfaomega_ebooks_group' );
            do_settings_sections( 'alfaomega_ebooks_page1' );
            do_settings_sections( 'alfaomega_ebooks_page2' );
        }else{
            settings_fields( 'alfaomega_ebooks_options' );
            do_settings_sections( 'alfaomega_ebooks_page2' );
        }
        submit_button( esc_html__( 'Save Settings', 'alfaomega-ebooks' ) );
        ?>
    </form>
</div>
