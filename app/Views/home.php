<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <p><?php $echo($csrf_token); ?></p>
    <p><?php $include('part'); ?></p>
    <p><?php $json(['json' => 'data']); ?></p>
</body>

</html>
