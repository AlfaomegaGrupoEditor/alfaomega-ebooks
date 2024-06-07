<?php
    $service = new Alfaomega_ebooks_Service();
    $key = $_GET['key'] ?? '';
    $path = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
    $data = $service->getReaderData(intval($path[array_key_last($path)]), $key);
?>
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
        theme-name = "default"
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
