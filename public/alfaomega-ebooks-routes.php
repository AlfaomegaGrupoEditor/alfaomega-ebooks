<?php

$redirectUrl = is_user_logged_in()
    ? get_permalink( get_option('woocommerce_myaccount_page_id') ) . '/downloads'
    : get_permalink( get_option('woocommerce_myaccount_page_id') );

switch (get_query_var('param_1')) {
    case 'download':
        if (!is_user_logged_in()) {
            $_SESSION['alfaomega_ebooks_msg'] = [
                'type' => 'error',
                'message' => esc_html__('Please log in to download ebooks.', 'alfaomega-ebooks')
            ];
            wp_safe_redirect( $redirectUrl);
            exit;
        }

        try {
            $service = new Alfaomega_Ebooks_Service();
            $service->downloadEbook(get_query_var('param_2'), $_GET['id'] ?? '');
            $_SESSION['alfaomega_ebooks_msg'] = [
                'type' => 'success',
                'message' => esc_html__('Please upload the downloaded license file into your eBook reader. Notice that you can also access the ebook to read online.', 'alfaomega-ebooks')
            ];
        } catch (Exception $e) {
            $_SESSION['alfaomega_ebooks_msg'] = [
                'type' => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks')
            ];
            wp_safe_redirect( $redirectUrl);
        }

        exit;
    default:
        $_SESSION['alfaomega_ebooks_msg'] = [
            'type' => 'error',
            'message' => esc_html__('Unknown action requested.', 'alfaomega-ebooks')
        ];
        wp_safe_redirect( $redirectUrl);
        exit;
}
