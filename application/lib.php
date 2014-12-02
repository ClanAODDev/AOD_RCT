<?php
include "modules/vbfunctions.php";

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
else {
    error_reporting(0);
    ini_set('display_errors', 0);
}


function isLoggedIn() {
    if (!isset($_SESSION['user_id']) && (!isset($_SESSION['credentials']))) {

    }
}

function define_pages() {

    // build page rules for routing system
    $rules = array(
        'moderator' => "/moderator",
        'home' => "/"
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


function get_games() {

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