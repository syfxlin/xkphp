<?php use App\Facades\V; ?>

<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
</head>

<body>
  <form action="/register" method="post">
    <input type="text" name="username">
    <?php if (V::error('username')): ?>
      <p><?php V::echo(V::error('username')); ?></p>
    <?php endif; ?>
    <input type="text" name="nickname">
    <?php if (V::error('nickname')): ?>
      <p><?php V::echo(V::error('nickname')); ?></p>
    <?php endif; ?>
    <input type="email" name="email">
    <?php if (V::error('email')): ?>
      <p><?php V::echo(V::error('email')); ?></p>
    <?php endif; ?>
    <input type="password" name="password">
    <?php if (V::error('password')): ?>
      <p><?php V::echo(V::error('password')); ?></p>
    <?php endif; ?>
    <input type="password" name="password_confirmed">
    <?php if (V::error('password_confirmed')): ?>
      <p><?php V::echo(V::error('password_confirmed')); ?></p>
    <?php endif; ?>
    <input type="submit" value="Register">
    <?php V::csrf(); ?>
  </form>
</body>

</html>
