<?php 
/**
* Tumder Project - A platform for the fun
* @copyright (c) 2016 Loois Sndr. All rights reserved.
*
* @author Loois Sndr
* @since 2016
*/

#error_reporting(0);

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname(dirname(dirname( __FILE__ ))) . '/' );
}

# Theater mode - full size
$theater_pages = array('play');
$themeData['page_theater_mode'] = (incCheck($theater_pages, $_GET['p'], 'scan'))? 'theater-mode':'';
# >>

if (!isset($_GET['p'])) { $_GET['p'] = 'home'; }

switch ($_GET['p']) {
    # Index page source
    case 'home':
    case 'index':
        include( ABSPATH . 'assets/sources/home.php');
    break;

    # Admin page source
    case 'admin':
        include( ABSPATH . 'assets/sources/admin-panel.php');
    break;

    # Game page source
    case 'play':
        include( ABSPATH . 'assets/sources/play.php');
    break;

    # Login page source
    case 'login':
        include( ABSPATH . 'assets/sources/login.php');
    break;

    # Register page source
    case 'signup':
        include( ABSPATH . 'assets/sources/register.php');
    break;

    # Settings page source
    case 'setting':
        include( ABSPATH . 'assets/sources/setting.php');
    break;

    # Profile page source
    case 'profile':
        include( ABSPATH . 'assets/sources/profile.php');
    break;

    # Logout
    case 'logout':
        include( ABSPATH . 'assets/sources/logout.php');
    break;

    # Community page source
    case 'community':
        include( ABSPATH . 'assets/sources/community.php');
    break;

    # Categories page source
    case 'categories':
        include( ABSPATH . 'assets/sources/categories.php');
    break;

    case 'search':
        include( ABSPATH . 'assets/sources/search.php');
    break;
}


if ( empty($themeData['page_content']) ) {
    $themeData['page_content'] = \Tumder\UI::view('welcome/error');
}

