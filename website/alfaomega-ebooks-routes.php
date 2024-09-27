<?php

use AlfaomegaEbooks\Http\RouteManager;
use AlfaomegaEbooks\Services\eBooks\Service;

$purchase = true;
$redirectUrl = is_user_logged_in()
    ? get_permalink( get_option('woocommerce_myaccount_page_id') ) . '/downloads'
    : get_permalink( get_option('woocommerce_myaccount_page_id') );

$myEbooksPage = get_page_by_path('my_ebooks');
$myEbooksUrl = get_permalink($myEbooksPage->ID);

switch (get_query_var('param_1')) {
    case 'read':
        if (!is_user_logged_in()) {
            $_SESSION['alfaomega_ebooks_msg'] = [
                'type' => 'error',
                'message' => esc_html__('Please log in to read ebooks.', 'alfaomega-ebooks')
            ];
            wp_safe_redirect( $redirectUrl);
            exit;
        }

        try {
            // $service = new Alfaomega_Ebooks_Service();
            // $service->readEbook($ebookId, $_GET['key'] ?? '');
            $ebookId = intval(get_query_var('param_2'));
            $purchase = isset($_GET['key']);
            $accessKey = $purchase ?  $_GET['key'] : $_GET['access'];
            Service::make()->ebooks()
                ->read($ebookId, $accessKey, $purchase);
        } catch (Exception $e) {
            $_SESSION['alfaomega_ebooks_msg'] = [
                'type' => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks')
            ];
            wp_safe_redirect( $purchase ? $redirectUrl : $myEbooksUrl);
        }

        exit;
    case 'download':
        if (!is_user_logged_in()) {
            $_SESSION['alfaomega_ebooks_msg'] = [
                'type' => 'error',
                'message' => esc_html__('Please log in to download ebooks.', 'alfaomega-ebooks')
            ];
            wp_safe_redirect( $purchase ? $redirectUrl : $myEbooksUrl);
            exit;
        }

        try {
            // $service = new Alfaomega_Ebooks_Service();
            // $service->readEbook($ebookId, $_GET['key'] ?? '');
            $ebookId = intval(get_query_var('param_2'));
            $purchase = isset($_GET['key']);
            $accessKey = $purchase ?  $_GET['key'] : $_GET['access'];
            Service::make()->ebooks()
                ->download($ebookId, $accessKey, $accessKey);
        } catch (Exception $e) {
            $_SESSION['alfaomega_ebooks_msg'] = [
                'type' => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks')
            ];
            wp_safe_redirect( $redirectUrl);
        }

        exit;
    case 'api':
        $routeManager = new RouteManager();
        $routeManager->callEndpoint(get_query_var('param_2'));
        exit;
    default:
        $_SESSION['alfaomega_ebooks_msg'] = [
            'type' => 'error',
            'message' => esc_html__('Unknown action requested.', 'alfaomega-ebooks')
        ];
        wp_safe_redirect( $redirectUrl);
        exit;
}
