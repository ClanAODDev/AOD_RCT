<?php

include_once("config.php");
include_once("modules/vbfunctions.php");

session_regenerate_id();

/**
 * data collection for user logged in
 */

if (isLoggedIn()) {

    // fetch member data for current user
    $member_info  = get_user_info($_SESSION['username']);
    $userRole     = $member_info['role'];
    $curUser      = $member_info['username'];
    $forumId      = $member_info['member_id'];
    $user_platoon = $member_info['platoon_id'];
    $user_game = $member_info['game_id'];
    
    if (!is_null($member_info['member_id'])) {
        $avatar = get_user_avatar($member_info['member_id']);
    } else {
        $avatar = NULL;
    }

    
    /**
     * generate alerts
     */

    $alerts_list = NULL;
    $alerts      = get_alerts($member_info['userid']);
    if (count($alerts)) {
        foreach ($alerts as $alert) {
            $alerts_list .= "
            <div data-id='{$alert['id']}' data-user='{$member_info['userid']}' class='alert-dismissable alert alert-{$alert['type']} fade in' role='alert'>
                <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                {$alert['content']} </div>";
            }
        }



    /**
     * generate game list for navigation and main page
     */
    
    $game_list = NULL;
    $game_options = "<option>Select a division</option>";
    $games     = get_games();
    
    foreach ($games as $game) {
        $shortname  = strtolower($game['short_name']);
        $longname   = $game['full_name'];
        $game_list .= "<li><a href='/divisions/{$shortname}'>{$longname}</a></li>";
        $game_options .= "<option value='/divisions/{$shortname}'>{$longname}</option>";
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
 * Defines rules for router system
 * See commented examples for a guide
 * @return array defined rules
 */
function define_pages()
{

    /*
    'picture'   => "/picture/(?'text'[^/]+)/(?'id'\d+)",    // '/picture/some-text/51'
    'album'     => "/album/(?'album'[\w\-]+)",              // '/album/album-slug'
    'category'  => "/category/(?'category'[\w\-]+)",        // '/category/category-slug'
    'page'      => "/page/(?'page'about|contact)",          // '/page/about', '/page/contact'
    'post'      => "/(?'post'[\w\-]+)",                     // '/post-slug'
    'home'      => "/"
    */

   // need to refactor this and fetch from database
   // will also need to refactor getGames() to rely
   // on the same function
    $divisions = 
    array(
        'bf4',
        'hl'
        )
    ;
    
    // combine divisions for rulesets
    $divisions = implode("|", $divisions);

    // build page rules for routing system
    $rules = 
    array(
        'member' => "/member/(?'id'\d+)",
        'manage' => "/manage/(?'form'squad|platoon|division)",
        'division' => "/divisions/(?'division'" . $divisions . ")",
        'platoon' => "/divisions/(?'division'" . $divisions . ")/(?'platoon'\d+)",
        'user' => "/user/(?'page'profile|messages|settings)",

        'recruiting' => "/recruiting",
        'new_member' => "/recruiting/new-member",
        'existing_member' => "/recruiting/existing-member",
        'register' => "/register",
        'logout' => "/logout",
        'home' => "/"
        )
    ;
    
    return $rules;
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


function dbConnect()
{
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

            $sth = $pdo->prepare("SELECT users.id as userid, member_id, username, forum_name, rank_id, role, email, idle, platoon_id, last_logged, bf4_position_id, game_id, last_forum_login, last_forum_post, join_date, member.game_id FROM users 
                LEFT JOIN member ON users.username = member.forum_name
                LEFT JOIN games ON games.id = member.game_id
                LEFT JOIN bf4_position ON member.bf4_position_id = bf4_position.id
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
                $sth = $pdo->prepare('SELECT username, role, idle FROM users WHERE last_seen >= CURRENT_TIMESTAMP - INTERVAL 5 MINUTE ORDER BY last_seen DESC');
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
        if ( isset($_COOKIE['active_count']) && ( $_COOKIE['active_count'] > 20 ) ) {
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
            echo 'ERROR: ' . $e->getMessage();
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
        $span = "<span class='text-muted tool-user' title='Guest'>" . $user . "</span>";
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
    return "<img src='http://www.clanaod.net/forums/image.php?type={$type}&u={$forum_id}' class='img-thumbnail avatar-{$type}' />";
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
                echo 'ERROR: ' . $e->getMessage();
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
            echo 'ERROR: ' . $e->getMessage();
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
            echo 'ERROR: ' . $e->getMessage();
        }
    } else {
        return false;
    }
}


// need to return errors this way in other cases
function updateMember($uid, $fname, $blog, $bf4db, $mid)
{
    global $pdo;

    $status = NULL;
    
    if (dbConnect()) {
        try {
            $query = $pdo->prepare("UPDATE member SET forum_name = :fname, battlelog_name = :blog, member_id = :mid, bf4db_id = :bf4db WHERE id = :uid");

            $query->execute(array(
                ':fname' => $fname,
                ':blog' => $blog,
                ':bf4db' => $bf4db,
                ':mid' => $mid,
                ':uid' => $uid
                ));
        }
        catch (PDOException $e) {
            return $status = array('success' => false, 'message' => $e->getMessage());
        }
    }
    return $status = array('success' => true, 'message' => 'Member successfully updated!');
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
            echo 'ERROR: ' . $e->getMessage();
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
 * @param  int $bf4_position_id    position id (in bf4_position)
 * @param  int $squadleader_id     squad leader id
 * @param  int $game_id            game id (from games)
 * @return error                   will return an error if query fails
 */
function createMember($forum_name, $member_id, $battlelog_name, $bf4dbid, $platoon_id, $bf4_position_id, $squadleader_id, $game_id)
{
    global $pdo;
    
    if (dbConnect()) {
        try {

            $query = $pdo->prepare("INSERT INTO member ( forum_name, member_id, battlelog_name, bf4db_id, platoon_id, bf4_position_id, squad_leader_id, game_id, rank_id ) VALUES ( :forum, :member_id, :battlelog, :bf4db, :platoon, :bf4_pos, :sqdldr, :game, :rank )
                ON DUPLICATE KEY UPDATE
                battlelog_name = :battlelog, 
                bf4db_id = :bf4db,
                status_id = 1,
                platoon_id = :platoon, 
                bf4_position_id = :bf4_pos,
                squad_leader_id = :sqdldr,
                game_id = :game,
                rank_id = :rank");

            $query->execute(array(
                ':forum' => $forum_name,
                ':member_id' => $member_id,
                ':battlelog' => $battlelog_name,
                ':bf4db' => $bf4dbid,
                ':platoon' => $platoon_id,
                ':bf4_pos' => $bf4_position_id,
                ':sqdldr' => $squadleader_id,
                ':game' => $game_id,
                ':rank' => 1
                ));
        }

        catch (PDOException $e) {
            return 'ERROR: ' . $e->getMessage();
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
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
    if ($user->developer == 1) {
        return true;
    } else {
        return false;
    }
    
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
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
    if ($pass == hasher($pass, $user->credential)) {
        return $user->id;
    } else {
        return false;
    }
    
}

function curl_last_url(/*resource*/ $ch, /*int*/ &$maxredirect = null) { 
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

        $url = parse_url(curl_last_url($ch));
        $query = $url['query'];
        parse_str($query, $url_array);
        $page = @$url_array['page']-1;

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



function get_posts($type, $limit)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT 
            member.id, member.member_id, posts.title, posts.content, posts.date, posts.forum_id, posts.reply_id, posts.type, users.username, users.role FROM posts 
            LEFT JOIN users ON posts.user = users.id 
            LEFT JOIN member ON posts.forum_id = member.member_id 
            WHERE posts.type = :type
            ORDER BY posts.date DESC
            LIMIT :limiter";
            $query = $pdo->prepare($query);
            $query->bindParam(':type', $type);
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


function build_user_tools($role) {
    switch($role) {

        // squad leader
        case 1: 
        $tools =

        array(
            "Recruit" => array(
                'class' => 'addRct',
                'title' => 'Add new recruit',
                'descr' => 'Start the recruiting process with a division candidate',
                'icon' => 'plus-square',
                'link' => '/recruiting'
                ),

            "Manage" => array(
                'class' => 'manageSquad',
                'title' => 'Manage your squad',
                'descr' => 'Promote, demote, or kick members of your squad',
                'icon' => 'wrench',
                'link' => '/manage/squad'
                ),

            "Inactives" => array(
                'class' => 'revInactives',
                'title' => 'Review inactive members',
                'descr' => 'View inactive members and flag for removal',
                'icon' => 'flag-checkered',
                'link' => '/manage/inactives'
                )
            );
        break;

        // platoon leader
        case 2: 
        $tools =

        array(
            "Recruit" => array(
                'class' => 'addRct',
                'title' => 'Add new recruit',
                'descr' => 'Start the recruiting process with a brand new candidate',
                'icon' => 'plus-square',
                'link' => '/recruiting'
                ),

            "Manage" => array(
                'class' => 'managePlt',
                'title' => 'Manage your platoon',
                'descr' => 'Promote, demote, or kick members of your platoon',
                'icon' => 'wrench',
                'link' => '/manage/platoon'
                ),

            "Inactives" => array(
                'class' => 'revInactives',
                'title' => 'Review inactive members',
                'descr' => 'View inactive members and flag for removal',
                'icon' => 'flag-checkered',
                'link' => '/manage/inactives'
                )
            );
        break;

        // division leader
        case 3: 
        $tools =

        array(
            "Manage" => array(
                'class' => 'manageDiv',
                'title' => 'Manage your division',
                'descr' => 'Perform various forms of maintenance within your division',
                'icon' => 'wrench',
                'link' => '/manage/division'
                ),

            "Inactives" => array(
                'class' => 'revInactives',
                'title' => 'Review inactive reports',
                'descr' => 'View inactivity reports and prepare for removal',
                'icon' => 'flag-checkered',
                'link' => '/manage/inactives'
                )
            );
        break;
    }
    return $tools;
}



function get_member_name($name)
{

    global $pdo;

    if (dbConnect()) {

        try {

            $query = "SELECT member.forum_name, games.full_name as game_name, member.id, rank.abbr FROM member 
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

            $query = "SELECT member.forum_name, member.member_id, bf4_position.desc as bf4_position_desc, bf4_position.id as bf4_position_id, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr FROM `member` 
            LEFT JOIN `rank` ON member.rank_id = rank.id 
            LEFT JOIN `bf4_position` ON member.bf4_position_id = bf4_position.id 
            WHERE status_id=1 ORDER BY member.rank_id DESC";
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

/**
 * fetches squad members if they are a squad leader
 * @param  int $mid member id
 * @return array    returns array if squad members
 */
function get_my_squad($mid)
{

    global $pdo, $member_info;

    if ($member_info['bf4_position_id'] == 5) {

        if (dbConnect()) {

            try {

                $query = "SELECT member.id, member.forum_name, member.member_id, member.last_activity, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr as rank FROM `member` 
                LEFT JOIN `rank` on member.rank_id = rank.id 
                WHERE  member.squad_leader_id = :mid AND status_id = 1
                ORDER BY member.last_activity ASC";

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
    } else {
        return false;
    }
}


function get_player_games($mid)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            $query = "SELECT server, datetime FROM `activity` WHERE member_id = :mid ORDER BY datetime DESC LIMIT 25";
            
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

            $query = "SELECT member.id, member.forum_name, member.member_id,  bf4_position.desc as bf4_position_desc, bf4_position.id as bf4_position_id, member.battlelog_name, member.bf4db_id, member.rank_id, rank.abbr as rank, join_date, last_forum_login, last_forum_post, last_activity, forum_posts FROM `member` 
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
            return "ERROR:" . $e->getMessage();
        }
    }
    return $query;
}

function get_division_ldrs($gid) {

    global $pdo;
    $values = array();

    if (dbConnect()) 
    {
        try {

            $sql = "SELECT member.id, member.forum_name, rank.abbr FROM member LEFT JOIN rank on member.rank_id = rank.id WHERE bf4_position_id = 1 AND member.game_id = {$gid}; 
            SELECT member.id, member.forum_name, rank.abbr FROM member LEFT JOIN rank on member.rank_id = rank.id WHERE bf4_position_id = 2 AND member.game_id = {$gid};";

            $statement = $pdo->query($sql);
            while($statement->columnCount())
            {

                $rowset[] = $statement->fetch();
                $statement->nextRowset();
            }

        } catch (PDOException $e) {
            return "ERROR:" . $e->getMessage();
        }

    }
    return $rowset;
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
function get_squad_leaders($gid, $pid=false)
{

    global $pdo;
    
    if (dbConnect()) {

        try {

            // bf4_position_id 5 = squad leader
            $query = "SELECT member_id, forum_name as name, platoon.name as platoon_name FROM member 
            LEFT JOIN platoon ON platoon.id = member.platoon_id  
            WHERE member.game_id = :gid AND bf4_position_id = 5";

            if ($pid) {
                $query .= " AND platoon_id = :pid ";
            }

            $query .= " ORDER BY platoon.id, forum_name";
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


function get_member($mid) {
    global $pdo;

    if (dbConnect()) {

        try {

            $query = "SELECT member.id, rank.abbr as rank, bf4_position.desc as position, forum_name, member_id, battlelog_name, bf4db_id, rank_id,  platoon_id, bf4_position_id, squad_leader_id, status_id, game_id, join_date, last_forum_login, last_activity, member.game_id, last_forum_post, forum_posts, status.desc FROM member 
            LEFT JOIN users ON users.username = member.forum_name 
            LEFT JOIN games ON games.id = member.game_id
            LEFT JOIN bf4_position ON bf4_position.id = member.bf4_position_id
            LEFT JOIN rank ON rank.id = member.rank_id
            LEFT JOIN status ON status.id = member.status_id WHERE member.id = :mid";
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

function get_statuses() {
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


function get_positions() {
 global $pdo;

 if (dbConnect()) {

    try {

        $query = $pdo->prepare("SELECT `desc`, `id` FROM bf4_position");
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

    if ($etime < 1)
    {
        return '0 seconds';
    }

    $a = array( 365 * 24 * 60 * 60  =>  'year',
       30 * 24 * 60 * 60  =>  'month',
       24 * 60 * 60  =>  'day',
       60 * 60  =>  'hour',
       60  =>  'minute',
       1  =>  'second'
       );
    $a_plural = array( 'year'   => 'years',
     'month'  => 'months',
     'day'    => 'days',
     'hour'   => 'hours',
     'minute' => 'minutes',
     'second' => 'seconds'
     );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}


function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                $level--;
                $ends_line_level = NULL;
                $new_line_level = $level;
                break;

                case '{': case '[':
                $level++;
                case ',':
                $ends_line_level = $level;
                break;

                case ':':
                $post = " ";
                break;

                case " ": case "\t": case "\n": case "\r":
                $char = "";
                $ends_line_level = $new_line_level;
                $new_line_level = NULL;
                break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}


function convertRank($text) {
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
function convertStatus($status) {

    $status = (stristr($status, "LOA")) ? "LOA": $status;

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


function convertDivision($division) {
    switch ($division) {
        case "Battlefield 4":
        $id = 2;
        break;
    }
    return $id;
}


function get_bf4db_id($user) {

    $url = "http://bf4db.com/players?name={$user}";
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $html = curl_exec($ch);
    curl_close($ch);

    $regexp = "/<a href=\"\/players\/(\d*)\" class=\"personaName-medium\">" . $user . "<\/a>/iU";

    if ( preg_match_all($regexp, $html, $matches) ) {

        $len = count($matches[0]);

        for( $i = 0; $i < $len; $i++ ) {
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
 * encryption / decryption
 */


/*function encrypt($plain, $key) { 
        $iv_size        = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC); 
        $iv                = mcrypt_create_iv($iv_size, MCRYPT_RAND); 
        $key                = PBKDF2($key, $iv, 1, 32); 
        $crypted        = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plain, 
MCRYPT_MODE_CBC, $iv); 

        return base64_encode($iv.$crypted); 
} 

function decrypt($crypted, $key) { 
        $crypted        = base64_decode($crypted); 
        $iv                = substr($crypted, 0, 16); 
        $key                = PBKDF2($key, $iv, 1, 32); 
        $crypted        = substr($crypted, 16); 

        return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $crypted, 
MCRYPT_MODE_CBC, $iv); 
} 
*/
/** 
 * PHP PBKDF2 Implementation. 
 * 
 * For more information see: http://www.ietf.org/rfc/rfc2898.txt 
 * 
 * @param string $p                password 
 * @param string $s                salt 
 * @param integer $c                iteration count (use 1000 or higher) 
 * @param integer $dkl        derived key length 
 * @param string $algo        hash algorithm 
 * @return string                        derived key of correct length 
 */ 
/*function PBKDF2($p, $s, $c, $dkl, $algo = 'sha1') { 
        $kb = ceil($dkl / strlen(hash($algo, null, true))); 
        $dk = ''; 
        for($block = 1; $block <= $kb; ++$block) { 
                $ib = $b = hash_hmac($algo, $s.pack('N', $block), $p, true); 
                for($i = 1; $i < $c; ++$i) 
                        $ib ^= ($b = hash_hmac($algo, $b, $p, true)); 
                $dk.= $ib; 
        } 
        return substr($dk, 0, $dkl); 
} 

$crypted = encrypt("Message", "Secret Passphrase"); 
$plain = decrypt($crypted, "Secret Passphrase"); 
*/
?>
