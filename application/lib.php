<?php


/*if (extension_loaded('gd') && function_exists('gd_info')) {
    echo "PHP GD library is installed on your web server";
}
else {
    echo "PHP GD library is NOT installed on your web server";
}
die;
*/

include_once("config.php");
include_once(ROOT . "/application/routes.php");
include_once(ROOT . "/application/modules/vbfunctions.php");
include_once(ROOT . "/application/modules/curl_agents.php");


session_regenerate_id();
date_default_timezone_set('America/New_York');



/**
 * data collection for user logged in
 */

if (isLoggedIn()) {

    // fetch member data for current user
    $member_info   = get_user_info($_SESSION['username']);
    $userRole      = $member_info['role'];
    $curUser       = $member_info['username'];
    $forumId       = $member_info['forum_id'];
    $member_id     = $member_info['member_id'];
    $user_platoon  = $member_info['platoon_id'];
    $user_game     = $member_info['game_id'];
    $myUserId      = $member_info['userid'];
    $user_position = $member_info['position_id'];
    
    
    if (!is_null($member_info['forum_id'])) {
        $avatar = get_user_avatar($member_info['forum_id']);
    } else {
        $avatar = NULL;
    }
    
    /**
     * generate alerts
     */
    
    $alerts_list = NULL;
    $alerts      = get_alerts($myUserId);
    if (count($alerts)) {
        foreach ($alerts as $alert) {
            $alerts_list .= "
            <div data-id='{$alert['id']}' data-user='{$myUserId}' class='alert-dismissable alert alert-{$alert['type']} fade in' role='alert'>
                <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                {$alert['content']} </div>";
            }
        }

    /**
     * generate game list for navigation and main page
     */
    
    $game_list    = NULL;
    $game_options = "<option>Select a division</option>";
    $divisions    = array();
    $games        = get_games();
    
    foreach ($games as $game) {
        $shortname = strtolower($game['short_name']);
        $longname  = $game['full_name'];
        $game_list .= "<li><a href='/divisions/{$shortname}'><img src='/public/images/game_icons/tiny/{$shortname}.png' class='pull-right' /> {$longname}</a></li>";
        $game_options .= "<option value='/divisions/{$shortname}'>{$longname}</option>";
        $divisions[] = $shortname;
    }
}

/**
 * primary functions
 */


/**
 * Checks to see if session data exists
 * @return boolean
 */
function isLoggedIn()
{

    if (isset($_SESSION['loggedIn'])) {
        if ($_SESSION['loggedIn'] === true) {
            return true;
        }
    }
    return false;
}


/**
 * Generates a url based on arguments found in url
 * Not really used in AOD_RCT application
 * @param  varchar $arg url based argument
 * @param  varchar $val value of url based arguement
 * @return array URL encoded array
 */
function generateUrl($arg, $val)
{
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

function forceEmptyMessageIfNull($value)
{
    $value = (!empty($value)) ? $value : "Information missing...";
    return $value;
}

function ordSuffix($n)
{
    $str = "$n";
    $t   = $n > 9 ? substr($str, -2, 1) : 0;
    $u   = substr($str, -1);
    if ($t == 1) {
        return $str . 'th';
    } else {
        switch ($u) {
            case 1:
            return $str . 'st';
            case 2:
            return $str . 'nd';
            case 3:
            return $str . 'rd';
            default:
            return $str . 'th';
        }
    }
}

function singledigitToWord($number)
{
    switch ($number) {
        case 0:
        $word = "zero";
        break;
        case 1:
        $word = "one";
        break;
        case 2:
        $word = "two";
        break;
        case 3:
        $word = "three";
        break;
        case 4:
        $word = "four";
        break;
        case 5:
        $word = "five";
        break;
        case 6:
        $word = "six";
        break;
        case 7:
        $word = "seven";
        break;
        case 8:
        $word = "eight";
        break;
        case 9:
        $word = "nine";
        break;
    }
    return $word;
}



function dbConnect()
{
    global $pdo;
    $conn = '';

    $now = new DateTime();
    $mins = $now->getOffset() / 60;
    $sgn = ($mins < 0 ? -1 : 1);
    $mins = abs($mins);
    $hrs = floor($mins / 60);
    $mins -= $hrs * 60;
    $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);

    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->exec("SET time_zone='$offset';");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    catch (PDOException $e) {
        if (DEBUG_MODE)
            echo "<div class='alert alert-danger'><i class='fa fa-exclamation-circle'></i><strong>Database connection error</strong>: " . $e->getMessage() . "</div>";
    }
    
    return true;
}

function getPercentageColor($pct)
{
    if ($pct >= PERCENTAGE_CUTOFF_GREEN) {
        $percent_class = "success";
    } else if ($pct >= PERCENTAGE_CUTOFF_AMBER) {
        $percent_class = "warning";
    } else {
        $percent_class = "danger";
    }
    return $percent_class;
}


