<?php

class GitHub {
  
  public static function all_issues() {
    return GitHub_Client::get_json('/user/issues');
  }
  
  public static function repositories() {
    return GitHub_Client::get_json('/user/repos?type=all&sort=pushed');    
  }
  
  /**
   * Finds all organizations and their repos
   *
   * @return array
   * @author Baylor Rae'
   */
  public static function organization_repos() {
    $orgs_repos = array();
    $orgs = GitHub_Client::get_json('/user/orgs');
    
    // loop through the organizations and add the repos
    foreach( $orgs as $org ) {    
      $repos = GitHub_Client::get_json(sprintf('/orgs/%s/repos?type=all&sort=pushed', $org->login));
      
      $orgs_repos[$org->login] = $repos;
    }
    
    return $orgs_repos;
  }
  
  /**
   * A basic autoloader for my
   * GitHub API wrapper
   *
   * @package default
   * @author Baylor Rae'
   */
  public static function autoload($class_name) {
    if( substr($class_name, 0, 7) == 'GitHub_' ) {
      $file_path = sprintf(APP_ROOT . '/classes/%s.php', strtolower(str_replace('_', '/', $class_name)));
      if( file_exists($file_path) ) {
        include_once $file_path;
      }
    }
  }
  
}