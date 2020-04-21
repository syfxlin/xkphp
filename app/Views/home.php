<?php use App\Facades\V; ?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8" />
    <link rel="icon" href="<?php V::asset('favicon.ico'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#000000" />
    <meta name="description" content="Web site created using create-react-app" />
    <?php V::csrfMetaTag(); ?>
    <link rel="apple-touch-icon" href="<?php V::asset('logo192.png'); ?>" />
    <link rel="manifest" href="<?php V::asset('manifest.json'); ?>" />
    <link href="<?php V::asset('css/main.css'); ?>" rel="stylesheet" />
    <title>React App</title>
</head>

<body>
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="root"></div>
    <?php V::push('script'); ?>
        <script src="js/vendors-main.js"></script>
    <?php V::endpush(); ?>
    <?php V::push('script'); ?>
        <script src="js/main.js"></script>
    <?php V::endpush(); ?>
    <?php V::prepend('script'); ?>
        <script src="js/runtime-main.js"></script>
    <?php V::endprepend(); ?>
    <?php V::stack('script'); ?>
</body>

</html>