function getGames()
{

    global $pdo;
    
    if (dbConnect()) {

        try {
            $query = "SELECT id, short_name, full_name, subforum, description FROM games ORDER BY full_name";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}


/**
 * get_user_info grabs data specifically pertaining to the user logged in
 * @param  string $name the user's forum name
 * @return array $query an array containing member data
 */
function get_user_info($name)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $sth = $pdo->prepare("SELECT users.id as userid, member.id as member_id, member_id as forum_id, username, forum_name, rank_id, role, email, idle, platoon_id, last_logged, position_id, game_id, last_forum_login, last_forum_post, join_date, member.game_id FROM users 
                LEFT JOIN member ON users.username = member.forum_name
                LEFT JOIN games ON games.id = member.game_id
                LEFT JOIN position ON member.position_id = position.id
                LEFT JOIN rank ON member.rank_id = rank.id
                WHERE users.username = :username");
            
            $sth->bindParam(':username', $name);
            $sth->execute();
            $query = $sth->fetch();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
        
    }
    
    return $query;
}

function onlineUsers()
{
    global $pdo;
    
    if (isLoggedIn()) {
        if (dbConnect()) {
            try {
                // grab active users in past 2 minutes
                $sth = $pdo->prepare('SELECT member.id, username, role, idle FROM users LEFT JOIN member ON users.username = member.forum_name WHERE last_seen >= CURRENT_TIMESTAMP - INTERVAL 10 MINUTE ORDER BY idle, last_seen DESC');
                $sth->execute();
                $users = $sth->fetchAll();
            }
            catch (PDOException $e) {
                return false;
            }
            
        }
        return $users;
    }
}

function forceEndSession()
{
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    
    session_destroy();
}

function updateUserActivityStatus($id, $isActive = false)
{
    global $pdo;
    
    if (dbConnect()) {
        if (isset($_COOKIE['active_count']) && ($_COOKIE['active_count'] > 20)) {
            $idle = 1;
        } else {
            $idle = 0;
        }
        
        /**
         * small issue where a set cookie is not readable until
         * the following page reload. To overcome this, this function
         * is called twice. Once in users_online (when refreshing the 
         * user list) and another in the index, which would occur on
         * page refresh. For page refresh, we know the user is active,
         * so a "true" argument is provided for $isActive. Otherwise
         * we treat it as a user list refresh
         */
        
        try {

            $stmt = $pdo->prepare('UPDATE users SET last_seen = CURRENT_TIMESTAMP(), idle = :idle WHERE id = :id');
            $stmt->bindParam(':idle', $idle, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
        }
        catch (PDOException $e) {
            return false;
        }
    }
}


/**
 * colors for users online list
 * @param  string $user user's name
 * @param  int $level role level
 * @return string combined role string
 */
function userColor($user, $level)
{

    switch ($level) {
        case 99:
        $span = "<span class='text-muted tool-user idling' title='Idle'>" . $user . "</span>";
        break;
        case 4:
        $span = "<span class='text-danger tool-user' title='Clan Admin'>" . $user . "</span>";
        break;
        case 3:
        $span = "<span class='text-warning tool-user' title='Command Staff'>" . $user . "</span>";
        break;
        case 2:
        $span = "<span class='text-info tool-user' title='Platoon Leader'>" . $user . "</span>";
        break;
        case 1:
        $span = "<span class='text-primary tool-user' title='Squad Leader'>" . $user . "</span>";
        break;
        default:
        $span = "<span class='text-muted tool-user' title='User'>" . $user . "</span>";
        break;
    }
    
    return $span;
}


/**
 * colors for member tables
 * @param  string $user user's name
 * @param  int $level role level
 * @return string combined role string
 */
function memberColor($user, $level)
{

    switch ($level) {
        case 3:
        case 8:
        $span = "<span class='text-danger tool' title='Administrator'><i class='fa fa-shield '></i> " . $user . "</span>";
        break;
        case 2:
        case 1:
        $span = "<span class='text-warning tool' title='Command Staff'><i class='fa fa-shield '></i> " . $user . "</span>";
        break;
        case 4:
        $span = "<span class='text-info tool' title='Platoon Leader'><i class='fa fa-shield '></i> " . $user . "</span>";
        break;
        case 5:
        $span = "<span class='text-primary tool' title='Squad Leader'><i class='fa fa-shield '></i> " . $user . "</span>";
        break;
        default:
        $span = $user;
        break;
    }
    
    return $span;
}



function get_user_avatar($forum_id, $type = "thumb")
{
    $forum_img = "http://www.clanaod.net/forums/image.php?type={$type}&u={$forum_id}";
    $unknown   = "/public/images/blank_avatar.jpg";
    list($width, $height) = getimagesize($forum_img);
    
    // blank avatar is 1x1 but just to be safe
    if ($width > 10 && $height > 10) {
        return "<img src='{$forum_img}' class='img-thumbnail avatar-{$type}' />";
    } else {
        return "<img src='{$unknown}' class='img-thumbnail avatar-{$type}' />";
    }
    
}


/**
 * fetch top players in division (measured in games played on AOD servers)
 * @param  string $option daily or monthly option
 * @param  int $max       max players to show
 * @return array          array of players returned 
 */
function get_division_toplist($option, $max)
{
    switch ($option) {
        case "daily":
        $query = "SELECT forum_name, member_id, platoon.number, rank.abbr as rank, ( SELECT count(*) FROM activity WHERE activity.member_id = member.member_id AND activity.server LIKE 'AOD%' AND activity.datetime BETWEEN DATE_SUB(NOW(), INTERVAL 1 day) AND CURRENT_TIMESTAMP ) AS aod_games FROM member LEFT JOIN platoon ON member.platoon_id = platoon.id LEFT JOIN rank ON member.rank_id = rank.id WHERE status_id = 1 ORDER BY aod_games DESC LIMIT {$max}";
        break;
        
        case "monthly":
        $query = "SELECT forum_name, member_id, platoon.number, rank.abbr as rank, ( SELECT count(*) FROM activity WHERE activity.member_id = member.member_id AND activity.server LIKE 'AOD%' AND activity.datetime BETWEEN DATE_SUB(NOW(), INTERVAL 30 day) AND CURRENT_TIMESTAMP ) AS aod_games FROM member LEFT JOIN platoon ON member.platoon_id = platoon.id LEFT JOIN rank ON member.rank_id = rank.id WHERE status_id = 1 ORDER BY aod_games DESC LIMIT {$max}";
        break;
    }
    
    if (!isset($query)) {
        return false;
        
    }
    
    $totalAODquery = "SELECT round((SELECT count(*) FROM activity WHERE activity.server LIKE 'AOD%' AND activity.datetime BETWEEN DATE_SUB(NOW(), INTERVAL 30 day) AND CURRENT_TIMESTAMP) 
        / count(*)*100, 1) FROM activity WHERE activity.datetime BETWEEN DATE_SUB( NOW(), INTERVAL 30 day ) AND CURRENT_TIMESTAMP";

global $pdo;

if (dbConnect()) {
    try {

        $data = array();

        $query = $pdo->prepare($query);
        $query->execute();
        $result = $query->fetchAll();

        if ($option == "monthly") {

            $totalAODquery = $pdo->prepare($totalAODquery);
            $totalAODquery->execute();
            $totalPercentage = $totalAODquery->fetchColumn();

            $data["total_percentage"] = $totalPercentage;
        }
    }
    catch (PDOException $e) {
        return false;
    }

    $data["players"] = $result;

    return $data;
}

}


function get_games()
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT full_name, short_name, short_descr FROM games ORDER BY full_name";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}


/**
 * fetches game information
 * @param  string|int $game game id | shortname
 * @return array       array of game info
 */
function get_game_info($game)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT `id`, `short_name`, `welcome_forum`, `division_structure_thread`, `full_name`, `subforum`, `description` FROM `games` WHERE short_name = :game OR id = :game";
            $query = $pdo->prepare($query);
            $query->bindParam(':game', $game);
            $query->execute();
            $query = $query->fetch();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
        
    }
    
    return $query;
}

function get_game_threads($gid)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            // grab division threads as well as clan threads (id=0)
            $query = "SELECT thread_url, thread_title FROM games_threads WHERE game_id = {$gid} || game_id = 0";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    
    return $query;
}


function userExists($string)
{
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
                return false;
            }
            
            if ($count > 0) {
                return true;
            } else {
                return false;
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

/**
 * converts role id into real string
 * @param  int $role role id (aod.members)
 * @return string    the real string, contextual position
 */
function getUserRoleName($role)
{
    switch ($role) {
        case 0:
        $role = "User";
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
    }
    return $role;
}

/**
 * Updates user's last_logged column for activity tracking purposes
 * @param  int $user username
 * @return boolean   only returns false on failure
 */
function updateLoggedInTime($user)
{
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
            return false;
        }
    } else {
        return false;
    }
}

/**
 * update alert status when viewed (ajax call)
 * @param  int $uid   user id
 * @param  int $alert alert id
 * @return boolean    only returns false on failure
 */
