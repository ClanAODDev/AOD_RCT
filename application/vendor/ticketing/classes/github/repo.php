<?php

class GitHub_Repo {
  
  public static function find($owner, $repo) {
    return GitHub_Client::get_json(sprintf('/repos/%s/%s', $owner, $repo));
  }
  
}