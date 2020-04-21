<?php use App\Facades\V; ?>
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
    <?php if (V::error('account')): ?>
      <p><?php V::echo(V::error('account')); ?></p>
    <?php endif; ?>
    <input type="password" name="password">
    <?php if (V::error('password')): ?>
      <p><?php V::echo(V::error('password')); ?></p>
    <?php endif; ?>
    <input type="checkbox" name="remember_me">
    <input type="submit" value="Login">
    <?php V::csrf(); ?>
  </form>
</body>

</html>
