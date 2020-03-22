<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
</head>

<body>
  <form action="/login" method="post">
    <input type="text" name="account">
    <input type="password" name="password">
    <input type="checkbox" name="remember_me">
    <input type="submit" value="Login">
    <?php echo $csrf; ?>
  </form>
</body>

</html>