function updateAlert($alert, $uid)
{
    global $pdo;
    
    if (dbConnect() && (isset($uid)) && (isset($alert))) {
        try {
            $query = $pdo->prepare("INSERT INTO `alerts_status` ( alert_id, user_id, read_date) 
                VALUES ( :alert, :user, CURRENT_TIMESTAMP() )");
            $query->execute(array(
                ':alert' => $alert,
                ':user' => $uid
                ));
        }
        catch (PDOException $e) {
            return false;
        }
    } else {
        return false;
    }
}

function updateFlagged($id, $lid, $action)
{
    global $pdo;
    
    if (dbConnect() && (isset($id)) && (isset($action))) {

        if ($action == 1) {
            $query = "INSERT INTO inactive_flagged VALUES (:id, :lid)";
            $args  = array(
                ':id' => $id,
                ':lid' => $lid
                );
        } else {
            // return $result = array('success' => false, 'message' => 'Inactive flag cannot be removed at this time.');
            $query = "DELETE FROM inactive_flagged WHERE member_id = :id";
            $args  = array(
                ':id' => $id
                );
        }
        
        try {
            $query = $pdo->prepare($query);
            $query->execute($args);
            return $result = array(
                'success' => true,
                'message' => 'Success!'
                );
        }
        
        catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                return $result = array(
                    'success' => false,
                    'message' => 'Error: Player already flagged'
                    );
            }
            
            return $result = array(
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
                );
        }
    } else {
        return $result = array(
            'success' => false,
            'message' => 'Error: Something went wrong.'
            );
    }
}




function updateMember($uid, $fname, $blog, $bf4db, $mid, $plt, $sqdldr, $position)
{
    global $pdo;
    
    // slightly unorthodox means of throwing together the query
    // but it should work without too much of a headache
    // 
    
    
    // initial query
    $query = "UPDATE member SET forum_name = :fname, battlelog_name = :blog, member_id = :mid, bf4db_id = :bf4db"; 
    
    // check for defined values and append if set
    if (!is_null($plt)) {
        $query .= ",platoon_id = :platoon";
    }
    if (!is_null($sqdldr)) {
        $query .= ",squad_leader_id = :sqdldr";
    }
    if (!is_null($position)) {
        $query .= ",position_id = :position";
    }
    
    // finish up the query
    $query .= " WHERE id = :uid";
    
    if (dbConnect()) {
        try {
            $query = $pdo->prepare($query);
            
            $values = array(
                ':fname' => $fname,
                ':blog' => $blog,
                ':bf4db' => $bf4db,
                ':mid' => $mid,
                ':uid' => $uid
                );
            
            // only bind parameters if they are set
            if (!is_null($plt)) {
                $values[':platoon'] = $plt;
            }
            if (!is_null($sqdldr)) {
                $values[':sqdldr'] = $sqdldr;
            }
            if (!is_null($position)) {
                $values[':position'] = $position;
            }
            
            $query->execute($values);
        }
        catch (PDOException $e) {
            return $status = array(
                'success' => false,
                'message' => $e->getMessage()
                );
        }
    }
    return $status = array(
        'success' => true,
        'message' => 'Member successfully updated!'
        );
}

/**
 * creates a user during registration (ajax call)
 * @param  string $user       username
 * @param  string $email      email
 * @param  string $credential hashed password
 * @return boolean            returns false only on failure
 */
function createUser($user, $email, $credential)
{
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
            return false;
        }
        
    } else {
        return false;
    }
}

/**
 * Create a new member entry
 * @param  string $forum_name      player's forum anme
 * @param  int $member_id          forum id
 * @param  string $battlelog_name  battlelog name
 * @param  int $bf4dbid            bf4db id
 * @param  int $platoon_id         platoon id
 * @param  int $position_id    position id (in position)
 * @param  int $squadleader_id     squad leader id
 * @param  int $game_id            game id (from games)
 * @return error                   will return an error if query fails
 */
