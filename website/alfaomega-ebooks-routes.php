<?php

use AlfaomegaEbooks\Http\RouteManager;
use AlfaomegaEbooks\Services\eBooks\Service;

$purchase = true;
$redirectUrl = is_user_logged_in()
    ? get_permalink( get_option('woocommerce_myaccount_page_id') ) . '/downloads'
    : get_permalink( get_option('woocommerce_myaccount_page_id') );

$myEbooksPage = get_page_by_path('my-ao-ebooks');
$myEbooksUrl = get_permalink($myEbooksPage->ID);

switch (get_query_var('param_1')) {
    // Open the eBooks reader with the requested eBook
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
            $ebookId = intval(get_query_var('param_2'));
            $purchase = isset($_GET['key']);
            $accessKey = $purchase ?  $_GET['key'] : ($_GET['access'] ?? '');
            Service::make()->ebooks()->read($ebookId, $accessKey, $purchase);
        } catch (Exception $e) {
            $_SESSION['alfaomega_ebooks_msg'] = [
                'type' => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks')
            ];
            wp_safe_redirect( $purchase ? $redirectUrl : $myEbooksUrl);
        }

        exit;
    // Download the xml with eBook information to use in the reader
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
            // TODO: check the download logic from the access post
            $ebookId = intval(get_query_var('param_2'));
            $purchase = isset($_GET['key']);
            $accessKey = $purchase ?  $_GET['key'] : ($_GET['access'] ?? '');
            $filePath = Service::make()->ebooks()->download($ebookId, $accessKey, $accessKey);
            if (empty($filePath)) {
                $_SESSION['alfaomega_ebooks_msg'] = [
                    'type' => 'error',
                    'message' => esc_html__('The requested eBook is not available.', 'alfaomega-ebooks')
                ];
                wp_safe_redirect( $purchase ? $redirectUrl : $myEbooksUrl);
                exit;
            }

            $filename = basename($filePath);
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Type: application/octet-stream");
            header("Content-Length: " . filesize($filePath));
            readfile($filePath);
            exit;

        } catch (Exception $e) {
            $_SESSION['alfaomega_ebooks_msg'] = [
                'type' => 'error',
                'message' => esc_html__($e->getMessage(), 'alfaomega-ebooks')
            ];
            wp_safe_redirect( $redirectUrl);
        }

        exit;
    // api endpoints calls from the front-end
    case 'api':
        $routeManager = new RouteManager();
        $routeManager->callEndpoint(get_query_var('param_2'));
        exit;
    case 'webhook':
        $routeManager = new RouteManager();
        $routeManager->callWebhooks(get_query_var('param_2'));
        exit;
    default:
        $_SESSION['alfaomega_ebooks_msg'] = [
            'type' => 'error',
            'message' => esc_html__('Unknown action requested.', 'alfaomega-ebooks')
        ];
        wp_safe_redirect( $redirectUrl);
        exit;
}
