<?php

include_once("config.php");
include_once("modules/vbfunctions.php");

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

if (isLoggedIn()) {
    $curUser = ucwords(str_replace('aod_', '', $_SESSION['username']));    
}


function isLoggedIn() {

    if (isset($_SESSION['loggedIn'])) {
        if ($_SESSION['loggedIn'] === true) {
            return true;
        } 
    }
    return false;
}

function define_pages() {

/*
    'picture'   => "/picture/(?'text'[^/]+)/(?'id'\d+)",    // '/picture/some-text/51'
    'album'     => "/album/(?'album'[\w\-]+)",              // '/album/album-slug'
    'category'  => "/category/(?'category'[\w\-]+)",        // '/category/category-slug'
    'page'      => "/page/(?'page'about|contact)",          // '/page/about', '/page/contact'
    'post'      => "/(?'post'[\w\-]+)",                     // '/post-slug'
    'home'      => "/"
*/
    
    // build page rules for routing system
    $rules = array(
        'player'    => "/player/(?'id'\d+)",
        'game'      => "/game/(?'game'bf4|wf|aa|a3)",
        'register'     => "/register",
        'recruit'   => "/recruit",
        'home'      => "/"
        );
    
    return $rules;
}

function generateUrl($arg, $val)
{
    if ($_GET) {
        $string = $_GET;
        $string[$arg] = $val;
        $string = array_unique($string);
    }
    else {
        $string = array($arg => $val);
    }

    return http_build_query($string);
}

function forceEmptyMessageIfNull($value) {
    $value = (!empty($value)) ? $value : "Information missing...";
    return $value;
}


function dbConnect()
{
    global $pdo;
    $conn = '';
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    catch(PDOException $e) {
        if (DEBUG_MODE) echo "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i><strong>Database connection error</strong>: " . $e->getMessage() . "</div>";
    }

    return true;
}


function getGames() {

    global $pdo;

    if(dbConnect()) {

        try {
            $query = "SELECT id, short_name, full_name, subforum, description FROM games ORDER BY full_name";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();

        } catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;  
}


function get_game_info($gid) {

    global $pdo;

    if(dbConnect()) {

        if (!is_null($gid)) {

            try {

                $query = "SELECT id, short_name, full_name, subforum, description FROM games WHERE id = {$gid}";
                $query = $pdo->prepare($query);
                $query->execute();

            } catch (PDOException $e) {
                echo "ERROR:" . $e->getMessage();
            }

        } else {

            return false;
            exit;
        }

    }

    return $query;  
}


function get_game_threads($gid) {

    global $pdo;

    if(dbConnect()) {

        try {

            // grab division threads as well as clan threads (id=0)
            $query = "SELECT thread_url, thread_title FROM games_threads WHERE game_id = {$gid} || game_id = 0";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();

        } catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }

    return $query;  
}


function userExists($string) {
    global $pdo;
    
    if (dbConnect()) {
        if (isset($string)) {
            $string = strtolower($string);
            
            try {
                $sth = $pdo->prepare('SELECT count(*) FROM users WHERE username= :username LIMIT 1');
                $sth->bindParam(':username', $string);
                $sth->execute();
                $count = $sth->fetchColumn();
                
            }
            catch (PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
            
            if ($count > 0) {
                return true;
            } else {
                return false;
            }
            
            
            if (!$count) {
                die('Could not get data (userexists): ' . mysql_error());
            }
            
        } else {
            die('Cant connect to mysql (userexists)');
        }
    }
}


function createUser($user, $email, $credential) {
    global $pdo;
    
    if (dbConnect()) {
        try {
            $cost  = 10;
            $salt  = strstr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
            $salt  = sprintf("$2a$%02d$", $cost) . $salt;
            $hash  = crypt($credential, $salt);
            $user  = strtolower($user);
            $query = $pdo->prepare("INSERT INTO users ( username, credential, email, ip, date_joined) VALUES ( :user, :pass, :email, :ip, CURRENT_TIMESTAMP() )");

            $query->execute(array(
                ':user' => $user,
                ':pass' => $hash,
                ':email' => $email,
                ':ip' => $_SERVER['REMOTE_ADDR']
                ));
            return $pdo->lastInsertId();
            
        }
        catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
        
    } else {
        return false;
    }
}


function validatePassword($hash, $user) {
    global $pdo;
    $user = strtolower($user);
    
    if (dbConnect()) {
        try {
            $sth = $pdo->prepare('SELECT credential FROM users WHERE username = :username LIMIT 1');
            $sth->bindParam(':username', $user);
            $sth->execute();

            $user = $sth->fetch(PDO::FETCH_OBJ);

        }
        catch (PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
    if (crypt($hash, $user->credential) === $user->credential) {
        return true;
    } else {
        return false;
    }
    
}



function checkThread($player, $thread) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $thread . "&page=9999999");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $getPosts      = curl_exec($ch);
    $countPosts = stripos($getPosts, $player);
    if ($countPosts) { return true; } else { return false; }

}




?>