<?php
    $purchase = null;
    try {
        $purchase = isset($_GET['key']);
        $service = \AlfaomegaEbooks\Services\eBooks\Service::make()->ebooks();
        $accessKey = $purchase ?  $_GET['key'] : ($_GET['access'] ?? '');

        $path = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
        $eBookId = intval($path[array_key_last($path)]);
        if (empty($eBookId)) {
            array_pop($path);
            $eBookId = intval($path[array_key_last($path)]);
        }
        $error = null;
        $data = $service->getReaderData($eBookId, $accessKey, $purchase);
    } catch (Exception $e) {
        $purchase = is_null($purchase) || $purchase;
        $error = $e->getMessage();
        $data = null;
    }
?>
<?php if(empty($data)):?>
    <?php
        $message = [
            'type'          => 'error',
            'message'       => esc_html__(
                'Online eBook not available, please check order status or contact support.',
                'alfaomega-ebooks'),
            'error_details' => json_decode($error),
            'referer'       => $_SERVER['HTTP_REFERER'],
        ];
        $_SESSION['alfaomega_ebooks_msg'] = $message;

        if ($purchase) {
            $redirectUrl = is_user_logged_in()
                ? get_permalink( get_option('woocommerce_myaccount_page_id') ) . '/downloads'
                : get_permalink( get_option('woocommerce_myaccount_page_id') );
        } else {
            $myEbooksPage = get_page_by_path('my-ao-ebooks');
            $redirectUrl = get_permalink($myEbooksPage->ID);
        }
        if (empty($redirectUrl)) {
            $redirectUrl = $redirectUrl = home_url();;
        }
    ?>
    <script type="text/javascript">
        sessionStorage.setItem('alfaomega_ebooks_msg', '<?php echo json_encode($message); ?>');
        window.location.href = "<?php echo $redirectUrl; ?>";
    </script>
<?php else: ?>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <meta name="referrer" content="origin-when-cross-origin" />
        <title><?php echo $data['title']; ?></title>
        <link rel="icon" href="<?php echo $data['favicon']; ?>" type="image/x-icon" />
        <link href="<?php echo $data['readerUrl']; ?>/css/app.css" rel="stylesheet"/>
    </head>
    <body>
        <noscript>
            <strong>We're sorry but the Reader doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
        </noscript>

        <html-book
            id = "book"
            book-isbn = "<?php echo $data['isbn']; ?>"
            theme-name = "store"
            class = "bibliotecas"
            library-scr = "<?php echo $data['libraryBaseUrl']; ?>"
            mode = "local"
            teacher = "enabled"
            evaluations = "inactive"
            token = "<?php echo $data['token']; ?>"
        >
        </html-book>
        <script src="<?php echo $data['readerUrl']; ?>/js/app.js"></script>
    </body>
<?php endif; ?>
