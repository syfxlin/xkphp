<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8" />
    <link rel="icon" href="<?php $asset('favicon.ico'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#000000" />
    <meta name="description" content="Web site created using create-react-app" />
    <meta name="csrf-token" content="<?php echo $csrf_token; ?>">
    <link rel="apple-touch-icon" href="<?php $asset('logo192.png'); ?>" />
    <link rel="manifest" href="<?php $asset('manifest.json'); ?>" />
    <link href="<?php $asset('css/main.css'); ?>" rel="stylesheet" />
    <title>React App</title>
</head>

<body>
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="root"></div>
    <script src="<?php $asset('js/runtime-main.js'); ?>"></script>
    <script src="<?php $asset('js/vendors-main.js'); ?>"></script>
    <script src="<?php $asset('js/main.js'); ?>"></script>
</body>

</html>