function createMember($forum_name, $member_id, $battlelog_name, $bf4dbid, $platoon_id, $position_id, $squadleader_id, $game_id)
{
    global $pdo;
    
    if (dbConnect()) {
        try {

            // status of 999 is pending. Will reset to 1 when reflected as a new member via arch_sync
            $query = $pdo->prepare("INSERT INTO member ( forum_name, member_id, battlelog_name, bf4db_id, platoon_id, position_id, squad_leader_id, game_id, rank_id, status_id, last_forum_login, last_activity, last_forum_post, recruiter ) VALUES ( :forum, :member_id, :battlelog, :bf4db, :platoon, :bf4_pos, :sqdldr, :game, :rank, 999, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, :sqdldr )         

                ON DUPLICATE KEY UPDATE
                forum_name = :forum,
                position_id = :bf4_pos,
                battlelog_name = :battlelog,
                status_id = 999,
                platoon_id = :platoon,
                squad_leader_id = :sqdldr, 
                bf4db_id = :bf4db,
                recruiter = :sqdldr");
            
            $query->execute(array(
                ':forum' => $forum_name,
                ':member_id' => $member_id,
                ':battlelog' => $battlelog_name,
                ':bf4db' => $bf4dbid,
                ':platoon' => $platoon_id,
                ':bf4_pos' => $position_id,
                ':sqdldr' => $squadleader_id,
                ':game' => $game_id,
                ':rank' => 1,
                ));
        }
        
        catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
        
    }
    return true;
}



/**
 * verifies if the current user is a developer
 * @return boolean if dev: true, if not: false
 */
function isDev()
{
    global $pdo;
    $id = $_SESSION['user_id'];
    
    if (dbConnect()) {
        try {
            $sth = $pdo->prepare('SELECT developer FROM users WHERE id = :id LIMIT 1');
            $sth->bindParam(':id', $id);
            $sth->execute();
            
            $user = $sth->fetch(PDO::FETCH_OBJ);
            
        }
        catch (PDOException $e) {
            return false;
        }
    }
    
    if ($user->developer == 1) {
        return true;
    } else {
        return false;
    }
    
}


/**
 * verifies if the user can modify a particular player
 * @param  int $mid        id of the member being modified
 * @param  int $cur_sqd    the squad leader of the member being modified
 * @param  int $cur_plt    the platoon of the member being modified
 * @return boolean         result of the access check
 */
function canEdit($uid)
{

    global $pdo, $userRole, $forumId, $user_platoon, $user_game, $member_id;
    
    $access = false;
    
    if (dbConnect()) {
        try {
            $sth = $pdo->prepare('SELECT id, platoon_id, squad_leader_id, game_id FROM member WHERE id = :uid LIMIT 1');
            $sth->bindParam(':uid', $uid);
            $sth->execute();
            $member = $sth->fetch(PDO::FETCH_OBJ);
        }
        catch (PDOException $e) {
            return false;
        }
    }
    
    // is the user the assigned squad leader?
    if (($userRole == 1) && ($forumId == $member->squad_leader_id)) {
        $access = true;
        // is the user the platoon leader of the user?
    } else if (($userRole == 2) && ($user_platoon == $member->platoon_id)) {
        $access = true;
        // is the user the division leader of the user?
    } else if (($userRole == 3) && ($user_game == $member->game_id)) {
        $access = true;
        // is the user a dev or clan administrator?        
    } else if (isDev() || $userRole > 3) {
        $access = true;
        // is the user editing someone of a lesser role, or himself?
    } else if ($uid == $member_id) {
        $access = true;
    } else {
        $access = false;
    }
    
    return $access;
    
}


/**
 * validates password for login (ajax call)
 * @param  string $pass user's password
 * @param  string $user username
 * @return boolean      returns true on success; false on failure;
 */
function validatePassword($pass, $user)
{
    global $pdo;
    $user = strtolower($user);
    
    if (dbConnect()) {
        try {
            $sth = $pdo->prepare('SELECT id, credential FROM users WHERE username = :username LIMIT 1');
            $sth->bindParam(':username', $user);
            $sth->execute();
            
            $user = $sth->fetch(PDO::FETCH_OBJ);
            
        }
        catch (PDOException $e) {
            return false;
        }
    }
    
    if ($pass == hasher($pass, $user->credential)) {
        return $user->id;
    } else {
        return false;
    }
    
}


function curl_last_url( /*resource*/ $ch, /*int*/ &$maxredirect = null)
{
    $mr = $maxredirect === null ? 5 : intval($maxredirect);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    if ($mr > 0) {

        $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        
        $rch = curl_copy_handle($ch);
        curl_setopt($rch, CURLOPT_HEADER, true);
        curl_setopt($rch, CURLOPT_NOBODY, true);
        curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
        curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
        do {
            curl_setopt($rch, CURLOPT_URL, $newurl);
            $header = curl_exec($rch);
            if (curl_errno($rch)) {
                $code = 0;
            } else {
                $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                // echo $code;
                if ($code == 301 || $code == 302) {
                    preg_match('/Location:(.*?)\n/', $header, $matches);
                    $newurl = trim(array_pop($matches));
                } else {
                    $code = 0;
                }
            }
        } while ($code && --$mr);
        curl_close($rch);
        if (!$mr) {
            if ($maxredirect === null) {
                trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
            } else {
                $maxredirect = 0;
            }
            return false;
        }
        curl_setopt($ch, CURLOPT_URL, $newurl);
    }
    return $newurl;
}

function checkThread($player, $thread)
{

    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $thread . "&goto=newpost");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $getPosts   = curl_exec($ch);
    $countPosts = stripos($getPosts, $player);
    
    // if not found on the last page, check recursively (max 5 pages) until a match is found
    // don't want to go over 5 pages because of high traffic.
    // may change this to 3...
    if (!$countPosts) {

        $url   = parse_url(curl_last_url($ch));
        $query = $url['query'];
        parse_str($query, $url_array);
        $page = @$url_array['page'] - 1;
        
        curl_setopt($ch, CURLOPT_URL, $thread . "&page={$page}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $getPosts   = curl_exec($ch);
        $countPosts = stripos($getPosts, $player);
        
    }
    
    if ($countPosts) {
        return true;
    } else {
        return false;
    }
    
}


function get_alerts($uid)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "
            SELECT DISTINCT id, content, type FROM alerts
            WHERE start_date < CURRENT_TIMESTAMP 
            AND end_date > CURRENT_TIMESTAMP
            AND NOT EXISTS ( SELECT * FROM alerts_status WHERE alert_id = alerts.id AND user_id = :user )";
            
            $query = $pdo->prepare($query);
            $query->bindParam(':user', $uid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}



function get_posts($type, $limit, $role)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT 
            member.id, member.member_id, posts.title, posts.content, posts.date, posts.forum_id, posts.reply_id, posts.type, users.username, users.role FROM posts 
            LEFT JOIN users ON posts.user = users.id 
            LEFT JOIN member ON posts.forum_id = member.member_id 
            WHERE posts.type = :type AND posts.visibility <= :role
            ORDER BY posts.date DESC
            LIMIT :limiter";
            $query = $pdo->prepare($query);
            $query->bindParam(':type', $type);
            $query->bindParam(':role', $role);
            $query->bindValue(':limiter', (int) trim($limit), PDO::PARAM_INT);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}


function build_user_tools($role)
{
    switch ($role) {

        // squad leader
        case 1:
        $tools = array(
            "Recruit" => array(
                'class' => 'addRct',
                'title' => 'Add new recruit',
                'descr' => 'Start the recruiting process with a division candidate',
                'icon' => 'plus-square text-success',
                'link' => '/recruiting',
                'disabled' => false
                ),

            "Inactives" => array(
                'class' => 'revInactives',
                'title' => 'Review inactive members',
                'descr' => 'View inactive members and flag for removal',
                'icon' => 'flag',
                'link' => '/manage/inactive-members',
                'disabled' => false
                )
            );
        break;
        
        // platoon leader
        case 2:
        $tools = array(
            "Recruit" => array(
                'class' => 'addRct',
                'title' => 'Add new recruit',
                'descr' => 'Start the recruiting process with a division candidate',
                'icon' => 'plus-square text-success',
                'link' => '/recruiting',
                'disabled' => false
                ),

            "DivisionStructureGenerator" => array(
                'class' => 'divGenerator',
                'title' => 'Generate division structure',
                'descr' => 'Generate a new division structure skeleton',
                'icon' => 'cog text-info',
                'link' => '#',
                'disabled' => false
                ),

            "Inactives" => array(
                'class' => 'revInactives',
                'title' => 'Review inactive members',
                'descr' => 'View inactive members and flag for removal',
                'icon' => 'flag',
                'link' => '/manage/inactive-members',
                'disabled' => false
                )
            );
break;

        // division leader
case 3:
$tools = array(
    "Recruit" => array(
        'class' => 'addRct',
        'title' => 'Add new recruit',
        'descr' => 'Start the recruiting process with a division candidate',
        'icon' => 'plus-square text-success',
        'link' => '/recruiting',
        'disabled' => false
        ),

    "DivisionStructureGenerator" => array(
        'class' => 'divGenerator',
        'title' => 'Generate division structure',
        'descr' => 'Generate a new division structure skeleton',
        'icon' => 'cog text-info',
        'link' => '#',
        'disabled' => false
        ),

    "Inactives" => array(
        'class' => 'revInactives',
        'title' => 'Review inactive reports',
        'descr' => 'View inactivity reports and prepare for removal',
        'icon' => 'flag',
        'link' => '/manage/inactive-members',
        'disabled' => false
        )
    );
break;
}
return $tools;
}


function get_forum_name($mid)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.forum_name FROM member WHERE member.member_id = :mid";
            $query = $pdo->prepare($query);
            $query->bindParam(':mid', $mid);
            $query->execute();
            $query = $query->fetchColumn();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}



