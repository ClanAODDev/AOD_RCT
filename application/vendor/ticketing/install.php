<?php

define('APP_ROOT', realpath(dirname(__FILE__)));

include './config.php';

// already installed
if( defined('GITHUB_ACCESS_TOKEN') ) {
  header('Location: index.php');
}

if( !empty($_GET['register']) ) {
  
  if( defined('GITHUB_CLIENT_ID') && defined('GITHUB_CLIENT_SECRET') ) {
    header(sprintf('Location: https://github.com/login/oauth/authorize?client_id=%s&scope=repo', GITHUB_CLIENT_ID));
  }else {
    $message = "You didn't add your <code>Client ID</code> or <code>Client Secret</code>";
  }
}

if( !empty($_GET['code']) ) {
  
  $ch = curl_init('https://github.com/login/oauth/access_token');
  
  curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => array(
      'client_id' => GITHUB_CLIENT_ID,
      'client_secret' => GITHUB_CLIENT_SECRET,
      'code' => $_GET['code']
    ),
    CURLOPT_HTTPHEADER => array('Accept: application/json')
  ));
  
  $data = json_decode(curl_exec($ch));
  curl_close($ch);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Install | GitHub Issues</title>
<meta charset="utf-8" />

<link rel="stylesheet" href="css/bootstrap.min.css" />
<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="css/prettify.css" />
<style>
  body {
    padding: 60px 0 40px;
  }
  
  .prefix {
    font-size: 0.8em;    
    color: #555;
  }

  input {
    display: block;
    margin-top: 1em;
  }
  
  .ready-button-wrapper {
    text-align: center;
    margin: 1em 0;
  }
  
  figure {
    background: #eee;
    padding: 1em;
    display: inline-block;
    margin: 0;
  }
  
</style>
<body>
  
  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <span class="brand">Install GitHub Issues</span>
      </div>
    </div>
  </div>
  
  <div class="container">
  
    <section>
    
      <?php if( !empty($message) ) : ?>
        <p class="alert"><?php echo $message ?></p>
      <?php endif ?>
    
      <?php if( isset($data) && !empty($data->access_token) ) : ?>
        <h2><span class="prefix">Step 4:</span> Add your Access Token</h2>
        <p>
          Congratulations, you have registered your app and you're in the final step!
          Add the following key to <code>/config.php</code> and you can start accessing your
          GitHub issues.
        
          <input class="span12" onclick="this.select();" type="text" value="<?php echo $data->access_token ?>" />
          
          <div class="ready-button-wrapper">
            <a class="btn btn-large btn-primary" href="index.php">Done! I'm ready to use the app</a>
          </div>
        </p>
      <?php else : ?>
      
        <h2>Install GitHub Issues</h2>
        <p>
          To install the GitHub issues application you will need to connect your GitHub account via OAuth.
          Fortunately this can be done very quickly and you will be using the application in no time at all.
        </p>

        <h2><span class="prefix">Step 1:</span> Register Your Application</h2>
        
        <div class="row">
          <div class="span3">
            <p>
              The first thing you will need to do is <a href="https://github.com/settings/applications/new">register a new application</a> on GitHub.
              This will provide you with a <code>Client ID</code> and <code>Client Secret</code> that is specific to your GitHub account.
            </p>

            <p>
              You will need to specify the callback URL when registering the application. Make sure it points back to the current url.
              I'm guessing, but it looks like this should be it.

              <input onclick="this.select();" type="text" value="<?php echo 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'] ?>" />
            </p>
          </div>
          
          <div class="span9">
            <figure>
              <img src="img/install-1.png" />
            </figure>
          </div>
        </div>
        
        <h2><span class="prefix">Step 2:</span> Add Your Application Hashes</h2>

        <div class="row">
          <div class="span3">
            <p>
              Once you've obtained your <code>Client ID</code> and <code>Client Secret</code> you need to put them in <code>/config.php</code>.
              Inside your the configuration file you will see a spot designated for those to hashes.
            </p>
          </div>
          
          <div class="span9">
            <pre class="prettyprint linenums"><code>&lt;?php
/**
 * These definitions are only used before
 * you have defined your access_token.
 * But, you should leave them here just in case
 */
define('GITHUB_CLIENT_ID', 'you should put your client id here');
define('GITHUB_CLIENT_SECRET', 'and your client secret here');</code></pre>
          </div>
        </div>

        <h2><span class="prefix">Step 3:</span> Connect to GitHub</h2>
        <p>
          Now that you've put your <code>Client ID</code> and <code>Client Secret</code> in place you can click the button below to
          allow access to your GitHub account. You will be redirected back to this page with further instruction.
        </p>

        <div class="ready-button-wrapper">
          <a class="btn btn-primary btn-large" href="install.php?register=application">Sign in with GitHub</a>
        </div>
      
      <?php endif ?>
    
    </section>  
  </div>

<script src="js/prettyprint.min.js"></script>
<script>
  prettyPrint();
</script>
</body>
</html>