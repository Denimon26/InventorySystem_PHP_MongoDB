<?php
ob_start();
require_once('includes/load.php');
if ($session->isUserLoggedIn(true)) {
  redirect('home.php', false);
}
?>
<link rel="stylesheet" href="libs/css/main.css" />
<?php echo display_msg($msg); ?>
<div class="login-page">
  <div class="panel-form">
    <div class="panel-heading-form">
      <strong>
        <span class="glyphicon glyphicon-user"></span>
        <span>Login</span>
      </strong>
    </div>
    <div class="panel-body">
      <div class="col-md-6">
        <form method="post" action="auth_v2.php">
          <div class="form-group">
            <label for="username" class="control-label">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Username" required>
          </div>
          <div class="form-group">
            <label for="password" class="control-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
          </div>
          <div class="form-group clearfix">
            <button type="submit" class="btn btn-primary">Login</button>
          </div>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>

      </div>
    </div>
  </div>
</div>