<?php

class GitHub_Cache {
  
  public static $cache_directory_base = CACHE_PATH;

  /**
   * Finds a current cache
   *
   * @param string $key 
   * @param integer $expiration 
   * @return mixed
   * @author Baylor Rae'
   */
  public static function get($key, $expiration = 3600) {
    $filename = self::name($key);
    
    // check if the file exists
    if( !file_exists($filename) ) {
      return false;
    }
    
    // check if the file has "expired"
    if( filemtime($filename) < (time() - $expiration) ) {
      unlink($filename);
      return false;
    }
    
    // return the cache
    return file_get_contents($filename);
  }
  
  /**
   * Saves the cache file
   *
   * @param string $key 
   * @param string $value 
   * @return string
   * @author Baylor Rae'
   */
  public static function set($key, $value) {
    $filename = self::name($key);
    
    if( !is_writeable(self::cache_path()) ) {
      throw new Exception(self::cache_path() . ' is not writeable');
    }
    
    // save the cache
    file_put_contents($filename, $value);
  }
  
  /**
   * Get the path of the cache directory
   *
   * @return string
   * @author Baylor Rae'
   */
  private static function cache_path() {
    return self::$cache_directory_base;
  }
  
  /**
   * Hashes the $key
   * and returns the path of the cache
   *
   * @param string $key 
   * @return string
   * @author Baylor Rae'
   */
  private static function name($key) {
    return sprintf('%s/%s', self::cache_path(), sha1($key));
  }
}