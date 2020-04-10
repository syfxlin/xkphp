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
    <?php if ($error('username')): ?>
      <p><?php $echo($error('username')); ?></p>
    <?php endif; ?>
    <input type="text" name="nickname">
    <?php if ($error('nickname')): ?>
      <p><?php $echo($error('nickname')); ?></p>
    <?php endif; ?>
    <input type="email" name="email">
    <?php if ($error('email')): ?>
      <p><?php $echo($error('email')); ?></p>
    <?php endif; ?>
    <input type="password" name="password">
    <?php if ($error('password')): ?>
      <p><?php $echo($error('password')); ?></p>
    <?php endif; ?>
    <input type="password" name="password_confirmed">
    <?php if ($error('password_confirmed')): ?>
      <p><?php $echo($error('password_confirmed')); ?></p>
    <?php endif; ?>
    <input type="submit" value="Register">
    <?php echo $csrf; ?>
  </form>
</body>

</html>
