<!DOCTYPE html>
<html lang="en">
<head>
<title>GitHub Issues | Page Error</title>
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
        <span class="brand">GitHub Issues | Page Error</span>
      </div>
    </div>
  </div>
  
  <div class="container">
    <section>
      <h2>There was a problem loading the page</h2>
      <p>
        The following error was thrown when this page was loaded.
      </p>
      
      <p><code><?php echo $exception->getMessage() ?></code></p>
      
      <h2>Stack Trace</h2>
      
      <pre><code><?php echo $exception->getTraceAsString() ?></code></pre>
    </section>
  </div>
</body>
</html>