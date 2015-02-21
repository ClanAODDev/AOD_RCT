<?php

class GitHub_Client {
  
  const API_BASE = 'https://api.github.com';
  
  public static function post($url, array $data) {
    if( !defined('GITHUB_ACCESS_TOKEN') ) {
      return false;
    }
    
    $url = self::API_BASE . $url;
    $curl = curl_init($url);
    
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_USERAGENT => 'GitHub API Client',
      CURLOPT_HTTPHEADER => array('Authorization: bearer ' . GITHUB_ACCESS_TOKEN, 'Content-Type: application/json'),
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_POST => true
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);
    
    if( empty($response) ) {
      return false;
    }
    
    return json_decode($response);
  }
  
  public static function get_json($url) {
    if( !defined('GITHUB_ACCESS_TOKEN') ) {
      return false;
    }
    
    $url = self::API_BASE . $url;
    $curl = curl_init($url);
    
    if( $cache = GitHub_Cache::get($url, CACHE_TIME) ) {
      return json_decode($cache);
    }
    
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_USERAGENT => 'GitHub API Client',
      CURLOPT_HTTPHEADER => array('Authorization: bearer ' . GITHUB_ACCESS_TOKEN)
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);
    
    if( empty($response) ) {
      return false;
    }
    
    GitHub_Cache::set($url, $response);
    
    return json_decode($response);
  }
}