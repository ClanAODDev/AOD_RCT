<?php

include_once("config.php");
include_once("modules/vbfunctions.php");

/**
 * data collection for user logged in
 */

if (isLoggedIn()) {

    // fetch member data for current user
    $member_info = get_user_info($_SESSION['username']);
    $userRole = $member_info['role'];
    $curUser = $member_info['username'];
    $forumId = $member_info['member_id'];

    if (!is_null($member_info['member_id'])) {
        $avatar = get_user_avatar($member_info['member_id']);
    } else {
        $avatar = NULL;
    }

    

    /**
     * generate game list for navigation
     */
    
    $game_list = NULL;
    $games = get_games();

    foreach ($games as $game) {
        $shortname = strtolower($game['short_name']);
        $longname = $game['full_name'];
        $shortdescr = $game['short_descr'];
        $game_list .= "<li><a href='/{$shortname}'>{$longname}</a></li>";
        // <a href='/{$shortname}' class='list-group-item'><strong>{$longname}</strong><i class='fa fa-angle-double-right pull-right text-muted'></i></a>
    }
}

/**
 * primary functions
 */


/**
 * Checks to see if session data exists
 * @return boolean
 */
function isLoggedIn() {

    if (isset($_SESSION['loggedIn'])) {
        if ($_SESSION['loggedIn'] === true) {
            return true;
        }
    }
    return false;
}


/**
 * Defines rules for router system
 * See commented examples for a guide
 * @return array defined rules
 */
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
        'player' => "/player/(?'id'\d+)",
        'division' => "/(?'division'bf4|hl)",
        'platoon' => "/(?'division'bf4|hl)/platoon/(?'platoon'\d+)",
        'register' => "/register",
        'logout' => "/logout",
        'home' => "/"
        );
    
    return $rules;
}

/**
 * Generates a url based on arguments found in url
 * Not really used in AOD_RCT application
 * @param  varchar $arg url based argument
 * @param  varchar $val value of url based arguement
 * @return array URL encoded array
 */
function generateUrl($arg, $val) {
    if ($_GET) {
        $string       = $_GET;
        $string[$arg] = $val;
        $string       = array_unique($string);
    } else {
        $string = array(
            $arg => $val
            );
    }
    
    return http_build_query($string);
}

function forceEmptyMessageIfNull($value) {
    $value = (!empty($value)) ? $value : "Information missing...";
    return $value;
}

function ordSuffix($n) {
    $str = "$n";
    $t = $n > 9 ? substr($str,-2,1) : 0;
    $u = substr($str,-1);
    if ($t==1) return $str . 'th';
    else switch ($u) {
        case 1: return $str . 'st';
        case 2: return $str . 'nd';
        case 3: return $str . 'rd';
        default: return $str . 'th';
    }
}


function dbConnect() {
    global $pdo;
    $conn = '';
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    catch (PDOException $e) {
        if (DEBUG_MODE)
            echo "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i><strong>Database connection error</strong>: " . $e->getMessage() . "</div>";
    }
    
    return true;
}

function getPercentageColor($pct) {
    if ($pct >= PERCENTAGE_CUTOFF_GREEN) {
        $percent_class = "success";
    } else if ($pct >= PERCENTAGE_CUTOFF_AMBER) {
        $percent_class = "warning"; 
    } else {
        $percent_class = "danger"; 
    }
    return $percent_class;
}


