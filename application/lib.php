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
        'platoon'    => "/(?'game'bf4|wf|aa|a3)/platoon/(?'platoon'\d+)",
        'register'     => "/register",
        'logout'   => "/logout",
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


function get_member_info($name) {

    global $pdo;

    if(dbConnect()) {

        if (!is_null($gid)) {

            try {

                $query = "SELECT member_id, forum_name, game_id, subforum, description FROM games WHERE id = {$gid}";
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

function hasher($info, $encdata = false) 
{ 
  $strength = "10"; 
  
  //if encrypted data is passed, check it against input ($info) 
  if ($encdata) { 
    if (substr($encdata, 0, 60) == crypt($info, "$2a$".$strength."$".substr($encdata, 60))) { 
      return true; 
  } else { 
      return false; 
  } 
} else { 

    //make a salt and hash it with input, and add salt to end 
    $salt = ""; 
    for ($i = 0; $i < 22; $i++) { 
      $salt .= substr("./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", mt_rand(0, 63), 1); 
  } 
    //return 82 char string (60 char hash & 22 char salt) 
  return crypt($info, "$2a$".$strength."$".$salt).$salt; 
} 
} 

function updateLoggedInTime($user) {
   global $pdo;

   if (dbConnect()) {
    try {

        $user  = strtolower($user);
        $query = $pdo->prepare("UPDATE users SET last_logged = CURRENT_TIMESTAMP() WHERE username = :user");
        $query->execute(array( ':user' => $user ));            
    }
    catch (PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
} else {
    return false;
}
}

function createUser($user, $email, $credential) {
    global $pdo;
    
    if (dbConnect()) {
        try {

            $hash = hasher($credential);
            
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


function validatePassword($pass, $user) {
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

    if ($pass == hasher($pass, $user->credential)) {
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




/**
 * add scorpion's functions
 */


function get_members() {

    global $pdo;

    if(dbConnect()) {

        try {

            #$query = "SELECT * FROM member WHERE status_id=1 ORDER BY `rank_id` DESC";
            $query = "SELECT member.forum_name, member.member_id, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr FROM `member` LEFT JOIN `rank` on member.rank_id = rank.id WHERE status_id=1 ORDER BY member.rank_id DESC";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();

        } catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;  
}

function get_platoon_members($platoon_id) {

    global $pdo;

    if(dbConnect()) {

        try {

            #$query = "SELECT * FROM member WHERE status_id=1 AND platoon_id=".$platoon_id." ORDER BY `rank_id` DESC";
            $query = "SELECT member.forum_name, member.member_id, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr FROM `member` LEFT JOIN `rank` on member.rank_id = rank.id WHERE status_id=1 AND platoon_id=".$platoon_id." ORDER BY member.rank_id DESC";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();

        } catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;  
}

function get_platoons() {

    global $pdo;

    if(dbConnect()) {

        try {

            $query = "SELECT * FROM platoon WHERE 1 ORDER BY number";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();

        } catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;  
}

function get_platoon_info($platoon_id) {

    global $pdo;

    if(dbConnect()) {

        try {

            $query = "SELECT `name`, `number` FROM platoon WHERE id = {$platoon_id}";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();

        } catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;  
}


function get_platoon_id_from_number($platoon_number) {

    global $pdo;

    if(dbConnect()) {

        try {

            $query = "SELECT id FROM platoon WHERE number=".$platoon_number;
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetch();

        } catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;  
}

function count_total_games($member_id,$date) {

    global $pdo;

    if(dbConnect()) {

        $first_day_of_month = date("Y-m-d", strtotime("first day of".$date));
        $last_day_of_month = date("Y-m-d", strtotime("last day of".$date));

        #SELECT YEAR(TRUNC_HOUR(CONVERT_TZ(datetime,'+00:00','-06:00'))) AS year, MONTH(TRUNC_HOUR(CONVERT_TZ(datetime,'+00:00','-06:00'))) AS month, DAY(TRUNC_HOUR(CONVERT_TZ(datetime,'+00:00','-06:00'))) AS day, HOUR(TRUNC_HOUR(CONVERT_TZ(datetime,'+00:00','-06:00'))) AS hour, COUNT(DISTINCT member_id) AS games FROM activity WHERE server LIKE 'AOD%' GROUP BY TRUNC_HOUR(CONVERT_TZ(datetime,'+00:00','-06:00')) ORDER BY year, month, day, hour
        #SELECT DATE_FORMAT(TRUNC_HOUR(CONVERT_TZ(datetime,'+00:00','-06:00')), '%m/%d/%Y') AS date, HOUR(TRUNC_HOUR(CONVERT_TZ(datetime,'+00:00','-06:00'))) AS hour, COUNT(DISTINCT member_id) AS games FROM activity WHERE server LIKE 'AOD%' GROUP BY TRUNC_HOUR(CONVERT_TZ(datetime,'+00:00','-06:00')) ORDER BY date, hour
        
        # count total games played for all members
        #SELECT `member_id`, count(*) AS games FROM `activity` where `datetime` between '2014-11-01 00:00:00' and '2014-11-30 23:59:59' GROUP BY `member_id`
        
        # count total games played for a single member
        try {
            $query = "SELECT count(*) AS games FROM `activity` where `member_id`=".$member_id." AND `datetime` between '".$first_day_of_month." 00:00:00' and '".$last_day_of_month." 23:59:59'";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();
        } catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query[0]['games'];  
}

function count_aod_games($member_id,$date) {

    global $pdo;

    if(dbConnect()) {

        $first_day_of_month = date("Y-m-d", strtotime("first day of".$date));
        $last_day_of_month = date("Y-m-d", strtotime("last day of".$date));     
        
        # count total AOD games played for a single member
        try {
            $query = "SELECT count(*) AS games FROM `activity` where `member_id`=".$member_id." AND `server` LIKE 'AOD%' AND `datetime` between '".$first_day_of_month." 00:00:00' and '".$last_day_of_month." 23:59:59'";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();
        } catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query[0]['games'];  
}



?>
