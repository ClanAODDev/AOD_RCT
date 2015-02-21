<!DOCTYPE html>
<html lang="en">
<head>
<title><?php if( defined('PAGE_TITLE') ) echo PAGE_TITLE, " | " ?><?php echo "GitHub Issues" ?></title>
<meta charset="utf-8" />

<link rel="stylesheet" href="css/bootstrap.min.css" />
<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="css/prettify.css" />
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
        <a class="brand" href="index.php"><?php echo "GitHub Issues" ?></a>
        
        <div class="nav-collapse">
          <ul class="nav">
            <li class="active"><a href="index.php">Repositories</a></li>
            <li><a href="logout.php">Log Out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>