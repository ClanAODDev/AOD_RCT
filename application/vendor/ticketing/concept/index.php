<?php

session_start();

include 'vendor/autoload.php';

if( empty($_SESSION['access_token']) ) {
  
  if( empty($_SESSION['api_state']) ) {
    $_SESSION['api_state'] = md5(microtime(true));
  }
  
?>
  <a href="https://github.com/login/oauth/authorize?client_id=9c54e9d5f82b89e60316&amp;scope=repo">Sign in with GitHub</a>
<?php  
}else {
  $client = new Github\Client();

  $client->authenticate($_SESSION['access_token'], null, Github\Client::AUTH_HTTP_TOKEN);
  
  $ch = curl_init('https://api.github.com/user/repos?type=all&sort=pushed&access_token=' . $_SESSION['access_token']);
  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
  $repos = json_decode(curl_exec($ch));
  curl_close($ch);
  
?>
  <ol>
    <?php foreach( $repos as $repo ) : ?>
      <li>
        <a href="issues.php?repo=<?php echo $repo->full_name ?>"><?php echo $repo->name ?></a>
      </li>
    <?php endforeach ?>
  </ol>
<?php
}