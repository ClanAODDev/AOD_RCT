<?php

define( 'TEMPLATES', dirname( __FILE__ ) . '/layouts/' );
define( 'VIEWS', dirname( __FILE__ ) . '/views/' );

$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), '/' );
$uri = '/' . trim( str_replace( $uri, '', $_SERVER['REQUEST_URI'] ), '/' );
$uri = urldecode( $uri );

/**
 * Defines rules for router system
 * @return array defined rules
 */

function routing()
{
    
    global $divisions;
    
    // combine divisions for rulesets
    if (!is_null($divisions)) {
        $divisions = implode("|", $divisions);
    }
    
    
    // build page rules for routing system
    $rules = array(

        // stats
        'stats/top10/division' => "/stats/top10/division.png",

        // view (user level)
        'view/member' => "/member/(?'id'\d+)",
        'view/division' => "/divisions/(?'division'{$divisions})",
        'view/platoon' => "/divisions/(?'division'{$divisions})/(?'platoon'\d+)",

        // manage
        'manage/inactive' => "/manage/inactive-members",
        'manage/division' => "/manage/division",
        'manage/loas' => "/manage/leaves-of-absence",
        
        // user
        'user/settings' => "/settings",
        'user/help' => "/help",
        'user/register' => "/register",
        'user/logout' => "/logout",

        // admin
        'admin/main' => "/admin",

        // recruiting
        'recruiting/main' => "/recruiting",
        'recruiting/new_member' => "/recruiting/new-member",
        // 'recruiting/existing_member' => "/recruiting/existing-member",
        
        'home' => "/"
        );
    
    return $rules;
}

    // routing definitions use regex
    /*
    'picture'   => "/picture/(?'text'[^/]+)/(?'id'\d+)",    // '/picture/some-text/51'
    'album'     => "/album/(?'album'[\w\-]+)",              // '/album/album-slug'
    'category'  => "/category/(?'category'[\w\-]+)",        // '/category/category-slug'
    'page'      => "/page/(?'page'about|contact)",          // '/page/about', '/page/contact'
    'post'      => "/(?'post'[\w\-]+)",                     // '/post-slug'
    'home'      => "/"
    */
