<?php

session_start();

define('APP_ROOT', realpath(dirname(__FILE__)));
include 'config.php';

if( isset($_SESSION['logged_in']) ) {
  header('Location: index.php');
}

if( isset($_POST['username']) && isset($_POST['password']) ) {
  if( $_POST['username'] == USERNAME && $_POST['password'] == PASSWORD ) {
    $_SESSION['logged_in'] = true;
    header('Location: index.php');
  }else {
    $error = true;
  }
}

?>
<DOCTYPE html>
<html lang="en">
<head>
<title>Login | GitHub Issues</title>
<meta charset="utf-8" />

<link rel="stylesheet" href="css/bootstrap.min.css" />
<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
<style>
  body {
    padding: 60px 0 40px;
  }
</style>
</head>
<body>
  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <span class="brand"><?php echo "GitHub Issues" ?></span>
        
        <div class="nav-collapse">
          <ul class="nav">
            <li class="active"><a href="#">Log In</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  
  <div class="container">
    <form method="POST">
      
      <?php if( isset($error) ) : ?>
        <p class="alert alert-error">The username and password did not match</p>
      <?php endif ?>
      
      <label for="username">Username</label>
      <input type="text" name="username" id="username" />
      
      <label for="password">Password</label>
      <input type="password" name="password" id="password" />
      
      <div class="form-actions">
        <button class="btn btn-primary">Log In</button>
      </div>
    </form>
  </div>
</body>
</html>