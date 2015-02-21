<?php

session_start();

$access_token = $_SESSION['access_token'];

$ch = curl_init('https://api.github.com/repos/' . $_GET['repo'] . '/issues?type=all&sort=pushed&access_token=' . $_SESSION['access_token']);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$repos = json_decode(curl_exec($ch));
curl_close($ch);
  
?>
  <ol>
    <?php foreach( $repos as $repo ) : ?>
      <li><?php echo $repo->title ?></li>
    <?php endforeach ?>
  </ol>