function get_member_name($name)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.forum_name, games.full_name as game_name, member.id, member.member_id, rank.abbr FROM member 
            LEFT JOIN rank ON member.rank_id = rank.id 
            LEFT JOIN games ON member.game_id = games.id 
            WHERE member.forum_name LIKE CONCAT('%', :name, '%')
            ORDER BY member.rank_id DESC
            LIMIT 25";
            $query = $pdo->prepare($query);
            $query->bindParam(':name', $name);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}

function get_members()
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.forum_name, member.member_id, position.desc as position_desc, position.id as position_id, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr FROM `member` 
            LEFT JOIN `rank` ON member.rank_id = rank.id 
            LEFT JOIN `position` ON member.position_id = position.id 
            WHERE (status_id = 1 OR status_id = 999) ORDER BY member.rank_id DESC";
            $query = $pdo->prepare($query);
            $query->bindParam(':pid', $pid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}

function get_gen_pop($pid, $order_by_rank = false)
{

    global $pdo, $member_info;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.id, member.forum_name, member.member_id, member.last_activity, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr as rank FROM `member` 
            LEFT JOIN `rank` on member.rank_id = rank.id 
            WHERE  member.position_id = 7 AND (status_id = 1 OR status_id = 999) AND platoon_id = :pid
            ";

            if ($order_by_rank) {
                $query .= " ORDER BY member.rank_id DESC, member.join_date ASC ";
            } else {
                $query .= " ORDER BY member.last_activity ASC ";
            }
            
            $query = $pdo->prepare($query);
            $query->bindParam(':pid', $pid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}



function get_leaves_of_absence($gid) {

    global $pdo, $member_info;

    if (dbConnect()) {

        try {

            $query = "SELECT loa.member_id, loa.reason, loa.date_end, member.forum_name, rank.abbr as rank FROM loa
            LEFT JOIN member ON member.member_id = loa.member_id
            LEFT JOIN rank ON rank.id = member.rank_id
            WHERE member.game_id = :gid";

            $query = $pdo->prepare($query);
            $query->bindParam(':gid', $gid);
            $query->execute();
            $query = $query->fetchAll();

        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}

/**
 * fetches squad members based on member id
 * @param  int $mid member id
 * @return array    returns array if squad members
 */
function get_my_squad($mid, $order_by_rank = false)
{

    global $pdo, $member_info;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.id, member.forum_name, member.member_id, member.last_activity, member.battlelog_name, member.bf4db_id, member.forum_posts, member.join_date, member.rank_id, rank.abbr as rank FROM `member` 
            LEFT JOIN `rank` on member.rank_id = rank.id 
            WHERE member.squad_leader_id = :mid AND (member.status_id = 1 OR member.status_id = 999) AND member.position_id = 6";

            if ($order_by_rank) {
                $query .= " ORDER BY member.rank_id DESC, member.join_date ASC ";
            } else {
                $query .= " ORDER BY member.last_activity ASC ";
            }
            
            $query = $pdo->prepare($query);
            $query->bindParam(':mid', $mid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}


function get_part_timers($gid)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member_id, forum_name, battlelog_name FROM part_timers WHERE game_id = :gid";
            $query = $pdo->prepare($query);
            $query->bindParam(':gid', $gid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}



/**
 * fetches inactive players (excess of 30 days inactivity on forums)
 * @param  int $id      member forum id
 * @param  string $type determines type of query: sqd, plt, div
 * @return array        array of inactive members
 */
function get_my_inactives($id, $type, $flagged = NULL)
{

    global $pdo, $member_info;
    $args = NULL;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.id, member.forum_name, member.member_id, member.last_activity, member.battlelog_name, member.bf4db_id, inactive_flagged.flagged_by, member.squad_leader_id, member.forum_posts, member.join_date FROM `member` 
            LEFT JOIN `rank` ON member.rank_id = rank.id  
            LEFT JOIN `inactive_flagged` ON member.member_id = inactive_flagged.member_id          
            WHERE (status_id = 1 OR status_id = 999) AND (last_activity < CURDATE() - INTERVAL 29 DAY AND status_id = 1) AND ";
            
            switch ($type) {
                case "sqd":
                $args = "member.squad_leader_id = :id";
                break;
                
                case "plt":
                $args = "member.platoon_id = :id";
                break;
                
                case "div":
                $args = "member.game_id = :id";
                break;
                
                default:
                $args = "member.game_id = :id";
                break;
            }
            
            if (isDev())
                $args = "member.game_id = :id";
            
            
            if (!is_null($flagged)) {
                $query .= "(member.member_id IN (SELECT member_id FROM inactive_flagged)) AND ";
                $query .= $args . " ORDER BY inactive_flagged.flagged_by";
            } else {
                $query .= "(member.member_id NOT IN (SELECT member_id FROM inactive_flagged)) AND ";
                $query .= $args . " ORDER BY member.last_activity ASC";
            }

            
            // add arguments
            
            $query = $pdo->prepare($query);
            $query->bindParam(':id', $id);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}


function get_player_games($mid)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT (SELECT count(*) FROM `activity` WHERE member_id = :mid AND datetime BETWEEN DATE_SUB( NOW(), INTERVAL 29 day ) AND CURRENT_TIMESTAMP) as lastmonth_games, server, datetime FROM `activity` WHERE member_id = :mid ORDER BY datetime DESC";
            
            $query = $pdo->prepare($query);
            $query->bindParam(':mid', $mid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}



function get_platoon_members($pid)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.id, member.forum_name, member.member_id,  position.desc as position_desc, position.id as position_id, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr as rank, join_date, last_forum_login, last_forum_post, last_activity, forum_posts FROM `member` 
            LEFT JOIN `rank` on member.rank_id = rank.id 
            LEFT JOIN `position` ON member.position_id = position.id 
            WHERE (status_id = 1 OR status_id = 999) AND platoon_id = :pid AND position_id NOT IN (3,2,1)
            ORDER BY member.rank_id DESC";
            
            $query = $pdo->prepare($query);
            $query->bindParam(':pid', $pid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}

function get_division_ldrs($gid)
{

    global $pdo;
    
    if (dbConnect()) {
        try {

            $sql = "SELECT member.id, member.member_id as forum_id, member.forum_name, rank.abbr as rank, member.battlelog_name, position.desc as position_desc FROM member 
            LEFT JOIN rank on member.rank_id = rank.id 
            LEFT JOIN `position` ON member.position_id = position.id 
            WHERE position_id IN (1,2) AND member.game_id = {$gid}";
            
            $statement = $pdo->query($sql);
            $statement->execute();
            $result = $statement->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
        
    }
    return $result;
}


function get_general_sergeants($gid)
{

    global $pdo;
    
    if (dbConnect()) {
        try {

            $sql = "SELECT member.id, member.member_id as forum_id, member.forum_name, rank.abbr as rank, position.desc as position_desc, member.battlelog_name FROM member 
            LEFT JOIN rank on member.rank_id = rank.id 
            LEFT JOIN `position` ON member.position_id = position.id 
            WHERE position_id = 3 AND member.game_id = {$gid}";
            
            $statement = $pdo->query($sql);
            $statement->execute();
            $result = $statement->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
        
    }
    return $result;
}

function get_platoons($gid)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT platoon.id as platoon_id, number, name as platoon_name, leader_id, member.forum_name, rank.abbr FROM platoon 
            LEFT JOIN member on platoon.leader_id = member.member_id
            LEFT JOIN rank on member.rank_id = rank.id
            WHERE platoon.game_id = :gid 
            ORDER BY number";
            $query = $pdo->prepare($query);
            $query->bindParam(':gid', $gid);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}



function get_platoon_info($platoon_id)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT `name`, `number` FROM platoon WHERE id = {$platoon_id}";
            $query = $pdo->prepare($query);
            $query->execute();
            $query = $query->fetch();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}


function get_platoon_id_from_number($platoon_number, $division)
{

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


function get_platoon_number_from_id($platoon, $division)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT number FROM platoon WHERE id = :pid AND game_id = :did";
            $query = $pdo->prepare($query);
            $query->bindParam(':pid', $platoon);
            $query->bindParam(':did', $division);
            $query->execute();
            $query = $query->fetch();
            
        }
        catch (PDOException $e) {
            return false;
        }
    }
    return $query[0];
}

/**
 * fetches all squad leaders in a division
 * @param  int $gid game id (from games)
 * @return array    array of le squad leaders
 */
function get_squad_leaders($gid, $pid = false, $order_by_rank = false)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            // position_id 5 = squad leader
            $query = "SELECT member.id, last_activity, rank.abbr as rank, member_id, forum_name as name, platoon.name as platoon_name, member.battlelog_name FROM member 
            LEFT JOIN platoon ON platoon.id = member.platoon_id
            LEFT JOIN rank ON rank.id = member.rank_id  
            WHERE member.game_id = :gid AND position_id = 5";
            
            if ($pid) {
                $query .= " AND platoon_id = :pid ";
            }

            if ($order_by_rank) {
                $query .= " ORDER BY member.rank_id DESC, member.forum_name ASC ";
            } else {
                $query .= "  ORDER BY platoon.id, forum_name";
            }


            $query = $pdo->prepare($query);
            $query->bindParam(':gid', $gid);
            
            if ($pid) {
                $query->bindParam(':pid', $pid);
            }
            
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}


function get_member($mid)
{
    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT member.id, rank.abbr as rank, position.desc as position, forum_name, member_id, battlelog_name, bf4db_id, rank_id,  platoon_id, position_id, squad_leader_id, status_id, game_id, join_date, last_forum_login, last_activity, member.game_id, last_forum_post, forum_posts, status.desc FROM member 
            LEFT JOIN users ON users.username = member.forum_name 
            LEFT JOIN games ON games.id = member.game_id
            LEFT JOIN position ON position.id = member.position_id
            LEFT JOIN rank ON rank.id = member.rank_id
            LEFT JOIN status ON status.id = member.status_id WHERE member.member_id = :mid";
            $query = $pdo->prepare($query);
            $query->bindParam(':mid', $mid);
            $query->execute();
            $query = $query->fetch();
            
        }
        catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    return $query;
}

