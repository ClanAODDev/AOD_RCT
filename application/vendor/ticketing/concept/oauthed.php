<?php

session_start();

$ch = curl_init('https://github.com/login/oauth/access_token');

curl_setopt_array($ch, array(
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POSTFIELDS => array(
    'client_id' => '9c54e9d5f82b89e60316',
    'client_secret' => 'bcbf037d5f6259ecf29facb36a6f101215519bf6',
    'code' => $_GET['code'],
    'state' => $_SESSION['api_state']
  ),
  CURLOPT_HTTPHEADER => array('Accept: application/json')
));

$data = json_decode(curl_exec($ch));
curl_close($ch);

$_SESSION['access_token'] = $data->access_token;
$_SESSION['token_type'] = $data->token_type;

header('Location: /');