function getGames() {

    global $pdo;
    
    if (dbConnect()) {

        try {
            $query = "SELECT id, short_name, full_name, subforum, description FROM games ORDER BY full_name";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}


/**
 * get_user_info grabs data specifically pertaining to the user logged in
 * @param  [varchar] $name the user's forum name
 * @return [array] $query an array containing member data
 */
function get_user_info($name) {

    global $pdo;
    
    if (dbConnect()) {

        try {

            $sth = $pdo->prepare("SELECT users.id as userid, member_id, username, forum_name, rank_id, role, email, idle, last_logged FROM users 
                LEFT join member on users.username = member.forum_name
                LEFT JOIN rank on member.rank_id = rank.id
                WHERE users.username = :username");
            
            $sth->bindParam(':username', $name);
            $sth->execute();
            $query = $sth->fetch();
            
        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
        
    }
    
    return $query;
}

function onlineUsers() {
    global $pdo;
    
    if (isLoggedIn()) {
        if (dbConnect()) {
            try {
                // grab active users in past 2 minutes
                $sth = $pdo->prepare('SELECT username, role, idle FROM users WHERE last_seen >= CURRENT_TIMESTAMP - INTERVAL 15 MINUTE ORDER BY last_seen DESC');
                $sth->execute();
                $users = $sth->fetchAll();
            }
            catch (PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
            }
            
        }
        return $users;
    } 
}

function forceEndSession() {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
            );
    }

    session_destroy();
}

function updateUserActivityStatus($id) {
    global $pdo;
    
    if (dbConnect()) {

        // set cookie, test for idleness
        $idle = 0;

        if(!isset($_COOKIE['aod_rct_active_count'])) {
            setcookie('aod_rct_active_count', 0, time() + (86400 * 30), '/');
        } else if ($_COOKIE['aod_rct_active_count'] >= 30) {
            $idle = 1;
        } else {
            setcookie('aod_rct_active_count', $_COOKIE['aod_rct_active_count']+1, time() + (86400 * 30), '/');

        }

        try {

            $stmt = $pdo->prepare('UPDATE users SET last_seen = CURRENT_TIMESTAMP(), idle = :idle WHERE id = :id');
            $stmt->bindParam(':idle', $idle, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt ->execute();

        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}




function userColor($user, $level) {

    switch ($level) {
        case 5:
        $span = "<span class='developer tool' title='Developer'>". $user ."</span>";
        break;
        case 4:
        $span = "<span class='text-danger tool' title='Administrator'>". $user ."</span>";
        break;
        case 3:
        $span = "<span class='text-warning tool' title='Command Staff'>". $user ."</span>";
        break;
        case 2:
        $span = "<span class='text-info tool' title='Platoon Leader'>". $user ."</span>";
        break;
        case 1:
        $span = "<span class='text-primary tool' title='Squad Leader'>". $user ."</span>";
        break;
        default:
        $span = "<span class='text-muted tool' title='Guest'>". $user ."</span>";
        break;
    }

    return $span;
}



function memberColor($user, $level) {

    switch ($level) {
        case 3:
        case 8:
        $span = "<span class='text-danger tool' title='Administrator'><i class='fa fa-shield '></i> ". $user ."</span>";
        break;
        case 2:
        case 1:
        $span = "<span class='text-warning tool' title='Command Staff'><i class='fa fa-shield '></i> ". $user ."</span>";
        break;
        case 4:
        $span = "<span class='text-info tool' title='Platoon Leader'><i class='fa fa-shield '></i> ". $user ."</span>";
        break;
        case 5:
        $span = "<span class='text-primary tool' title='Squad Leader'><i class='fa fa-shield '></i> ". $user ."</span>";
        break;
        default:
        $span = $user;
        break;
    }

    return $span;
}



function get_user_avatar($forum_id, $type = "thumb") {
    return "<img src='http://www.clanaod.net/forums/image.php?type={$type}&u={$forum_id}' class='img-thumbnail avatar' />";
}



function get_games() {

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT full_name, short_name, short_descr FROM games ORDER BY full_name";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}



function get_game_info($gname) {

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT `id`, `short_name`, `full_name`, `subforum`, `description` FROM `games` WHERE short_name = '$gname'";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetch();

        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }

    }
    
    return $query;
}

/*
function getDivisionLeadership($gid) { 
    global $pdo;
    
    if (dbConnect()) {

        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);

        try {
            $sql = "SELECT 1; SELECT 2;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();


        } catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
            
        }
            return $stmt;
    }

}*/


function get_game_threads($gid) {

    global $pdo;
    
    if (dbConnect()) {

        try {

            // grab division threads as well as clan threads (id=0)
            $query = "SELECT thread_url, thread_title FROM games_threads WHERE game_id = {$gid} || game_id = 0";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetch();
            
        }
        catch (PDOException $e) {
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

function hasher($info, $encdata = false) {
    $strength = "10";
    
    //if encrypted data is passed, check it against input ($info) 
    if ($encdata) {
        if (substr($encdata, 0, 60) == crypt($info, "$2a$" . $strength . "$" . substr($encdata, 60))) {
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
        return crypt($info, "$2a$" . $strength . "$" . $salt) . $salt;
        
    }
}

function getUserRoleName($role) {
    switch ($role) {
        case 0:
        $role = "Guest";
        break;
        case 1:
        $role = "Squad Leader";
        break;
        case 2:
        $role = "Platoon Leader";
        break;
        case 3:
        $role = "Division Commander";
        break;
        case 4:
        $role = "Administrator";
        break;
        case 5:
        $role = "Developer";
        break;
    }
    return $role;
}

function updateLoggedInTime($user) {
    global $pdo;
    
    if (dbConnect()) {
        try {

            $user  = strtolower($user);
            $query = $pdo->prepare("UPDATE users SET last_logged = CURRENT_TIMESTAMP() WHERE username = :user");
            $query->execute(array(
                ':user' => $user
                ));
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
    $getPosts   = curl_exec($ch);
    $countPosts = stripos($getPosts, $player);
    if ($countPosts) {
        return true;
    } else {
        return false;
    }
    
}




/**
 * add scorpion's functions
 */


function get_members() {

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.forum_name, member.member_id, bf4_position.desc as bf4_position_desc, bf4_position.id as bf4_position_id, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr FROM `member` 
            LEFT JOIN `rank` ON member.rank_id = rank.id 
            LEFT JOIN `bf4_position` ON member.bf4_position_id = bf4_position.id 
            WHERE status_id=1 ORDER BY member.rank_id DESC";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}

function get_platoon_members($pid) {

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.id, member.forum_name, member.member_id,  bf4_position.desc as bf4_position_desc, bf4_position.id as bf4_position_id, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr FROM `member` 
            LEFT JOIN `rank` on member.rank_id = rank.id 
            LEFT JOIN `bf4_position` ON member.bf4_position_id = bf4_position.id 
            WHERE status_id = 1 AND platoon_id= :pid
            ORDER BY member.rank_id DESC";

            $query = $pdo->prepare($query);
            $query->bindParam(':pid', $pid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}

function get_squads($pid) {

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.forum_name, member.member_id,  bf4_position.desc as bf4_position_desc, bf4_position.id as bf4_position_id, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr FROM `member` 
            LEFT JOIN `rank` on member.rank_id = rank.id 
            LEFT JOIN `bf4_position` ON member.bf4_position_id = bf4_position.id 
            WHERE status_id = 1 AND platoon_id= :pid
            ORDER BY member.rank_id DESC";

            $query = $pdo->prepare($query);
            $query->bindParam(':pid', $pid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}

function get_platoons($gid) {

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT * FROM platoon WHERE game_id = :gid ORDER BY number";
            $query = $pdo->prepare($query);
            $query->bindParam(':gid', $gid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}



function get_platoon_info($platoon_id) {

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT `name`, `number` FROM platoon WHERE id = {$platoon_id}";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetch();
            
        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}


function get_platoon_id_from_number($platoon_number, $division) {

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT id FROM platoon WHERE number = :platoon_num AND game_id = :division";
            $query = $pdo->prepare($query);
            $query->bindParam(':platoon_num', $platoon_number);
            $query->bindParam(':division', $division);
            $query->execute();
            $query = $query->fetch();
            
        }
        catch (PDOException $e) {
            return false;
        }
    }
    return $query[0];
}

function count_total_games($member_id, $date) {

    global $pdo;
    
    if (dbConnect()) {

        $first_day_of_month = date("Y-m-d", strtotime("first day of" . $date));
        $last_day_of_month  = date("Y-m-d", strtotime("last day of" . $date));
        
        try {
            $query = "SELECT count(*) AS games FROM `activity` where `member_id`=" . $member_id . " AND `datetime` between '" . $first_day_of_month . " 00:00:00' and '" . $last_day_of_month . " 23:59:59'";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();
        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query[0]['games'];
}

function count_aod_games($member_id, $date) {

    global $pdo;
    
    if (dbConnect()) {

        $first_day_of_month = date("Y-m-d", strtotime("first day of" . $date));
        $last_day_of_month  = date("Y-m-d", strtotime("last day of" . $date));

        # count total AOD games played for a single member
        try {
            $query = "SELECT count(*) AS games FROM `activity` where `member_id`=" . $member_id . " AND `server` LIKE 'AOD%' AND `datetime` between '" . $first_day_of_month . " 00:00:00' and '" . $last_day_of_month . " 23:59:59'";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();
        }
        catch (PDOException $e) {
            echo "ERROR:" . $e->getMessage();
        }
    }
    return $query[0]['games'];
}

?>