function get_statuses()
{
    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = $pdo->prepare("SELECT `desc`, `id` FROM status");
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    return $query;
}


function get_positions($my_position)
{
    global $pdo;
    
    $my_position = (isDev()) ? 0 : $my_position;
    
    if (dbConnect()) {

        try {

            $query = $pdo->prepare("SELECT `desc`, `id` FROM position WHERE id >= :my_position");
            $query->bindParam(':my_position', $my_position);
            $query->execute();
            $query = $query->fetchAll();
            
        }
        catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    return $query;
}




function count_total_games($member_id, $bdate, $edate)
{

    global $pdo;
    
    if (dbConnect()) {

        #$first_day_of_month = date("Y-m-d", strtotime("first day of" . $date)) . ' 00:00:00';
        #$last_day_of_month  = date("Y-m-d", strtotime("last day of" . $date)). ' 23:59:59';

        try {
            $query = "SELECT count(*) AS games FROM activity WHERE member_id = :mid AND datetime between :bdate AND :edate";
            $query = $pdo->prepare($query);
            $query->bindParam(':mid', $member_id);
            $query->bindParam(':bdate', $bdate);
            $query->bindParam(':edate', $edate);
            $query->execute();
            $query = $query->fetchAll();
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query[0]['games'];
}

function count_aod_games($member_id, $bdate, $edate)
{

    global $pdo;
    
    if (dbConnect()) {

        #$first_day_of_month = date("Y-m-d", strtotime("first day of" . $date)) . ' 00:00:00';
        #$last_day_of_month  = date("Y-m-d", strtotime("last day of" . $date)). ' 23:59:59';

        # count total AOD games played for a single member
        try {
            $query = "SELECT count(*) AS games FROM activity WHERE member_id = :mid AND server LIKE 'AOD%' AND datetime between :bdate AND :edate";
            $query = $pdo->prepare($query);
            $query->bindParam(':mid', $member_id);
            $query->bindParam(':bdate', $bdate);
            $query->bindParam(':edate', $edate);
            $query->execute();
            $query = $query->fetchAll();
        }
        catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query[0]['games'];
}


function formatTime($ptime)
{
    $etime = time() - $ptime;
    
    if ($etime < 1) {
        return '0 seconds';
    }
    
    $a        = array(
        365 * 24 * 60 * 60 => 'year',
        30 * 24 * 60 * 60 => 'month',
        24 * 60 * 60 => 'day',
        60 * 60 => 'hour',
        60 => 'minute',
        1 => 'second'
        );
    $a_plural = array(
        'year' => 'years',
        'month' => 'months',
        'day' => 'days',
        'hour' => 'hours',
        'minute' => 'minutes',
        'second' => 'seconds'
        );
    
    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}


function prettyPrint($json)
{
    $result          = '';
    $level           = 0;
    $in_quotes       = false;
    $in_escape       = false;
    $ends_line_level = NULL;
    $json_length     = strlen($json);
    
    for ($i = 0; $i < $json_length; $i++) {
        $char           = $json[$i];
        $new_line_level = NULL;
        $post           = "";
        if ($ends_line_level !== NULL) {
            $new_line_level  = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ($in_escape) {
            $in_escape = false;
        } else if ($char === '"') {
            $in_quotes = !$in_quotes;
        } else if (!$in_quotes) {
            switch ($char) {
                case '}':
                case ']':
                $level--;
                $ends_line_level = NULL;
                $new_line_level  = $level;
                break;
                
                case '{':
                case '[':
                $level++;
                case ',':
                $ends_line_level = $level;
                break;
                
                case ':':
                $post = " ";
                break;
                
                case " ":
                case "\t":
                case "\n":
                case "\r":
                $char            = "";
                $ends_line_level = $new_line_level;
                $new_line_level  = NULL;
                break;
            }
        } else if ($char === '\\') {
            $in_escape = true;
        }
        if ($new_line_level !== NULL) {
            $result .= "\n" . str_repeat("\t", $new_line_level);
        }
        $result .= $char . $post;
    }
    
    return $result;
}


function convertRank($text)
{
    switch ($text) {
        case "Recruit":
        $id = 1;
        break;
        case "Cadet":
        $id = 2;
        break;
        case "Private":
        $id = 3;
        break;
        case "Private First Class":
        $id = 4;
        break;
        case "Specialist":
        $id = 5;
        break;
        case "Trainer":
        $id = 6;
        break;
        case "Lance Corporal":
        $id = 7;
        break;
        case "Corporal":
        $id = 8;
        break;
        case "Sergeant":
        $id = 9;
        break;
        case "Staff Sergeant":
        $id = 10;
        break;
        case "Master Sergeant":
        $id = 11;
        break;
        case "First Sergeant":
        $id = 12;
        break;
        case "Command Sergeant":
        $id = 13;
        break;
        case "Sergeant Major":
        $id = 14;
        break;
    }
    return $id;
}

/**
 * converts textual status to a usable id
 * @param  string $status text based status
 * @return [type]         [description]
 */
function convertStatus($status)
{

    $status = (stristr($status, "LOA")) ? "LOA" : $status;
    
    switch ($status) {

        case "Active":
        $id = 1;
        break;
        case "On Leave":
        case "Missing in Action":
        case "LOA":
        $id = 3;
        break;
        case "Retired":
        $id = 4;
        break;

    }
    return $id;
}

function lastSeenColored($last_seen)
{
    if (strtotime($last_seen) < strtotime('-30 days')) {
        $status = 'danger';
    } else if (strtotime($last_seen) < strtotime('-14 days')) {
        $status = 'warning';
    } else {
        $status = 'default';
    }
    return $status;
}

function lastSeenFlag($last_seen)
{
    if (strtotime($last_seen) < strtotime('-30 days')) {
        $status = "<i class='fa fa-flag text-danger'></i>";
    } else if (strtotime($last_seen) < strtotime('-14 days')) {
        $status = "<i class='fa fa-flag text-warning'></i>";
    } else {
        $status = NULL;
    }
    return $status;
}

function convertDivision($division)
{
    $division = strtolower($division);
    switch ($division) {

        case "battlefield":
        $id = 2;
        break;
        
        default:
        $id = NULL;
        break;
    }
    return $id;
}









/**
 * cURL functions
 */
function get_bf4db_id($user)
{

    $url     = "http://bf4db.com/players?name={$user}";
    $ch      = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    
    $html = curl_exec($ch);
    curl_close($ch);
    
    $regexp = "/<a href=\"\/players\/(\d*)\" class=\"personaName-medium\">" . $user . "<\/a>/iU";
    
    if (preg_match_all($regexp, $html, $matches)) {

        $len = count($matches[0]);
        
        for ($i = 0; $i < $len; $i++) {
            $id = $matches[1][$i];
        }
        
    }
    
    if (isset($id)) {
        return $id;
    } else {
        return false;
    }
    
}


/**
 * fetch battlelog id from BF4 Stats
 * @param  string $battlelogName player's battlelog name
 * @return int                   battlelog personaId
 */
function get_battlelog_id($battlelogName) {
    $url = "http://api.bf4stats.com/api/playerInfo?plat=pc&name={$battlelogName}";
    $headers = get_headers($url); 

    if (stripos($headers[0], '40') !== false || stripos($headers[0], '50') !== false) 
    { 
        $result = array('error' => true, 'message' => 'Player not found, or server down.');

    } else {

        $json = file_get_contents($url);
        $data = json_decode($json);
        $personaId = $data->player->id;

        // returning array
        $result = array('error' => false, 'id' => $personaId);

    }

    return $result;
}




/**
 * ------------------------------------------------------
 * BF4 battlelog stuff
 * ------------------------------------------------------
 */



function download_bl_reports($personaId) {

    $agent = random_user_agent();

    $options = array(
      'http'=>array(
        'method'=>"GET",
        'header'=>"Accept-language: en\r\n" .
        "Cookie: foo=bar\r\n" .
        "User-Agent: {$agent}\r\n"
        )
      );

    $context = stream_context_create($options);

    // http://battlelog.battlefield.com/bf4/warsawbattlereportspopulate/302422941/2048/1/

    $url = "http://battlelog.battlefield.com/bf4/warsawbattlereportspopulate/{$personaId}/2048/1/";
    $json = file_get_contents($url, false, $context);
    $data = json_decode($json);

    $reports = $data->data->gameReports;

    return $reports;
}


/**
 * fetches battlelog reports (max of 10 for now)
 * @param  int $player_id player's personaId
 * @return array            array of game reports
 *
 * need to come up with recursive method to retrieve last 30 days worth
 */
function parse_battlelog_reports($personaId) {

    $reports = download_bl_reports($personaId);

    $monthAgo = strtotime('-30 days'); 
    $arrayReports = array();
    $i = 1;

    foreach ($reports as $report) {
        $unix_date = $report->createdAt;
        $date = DateTime::createFromFormat('U', $unix_date)->format('M d');

        $arrayReports[$i]['reportId'] = $report->gameReportId;
        $arrayReports[$i]['serverName'] = $report->name;
        $arrayReports[$i]['map'] = $report->map;
        $arrayReports[$i]['date'] = $date;

        $i++;
    }



    return $arrayReports;
}




// testing
/*if (isset($_GET['guy_reports'])) {

    $personaId = get_battlelog_id("aguybrush");
    $reports = parse_battlelog_reports($personaId);

    var_dump($reports);

    die;

}
*/





if (isset($_GET['test'])) {

    $result = get_battlelog_id($_GET['test']);

    if ($result['error']) {
        echo $result['message'];
    } else {
        echo $result['id'];
    }

    die;
}









/**
 * Division Structure Generator
 * Generates a BB-code division structure based on current database data
 *
 * Hardcoded values for BF division. Could be used to generate other division
 * structures later, but not pertinent at the moment.
 */
function generate_division_structure() {

    // BF division = 2
    $game = 2;

    // colors
    $division_leaders_color = "#00FF00";
    $platoon_leaders_color = "#00FF00";
    $squad_leaders_color = "#FFA500";

    $div_name_color = "#FF0000";
    $platoon_num_color = "#FF0000";
    $platoon_pos_color = "#40E0D0";

    // misc settings
    $min_num_squad_leaders = 2;


    // game icons
    $bf4_icon = "[img]http://i.imgur.com/WjKYT85.png[/img]";
    $bfh_icon = "[img]http://i.imgur.com/L51wBk8.png[/img]";



    // header
    $out = "[table='width: 1000']";
    $i = 1;
    $out .= "[tr][td]";

    // banner
    $out .= "[center][img]http://i.imgur.com/iWpjGZG.png[/img][/center]<br />";

    /**
     * ---------------------------
     * ------division leaders-----
     * ---------------------------
     */

    $out .= "<br /><br />[center][size=5][color={$div_name_color}][b][i][u]Division Leaders[/u][/i][/b][/color][/size][/center]<br />";
    $out .= "[center][size=4]";
    $divleaders = get_division_ldrs($game);

    foreach ($divleaders as $leader) {
        $aod_url = "[url=" . CLANAOD . $leader['forum_id'] . "]";
        $bl_url = "[url=" . BATTLELOG . $leader['battlelog_name']. "]";
        $out .= "{$aod_url}[color={$division_leaders_color}]{$leader['rank']} {$leader['forum_name']}[/url]{$bl_url}  {$bf4_icon}[/url][/color] - {$leader['position_desc']}<br />";
    }

    $out .= "[/size][/center]<br /><br />";



    /**
     * ---------------------------
     * -----general sergeants-----
     * ---------------------------
     */

    $genSgts = get_general_sergeants($game);
    $out .= "[center][size=3][color={$platoon_pos_color}]General Sergeants[/color]<br />";
    foreach ($genSgts as $sgt) {
        $aod_url = "[url=" . CLANAOD . $sgt['forum_id'] . "]";
        $bl_url = "[url=" . BATTLELOG . $sgt['battlelog_name']. "]";
        $out .= "{$aod_url}{$sgt['rank']} {$sgt['forum_name']}[/url]{$bl_url}  {$bf4_icon}[/url]<br />";
    }
    $out .= "[/size][/center]";

    $out .= "[/td][/tr][/table]";


    /**
     * ---------------------------
     * ---------platoons----------
     * ---------------------------
     */

    $out .= "<br /><br />[table='width: 1100']";

    $platoons = get_platoons($game);
    foreach ($platoons as $platoon) {
        if ($i == 1) {
            $out .= "[tr]";
            $out .= "[td]";
        } else {
            $out .= "[td]";
        }


        $out .= "[size=5][color={$platoon_num_color}]Platoon {$i}[/color][/size] <br />[i][size=3]{$platoon['platoon_name']}[/size][/i]<br /><br />";

        // platoon leader
        $leader = get_member($platoon['leader_id']);
        $aod_url = "[url=" . CLANAOD . $leader['member_id'] . "]";
        $bl_url = "[url=" . BATTLELOG . $leader['battlelog_name']. "]";
        $out .= "{$aod_url}[size=3][color={$platoon_pos_color}]Platoon Leader[/color]<br />[color={$platoon_leaders_color}]{$leader['rank']} {$leader['forum_name']}[/color][/size][/url]{$bl_url}  {$bf4_icon}[/url]<br /><br />";

        // squad leaders
        $squadleaders = get_squad_leaders($game, $platoon['platoon_id'], true);

        $mcount = 0;
        foreach ($squadleaders as $sqdldr) {

            $aod_url = "[url=" . CLANAOD . $sqdldr['member_id'] . "]";
            $bl_url = "[url=" . BATTLELOG . $sqdldr['battlelog_name']. "]";
            $out .= "[size=3][color={$platoon_pos_color}]Squad Leader[/color]<br />{$aod_url}[color={$squad_leaders_color}]{$sqdldr['rank']} {$sqdldr['name']}[/color][/url]{$bl_url}  {$bf4_icon}[/url][/size]<br />";

            // squad members
            $squadmembers = get_my_squad($sqdldr['member_id'], true);
            $out .= "[size=1][list=1]";

            foreach ($squadmembers as $member) {
                $aod_url = "[url=" . CLANAOD . $member['member_id'] . "]";  
                $bl_url = "[url=" . BATTLELOG . $member['battlelog_name']. "]";
                $out .= "[*]{$aod_url}{$member['rank']} {$member['forum_name']}[/url]{$bl_url}  {$bf4_icon}[/url]<br />";
            }

            $out .= "[/list][/size]<br />";
            $mcount++;
        }

        if ($mcount < $min_num_squad_leaders) {
            // minimum of 2 squad leaders per platoon
            $min_num_squad_leaders = ($min_num_squad_leaders < 2) ? 2 : $min_num_squad_leaders;
            for ($mcount = $mcount; $mcount < $min_num_squad_leaders; $mcount++)
                $out .= "[size=3][color={$platoon_pos_color}]Squad Leader[/color]<br />[color={$squad_leaders_color}]TBA[/color][/size]<br />";
        }

        $out .= "<br /><br />";


        /**
         * ---------------------------
         * ----general population-----
         * ---------------------------
         */

        $genpop = get_gen_pop($platoon['platoon_id'], true);
        $out .= "[size=3][color={$platoon_pos_color}]Members[/color][/size]<br />[size=1]";
        foreach ($genpop as $member) {
            $bl_url = "[url=" . BATTLELOG . $member['battlelog_name']. "]";
            $aod_url = "[url=" . CLANAOD . $member['member_id'] . "]";
            $out .= "{$aod_url}{$member['rank']} {$member['forum_name']}[/url]{$bl_url}  {$bf4_icon}[/url]<br />";

        }

        $out .= "[/size]";
        $out .= "[/td]";

        $i++;

    }
    // end last platoon
    $out .= "[/tr][/table]<br /><br />";


    /**
     * ---------------------------
     * --------part timers--------
     * ---------------------------
     */
    $i = 1;

    $out .= "<br />[table='width: 1000']";
    $out .= "[tr][td]<br />[center][size=3][color={$platoon_pos_color}][b]Part Time Members[/b][/color][/size][/center][/td][/tr]";
    $out .= "[/table]<br /><br />";


    $out .= "[table='width: 1000']";
    $out .= "[tr][td][center]";


    $partTimers = get_part_timers($game);

    foreach ($partTimers as $member) {

        if ($i % 10 == 0) {
            $out .= "[/td][td]";
        }
        $bl_url = "[url=" . BATTLELOG . $member['battlelog_name']. "]";
        $aod_url = "[url=" . CLANAOD . $member['member_id'] . "]";
        $out .= "{$aod_url}AOD_{$member['forum_name']}[/url]{$bl_url}  {$bf4_icon}[/url]<br />";

        $i++;

    }

    $out .= "[/center][/td]";
    $out .= "[/tr][/table]<br /><br />";


    /**
     * ---------------------------
     * -----------LOAS------------
     * ---------------------------
     */
    
    $i = 1;

    $out .= "<br />[table='width: 1000']";
    $out .= "[tr][td]<br />[center][size=3][color={$platoon_pos_color}][b]Leaves of Absence[/b][/color][/size][/center][/td][/tr]";
    $out .= "[/table]<br /><br />";


    $out .= "[table='width: 1000']";
    $out .= "[tr][td][center]";


    $loas = get_leaves_of_absence($game);
    foreach ($loas as $member) {
        $date_end = date("M d, Y", strtotime($member['date_end']));
        $aod_url = "[url=" . CLANAOD . $member['member_id'] . "]";
        $out .= "{$aod_url}{$member['rank']} {$member['forum_name']}[/url]<br />[b]Ends[/b] {$date_end}<br />{$member['reason']}<br /><br />";

        $i++;

    }

    $out .= "[/center][/td]";
    $out .= "[/tr][/table]";

    return $out;
}