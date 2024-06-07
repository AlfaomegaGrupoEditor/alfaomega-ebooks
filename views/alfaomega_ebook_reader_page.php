<?php
    $title = '';
    $favicon = '';
    $readerUrl = '';
    $isbn = '';
    $libraryBaseUrl = '';
    $token = '';
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="referrer" content="origin-when-cross-origin" />
    <title><?php echo $title; ?></title>
    <link rel="icon" href="<?php echo $favicon; ?>" type="image/x-icon" />
    <link href="<?php echo $readerUrl; ?>/css/app.css" rel="stylesheet"/>
</head>
<body>
    <noscript>
        <strong>We're sorry but the Reader doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
    </noscript>

    <html-book
        id = "book"
        book-isbn = "<?php echo $isbn; ?>"
        theme-name = "default"
        class = "bibliotecas"
        library-scr = "<?php echo $libraryBaseUrl; ?>"
        mode = "local"
        teacher = "enabled"
        evaluations = "inactive"
        token = "<?php echo $token; ?>"
    >
    </html-book>
    <script src="<?php echo $readerUrl; ?>/js/app.js"></script>
</body>
