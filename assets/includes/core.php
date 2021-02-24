<?php
/**
* @package Tumder
*/

set_time_limit(0);
session_start();
date_default_timezone_set( 'UTC' ); // Tumder calculates offsets from UTC
define('CORE_PILOT', true);

if ( !defined( 'ABSPATH' ) ) 
    define('ABSPATH', dirname(dirname(dirname(__FILE__))) . '/');

$time = ceil( time() );
$date = date("j/m/y g:iA", $time);
$access = true;

if ( td_installing() ) {
    require_once( ABSPATH . 'assets/includes/config.php');
    require_once ABSPATH . 'assets/includes/tables.php';

    /**
    * Connecting to MySql server
    */
    $Tumdconnect = @new mysqli($dbTumd['host'], $dbTumd['user'], $dbTumd['pass'], $dbTumd['name']);

    /**
    * Set up connection charset
    */
    $Tumdconnect->set_charset("utf8");

    /**
    * Check connection status
    */
    if ($Tumdconnect->connect_errno) 
        exit($Tumdconnect->connect_errno);

    require_once ABSPATH . 'assets/classes/load.php';
    require_once ABSPATH . 'td-content/addons/load.php';
    require_once ABSPATH . 'assets/includes/engine.php';
}


/* 
* General functions 
*/
function is_logged() 
{

    if (isset($_COOKIE["tumd_ac_u"]) && isset($_COOKIE["tumd_ac_p"])) 
    {
        $userId = (int) secureEncode($_COOKIE["tumd_ac_u"]);
        $userPass = secureEncode($_COOKIE["tumd_ac_p"]);

        global $Tumdconnect;
        $query = $Tumdconnect->query("SELECT id FROM ".ACCOUNTS." WHERE id=$userId AND password='$userPass' AND active=1");
        $fetch = $query->num_rows;
        
        return $fetch;
    }
}

function is_admin() 
{
    global $userData;
    return (is_logged() && $userData['admin']) ? true : false;
}

function is_page( $page ) {
    static $this_page = '';
    switch ($page) {
        case 'play':
            $this_page = 'play' === $_GET['p'] && !empty($_GET['id']) && getGame($_GET['id']);
        break;

        case 'admin':
            $this_page = 'admin' === $_GET['p'];
        break;
        
        case 'home':
        default:
            $this_page = 'home' === $_GET['p'];
        break;
    }
    return (bool) ($this_page) ? true : false;
}


function title_tag() 
{
    return '<title>' . td_title() . '</title>' . "\n";
}


function td_title($name_display=true, $sep1='&middot;', $sep2='&raquo;') 
{
    global $config, $lang;
    if (!isset($_GET['p'])) {
        $_GET['p'] = 'home';
    }

    switch ($_GET['p']) {
        case 'home':
        case 'index':
            $set = $lang['page_home'];
        break;
        case 'categories':
            $set = $lang['page_categories'];
        break;
        case 'community':
            $set = $lang['page_community'];
        break;
        case 'setting':
            if (isset($_GET['section']) && !empty($_GET['section'])) {
                $page_section = $_GET['section'];
                if ($page_section == 'info') {
                    $set = $lang['page_setting'].' '.$sep2.' '.$lang['page_section_info'];
                } elseif ($page_section == 'avatar') {
                    $set = $lang['page_setting'].' '.$sep2.' '.$lang['page_section_avatar'];
                } elseif ($page_section == 'theme') {
                    $set = $lang['page_setting'].' '.$sep2.' '.$lang['page_section_theme'];
                } elseif ($page_section == 'password') {
                    $set = $lang['page_setting'].' '.$sep2.' '.$lang['page_section_password'];
                } else {
                    $set = $lang['page_section_not_found'];
                }
            } else {
                $set = $lang['page_section_not_found'];
            }
            
        break;
        case 'profile':
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                if (getData($_GET['id'], 'id') == true) {
                    $username_titled = getData($_GET['id'], 'name');
                    $set = $username_titled['name'];
                } else {
                    $set = $lang['page_user_not_found'];
                }
            } else {
                $set = $lang['page_user_not_found'];
            }
        break;
        case 'search':
            if (isset($_GET['q']) && !empty($_GET['q'])) {
                $set = $lang['page_search'].' '.$sep2.' '.$_GET['q'];
            } else {
                $set = $lang['page_search'];
            }
        break;
        case 'play':
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                if (getGame($_GET['id'], 'id') == true) {
                    $gamename_titled = getGame($_GET['id'], 'name');
                    $set = $gamename_titled['name'];
                } else {
                    $set = $lang['page_game_not_found'];
                }
            } else {
                $set = $lang['page_game_not_found'];
            }
        break;
        case 'admin':
            if (isset($_GET['section']) && !empty($_GET['section'])) {
                $page_section = $_GET['section'];
                if ($page_section == 'global') {
                    $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_global'];
                } elseif ($page_section == 'addgame') {
                    $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_addgame'];
                } elseif ($page_section == 'setting') {
                    $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_setting'];
                } elseif ($page_section == 'games') {
                    if (isset($_GET['action']) && !empty($_GET['action'])) {
                        $section_page = $_GET['action'];
                        if ($section_page == 'edit') {
                            $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_games'].' &rsaquo; '.$lang['admin_game_edit'];
                        } else {
                            $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_games'].' &rsaquo; '.$lang['page_section_not_found'];
                        }
                    } else {
                        $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_games'];
                    }
                } elseif ($page_section == 'categories') {
                    if (isset($_GET['action']) && !empty($_GET['action'])) {
                        $section_page = $_GET['action'];
                        if ($section_page == 'add') {
                            $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_categories'].' &rsaquo; '.$lang['admin_category_add'];
                        } elseif ($section_page == 'edit') {
                            $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_categories'].' &rsaquo; '.$lang['admin_category_edit'];
                        } else {
                            $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_categories'].' &rsaquo; '.$lang['page_section_not_found'];
                        }
                    } else {
                        $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_categories'];
                    }
                } elseif ($page_section == 'users') {
                    if (isset($_GET['action']) && !empty($_GET['action'])) {
                        $section_page = $_GET['action'];
                        if ($section_page == 'edit') {
                            $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_users'].' &rsaquo; '.$lang['admin_user_edit'];
                        } else {
                            $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_users'].' &rsaquo; '.$lang['page_section_not_found'];
                        }
                    } else {
                        $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_users'];
                    }
                } elseif ($page_section == 'ads') {
                    $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_ads'];
                } elseif ($page_section == 'reports') {
                    $set = $lang['page_admin'].' '.$sep2.' '.$lang['page_admin_section_reports'];
                } else {
                    $set = $lang['page_section_not_found'];
                }
            } else {
                $set = $lang['page_section_not_found'];
            }        
        break;
        case 'login':
            $set = $lang['page_login'];
        break;
        case 'signup':
            $set = $lang['page_signup'];
        break;
        case 'error':
        default:
            $set = $lang['page_error'];
        break;
    }

    if ($name_display) {
        return $config['site_name'].' '.$sep1.' '.$set;
    } else {
        return $set;
    }
}

function slugify($text) {
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}

function urlState($url, $type=false) 
{
    if ($type == true) {
        return urlencode($url);
    }
    elseif($type == false) {
        return urldecode($url);
    }
}


function siteUrl() 
{
    global $config;
    return $config['site_url'];
}


function shortStr($str, $len, $pnt=true) 
{
    if (strlen($str) > $len) {
        if ($pnt == true) {
            $str = mb_substr($str, 0, $len, 'UTF-8')."…";
        } else {
            $str = mb_substr($str, 0, $len, 'UTF-8');
        }
    }
    return $str;
}

function secureEncode($string) 
{
    global $Tumdconnect;
    $string = trim($string);
    if (!$Tumdconnect->connect_errno) {
    $string = mysqli_real_escape_string($Tumdconnect, $string);
    }
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = str_replace('\\r\\n', '<br>',$string);
    $string = str_replace('\\r', '<br>',$string);
    $string = str_replace('\\n\\n', '<br>',$string);
    $string = str_replace('\\n', '<br>',$string);
    $string = str_replace('\\n', '<br>',$string);
    $string = stripslashes($string);
    $string = str_replace('&amp;#', '&#',$string);
    
    return $string;
}


function decodeHTML($string) 
{
    return $string = htmlspecialchars_decode($string);
}


function addslashes__recursive($var)
{
    if (!is_array($var))
    return addslashes($var);
    $new_var = array();
    foreach ($var as $k => $v)$new_var[addslashes($k)]=addslashes__recursive($v);
    return $new_var;
}

$_POST=addslashes__recursive($_POST);
$_GET=addslashes__recursive($_GET);
$_REQUEST=addslashes__recursive($_REQUEST);
$_SERVER=addslashes__recursive($_SERVER);
$_COOKIE=addslashes__recursive($_COOKIE);

function get_game_container_src($get_game_id, $get_game_width, $get_game_height) 
{
    $game_get_url = siteUrl().'/api/v1/sandbox/game/'.$get_game_id;

    $game_get_source = '<iframe src="'.$game_get_url.'" id="game-player" width="'.$get_game_width.'" height="'.$get_game_height.'" frameborder="0" scrolling="no" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

    return $game_get_source;
}


function gameFileUrl($file_url, $import) 
{
    if ($import == 1) {
        $game_url = siteUrl() . '/data-games/' . $file_url;
    }
    else {
        $game_url = $file_url;
    }
    return $game_url;
}


function gameImageUrl($image_url, $import) 
{
    global $config;

    if ($import == 1) {
        $url = siteUrl().'/'.$image_url;
    }
    else {
        if ($image_url == '') {
            $url = $config['theme_path'] . '/image/data-game/default_game-thumb.png';
        }
        else {
            $url = $image_url;
        }
    }
    return $url;
}


function gameData($row_data) 
{
    global $userData;

    $game = array('game_id' => $row_data['game_id'], 'instructions' => nl2br($row_data['instructions']), 'plays' => number_format($row_data['plays'], 0, '', '.'), 'category' => $row_data['category']);
    
    $game['name'] = $row_data['name'];
    $game['description'] = $row_data['description'];
    $game['game_url'] = siteUrl().'/game/' . $row_data['game_id'] . '-' . slugify($game['name']);
    $game['file_url'] = gameFileUrl($row_data['file'], $row_data['import']);
    $game['embed'] = get_game_container_src($game['game_id'], $row_data['w'], $row_data['h']);
    $game['image_url'] = gameImageUrl($row_data['image'], $row_data['import']);

    if (is_logged() && $userData['admin'] == 1) {
        $game['admin_edit'] = '<button data-href="'.siteUrl().'/admin/games/edit/'.$row_data['game_id'].'" class="stt-adm-button top-gm-btn fa fa-pencil"></button>';
    }
    else {
        $game['admin_edit'] = '';
    }

    $game['date_added'] = date('d/m/y', $row_data['date_added']);

    return $game;
}


function getGame($gid=0) 
{
    global $Tumdconnect;
    
    $gid = secureEncode($gid);
    $sql_query_game = $Tumdconnect->query("SELECT * FROM ".GAMES." WHERE game_id='{$gid}'");
    if ($sql_query_game->num_rows == 1) {
        if ($game = $sql_query_game->fetch_array()) {
            return $game;
        }
    }
}


function getAvatar($avatar_id=0, $gender=1, $avatar_size='large') 
{
    $userGet_media = getMedia($avatar_id);

    if ($avatar_id != 0) {
        $avatar_picture = $userGet_media[''.$avatar_size.''];
    } else {
        if ($gender == 2) {
            $avatar_picture = siteUrl()."/data-photo/data-gender/avatar-female.png";
        } else {
            $avatar_picture = siteUrl()."/data-photo/data-gender/avatar-male.png";
        }
    }

    return $avatar_picture;
}


function getInfo($user_id=0) 
{
    global $Tumdconnect;

    if (empty($user_id) or !is_numeric($user_id) or $user_id < 1) {
        return false;
    }

    $user_id = secureEncode($user_id);

    $user_info = "SELECT gender,about FROM ".USERS." WHERE user_id = '{$user_id}'";
    $user_info_query = $Tumdconnect->query($user_info);

    if ($user_info_query->num_rows == 1) {
        if ($user = $user_info_query->fetch_array()) {
            return $user;
        }
    }
}

function getData($user_id=0, $query_select='*') 
{
    global $Tumdconnect;

    if (is_numeric($user_id)) {
        $user_id_type = "id = " . $user_id;
    } elseif (preg_match('/[A-Za-z0-9_]/', $user_id)) {
        $user_id_type = "username = '{$user_id}'";
    } else {
        return false;
    }

    $user_id = secureEncode($user_id);
    $user_data = "SELECT $query_select FROM ".ACCOUNTS." WHERE " . $user_id_type;
    $user_data_query = $Tumdconnect->query($user_data);

    if ($user_data_query->num_rows == 1) {
        if ($user = $user_data_query->fetch_array()) {
            return $user;
        }
    }
}

function getMedia($file_id=0) 
{
    global $Tumdconnect;
    
    if (empty($file_id) or !is_numeric($file_id) or $file_id < 1) {
        return false;
    }
    
    $file_id = secureEncode($file_id);
    $query_one = "SELECT * FROM ".MEDIA." WHERE id=$file_id";
    $sql_query_one = $Tumdconnect->query($query_one);
    
    if ($sql_query_one->num_rows == 1) {
        $sql_fetch_one = mysqli_fetch_assoc($sql_query_one);
        $sql_fetch_one['complete'] = siteUrl().'/'.$sql_fetch_one['url'] . '.' . $sql_fetch_one['extension'];
        $sql_fetch_one['large'] = siteUrl().'/'.$sql_fetch_one['url'] . '_100x75.' . $sql_fetch_one['extension'];
        $sql_fetch_one['medium'] = siteUrl().'/'.$sql_fetch_one['url'] . '_100x100.' . $sql_fetch_one['extension'];
        $sql_fetch_one['thumb'] = siteUrl().'/'.$sql_fetch_one['url'] . '_thumb.' . $sql_fetch_one['extension'];

        return $sql_fetch_one;
    }
}


function uploadMedia($upload) 
{
    if ($GLOBALS['access'] !== true) {
        return false;
    }
    
    global $Tumdconnect;
    set_time_limit(0);
    
    if (!file_exists('data-photo/' . date('Y'))) {
        mkdir('data-photo/' . date('Y'), 0777, true);
    }
    
    if (!file_exists('data-photo/' . date('Y') . '/' . date('m'))) {
        mkdir('data-photo/' . date('Y') . '/' . date('m'), 0777, true);
    }
    
    $photo_dir = 'data-photo/' . date('Y') . '/' . date('m');
    
    if (is_uploaded_file($upload['tmp_name'])) {
        $upload['name'] = secureEncode($upload['name']);
        $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $upload['name']);
        $ext = strtolower(substr($upload['name'], strrpos($upload['name'], '.') + 1, strlen($upload['name']) - strrpos($upload['name'], '.')));
        
        if ($upload['size'] > 1024) {
            
            if (preg_match('/(jpg|jpeg|png)/', $ext)) {
                
                list($width, $height) = getimagesize($upload['tmp_name']);
                
                $query_one = "INSERT INTO " . MEDIA . " (extension, name, type) VALUES ('$ext','$name','photo')";
                $sql_query_one = $Tumdconnect->query($query_one);
                
                if ($sql_query_one) {
                    $sql_id = mysqli_insert_id($Tumdconnect);
                    $original_file_name = $photo_dir . '/' . generateKey() . '_' . $sql_id . '_' . md5($sql_id);
                    $original_file = $original_file_name . '.' . $ext;
                    
                    if (move_uploaded_file($upload['tmp_name'], $original_file)) {
                        $min_size = $width;
                        
                        if ($width > $height) {
                            $min_size = $height;
                        }
                        
                        $min_size = floor($min_size);
                        
                        if ($min_size > 920) {
                            $min_size = 920;
                        }
                        
                        $imageSizes = array(
                            'thumb' => array(
                                'type' => 'crop',
                                'width' => 64,
                                'height' => 64,
                                'name' => $original_file_name . '_thumb'
                            ),
                            '100x100' => array(
                                'type' => 'crop',
                                'width' => $min_size,
                                'height' => $min_size,
                                'name' => $original_file_name . '_100x100'
                            ),
                            '100x75' => array(
                                'type' => 'crop',
                                'width' => $min_size,
                                'height' => floor($min_size * 0.75),
                                'name' => $original_file_name . '_100x75'
                            )
                        );
                        
                        foreach ($imageSizes as $ratio => $data) {
                            $save_file = $data['name'] . '.' . $ext;
                            processMedia($data['type'], $original_file, $save_file, $data['width'], $data['height']);
                        }
                        
                        processMedia('resize', $original_file, $original_file, $min_size, 0);
                        $Tumdconnect->query("UPDATE " . MEDIA . " SET url='$original_file_name' WHERE id=$sql_id");
                        $get = array(
                            'id' => $sql_id,
                            'extension' => $ext,
                            'name' => $name,
                            'url' => $original_file_name
                        );
                        
                        return $get;
                    }
                }
            }
        }
    }
}


function uploadGameMedia($upload) 
{
    if ($GLOBALS['access'] !== true) return false;
    
    global $Tumdconnect;
    set_time_limit(0);
    
    if (!file_exists('data-photo/data-game/images/' . date('Y'))) {
        mkdir('data-photo/data-game/images/' . date('Y'), 0777, true);
    }
    
    if (!file_exists('data-photo/data-game/images/' . date('Y') . '/' . date('m'))) {
        mkdir('data-photo/data-game/images/' . date('Y') . '/' . date('m'), 0777, true);
    }
    
    $photo_dir = 'data-photo/data-game/images/' . date('Y') . '/' . date('m');
    
    if (is_uploaded_file($upload['tmp_name'])) {
        $upload['name'] = secureEncode($upload['name']);
        $name = preg_replace('/([^A-Za-z0-9_\-\.]+)/i', '', $upload['name']);
        $ext = strtolower(substr($upload['name'], strrpos($upload['name'], '.') + 1, strlen($upload['name']) - strrpos($upload['name'], '.')));
        
        if ($upload['size'] > 1024) {
            
            if (preg_match('/(jpg|jpeg|png)/', $ext)) {
                
                list($width, $height) = getimagesize($upload['tmp_name']);
                
                $query_one = "INSERT INTO " . MEDIA . " (extension, name, type) VALUES ('$ext','$name','game')";
                $sql_query_one = $Tumdconnect->query($query_one);
                
                if ($sql_query_one) {
                    $sql_id = mysqli_insert_id($Tumdconnect);
                    $original_file_name = $photo_dir . '/' . generateKey() . '_' . $sql_id . '_' . md5($sql_id);
                    $original_file = $original_file_name . '.' . $ext;
                    
                    if (move_uploaded_file($upload['tmp_name'], $original_file)) {
                        $min_size = $width;
                        
                        if ($width > $height) {
                            $min_size = $height;
                        }
                        
                        $min_size = floor($min_size);
                        
                        if ($min_size > 920) {
                            $min_size = 920;
                        }
                        
                        $imageSizes = array(
                            'thumb' => array(
                                'type' => 'crop',
                                'width' => 64,
                                'height' => 64,
                                'name' => $original_file_name . '_thumb'
                            ),
                            '100x100' => array(
                                'type' => 'crop',
                                'width' => $min_size,
                                'height' => $min_size,
                                'name' => $original_file_name . '_100x100'
                            ),
                            '100x75' => array(
                                'type' => 'crop',
                                'width' => $min_size,
                                'height' => floor($min_size * 0.75),
                                'name' => $original_file_name . '_100x75'
                            )
                        );
                        
                        foreach ($imageSizes as $ratio => $data) {
                            $save_file = $data['name'] . '.' . $ext;
                            processMedia($data['type'], $original_file, $save_file, $data['width'], $data['height']);
                        }
                        
                        processMedia('resize', $original_file, $original_file, $min_size, 0);
                        $Tumdconnect->query("UPDATE " . MEDIA . " SET url='$original_file_name' WHERE id=$sql_id");
                        $get = array(
                            'id' => $sql_id,
                            'extension' => $ext,
                            'name' => $name,
                            'url' => $original_file_name
                        );
                        
                        return $get;
                    }
                }
            }
        }
    }
}


function processMedia($run, $photo_src, $save_src, $width=0, $height=0, $quality=80) 
{
    
    if (!is_numeric($quality) or $quality < 0 or $quality > 100) {
        $quality = 80;
    }

    if (file_exists($photo_src)) {
        
        if (strrpos($photo_src, '.')) {
            $ext = substr($photo_src, strrpos($photo_src,'.') + 1, strlen($photo_src) - strrpos($photo_src, '.'));
            $fxt = (!in_array($ext, array('jpeg', 'png'))) ? "jpeg" : $ext;
        } else {
            $ext = $fxt = 0;
        }
        
        if (preg_match('/(jpg|jpeg|png)/', $ext)) {
            list($photo_width, $photo_height) = getimagesize($photo_src);
            $create_from = "imagecreatefrom" . $fxt;
            $photo_source = $create_from($photo_src);
            
            if ($run == "crop") {
                
                if ($width > 0 && $height > 0) {
                    $crop_width = $photo_width;
                    $crop_height = $photo_height;
                    $k_w = 1;
                    $k_h = 1;
                    $dst_x = 0;
                    $dst_y = 0;
                    $src_x = 0;
                    $src_y = 0;
                    
                    if ($width == 0 or $width > $photo_width) {
                        $width = $photo_width;
                    }
                    
                    if ($height == 0 or $height > $photo_height) {
                        $height = $photo_height;
                    }
                    
                    $crop_width = $width;
                    $crop_height = $height;
                    
                    if ($crop_width > $photo_width) {
                        $dst_x = ($crop_width - $photo_width) / 2;
                    }
                    
                    if ($crop_height > $photo_height) {
                        $dst_y = ($crop_height - $photo_height) / 2;
                    }
                    
                    if ($crop_width < $photo_width || $crop_height < $photo_height) {
                        $k_w = $crop_width / $photo_width;
                        $k_h = $crop_height / $photo_height;
                        
                        if ($crop_height > $photo_height) {
                            $src_x  = ($photo_width - $crop_width) / 2;
                        } elseif ($crop_width > $photo_width) {
                            $src_y  = ($photo_height - $crop_height) / 2;
                        } else {
                            
                            if ($k_h > $k_w) {
                                $src_x = round(($photo_width - ($crop_width / $k_h)) / 2);
                            } else {
                                $src_y = round(($photo_height - ($crop_height / $k_w)) / 2);
                            }
                        }
                    }
                    
                    $crop_image = @imagecreatetruecolor($crop_width, $crop_height);
                    
                    if ($ext == "png") {
                        @imagesavealpha($crop_image, true);
                        @imagefill($crop_image, 0, 0, @imagecolorallocatealpha($crop_image, 0, 0, 0, 127));
                    }
                    
                    @imagecopyresampled($crop_image, $photo_source ,$dst_x, $dst_y, $src_x, $src_y, $crop_width - 2 * $dst_x, $crop_height - 2 * $dst_y, $photo_width - 2 * $src_x, $photo_height - 2 * $src_y);

                    @imageinterlace($crop_image, true);
                    @imagejpeg($crop_image, $save_src, $quality);
                    @imagedestroy($crop_image);
                }
            } elseif ($run == "resize") {
                
                if ($width == 0 && $height == 0) {
                    return false;
                }
                
                if ($width > 0 && $height == 0) {
                    $resize_width = $width;
                    $resize_ratio = $resize_width / $photo_width;
                    $resize_height = floor($photo_height * $resize_ratio);
                } elseif ($width == 0 && $height > 0) {
                    $resize_height = $height;
                    $resize_ratio = $resize_height / $photo_height;
                    $resize_width = floor($photo_width * $resize_ratio);
                } elseif ($width > 0 && $height > 0) {
                    $resize_width = $width;
                    $resize_height = $height;
                }
                
                if ($resize_width > 0 && $resize_height > 0) {
                    $resize_image = @imagecreatetruecolor($resize_width, $resize_height);
                    
                    if ($ext == "png") {
                        @imagesavealpha($resize_image, true);
                        @imagefill($resize_image, 0, 0, @imagecolorallocatealpha($resize_image, 0, 0, 0, 127));
                    }
                    
                    @imagecopyresampled($resize_image, $photo_source, 0, 0, 0, 0, $resize_width, $resize_height, $photo_width, $photo_height);

                    @imageinterlace($resize_image, true);
                    @imagejpeg($resize_image, $save_src, $quality);
                    @imagedestroy($resize_image);
                }
            } elseif ($run == "scale") {
                
                if ($width == 0) {
                    $width = 100;
                }
                
                if ($height == 0) {
                    $height = 100;
                }
                
                $scale_width = $photo_width * ($width / 100);
                $scale_height = $photo_height * ($height / 100);
                $scale_image = @imagecreatetruecolor($scale_width, $scale_height);
                
                if ($ext == "png") {
                    @imagesavealpha($scale_image, true);
                    @imagefill($scale_image, 0, 0, imagecolorallocatealpha($scale_image, 0, 0, 0, 127));
                }
                
                @imagecopyresampled($scale_image, $photo_source, 0, 0, 0, 0, $scale_width, $scale_height, $photo_width, $photo_height);

                @imageinterlace($scale_image, true);
                @imagejpeg($scale_image, $save_src, $quality);
                @imagedestroy($scale_image);
            }
        }
    }
} 


function generateKey($minlength=5, $maxlength=5, $uselower=true, $useupper=true, $usenumbers=true, $usespecial=false) 
{
    $charset = '';
    
    if ($uselower) {
        $charset .= "abcdefghijklmnopqrstuvwxyz";
    }
    
    if ($useupper) {
        $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    }
    
    if ($usenumbers) {
        $charset .= "123456789";
    }
    
    if ($usespecial) {
        $charset .= "~@#$%^*()_+-={}|][";
    }
    
    if ($minlength > $maxlength) {
        $length = mt_rand($maxlength, $minlength);
    } else {
        $length = mt_rand($minlength, $maxlength);
    }
    
    $key = '';
    
    for ($i = 0; $i < $length; $i++) {
        $key .= $charset[(mt_rand(0, strlen($charset) - 1))];
    }
    
    return $key;
}

function dateState($date, $type=1) 
{
    $date_state = '';
    if ($type == 1) {
        $date_state = date("j/m/y", $date);
    }
    elseif ($type == 2) {
         $date_state = date("j/m/Y", $date);
    }
    elseif ($type == 3) {
         $date_state = date("j-m-y", $date);
    }
    elseif ($type == 4) {
         $date_state = date("j-m-Y", $date);
    }
    elseif ($type == 5) {
         $date_state = date("j/m/y g:i A", $date);
    }
    elseif ($type == 6) {
         $date_state = date("j/m/Y g:i A", $date);
    }
    elseif ($type == 7) {
         $date_state = date("j-m-y g:i A", $date);
    }
    elseif ($type == 8) {
         $date_state = date("j-m-Y g:i A", $date);
    }
    return $date_state;
}

function numberFormat($num) 
{
    $suffixes = array('', 'k', 'M', 'G', 'T');
        $suffixIndex = 0;
    
        while(abs($num) >= 1000 && $suffixIndex < sizeof($suffixes))
        {
            $suffixIndex++;
            $num /= 1000;
        }
    
        return (
            $num > 0
                ? floor($num * 1000) / 1000
                : ceil($num * 1000) / 1000
            )
            . $suffixes[$suffixIndex];
}


function listMenu($s1, $s2) 
{
    if ($s1 == $s2) {
        return 'active';
    } else {
        return false;
    }
}


function getADS($type='header') 
{
    global $Tumdconnect, $config;

    $sql_query_ads = $Tumdconnect->query("SELECT * FROM ".ADS."");
    
    if ($config['ads_status'] == true && $sql_query_ads->num_rows == true) {
        $ads_select = $sql_query_ads->fetch_array();
        if ($type == 'header') {
            $ads_select['header'] = '
            <div class="ad-box ad-mg">
                <div>'.$ads_select['header'].'</div>
            </div>';
        } elseif ($type == 'footer') {
            $ads_select['footer'] = '
            <div class="ad-box ad-mg">
                <div>'.$ads_select['footer'].'</div>
            </div>';
        } elseif ($type == 'gametop') {
            $ads_select['gametop'] = '
            <div class="ad-box ad-mg">
                <div>'.$ads_select['gametop'].'</div>
            </div>';
        } elseif ($type == 'gamebottom') {
            $ads_select['gamebottom'] = '
            <div class="ad-box ad-mg">
                <div>'.$ads_select['gamebottom'].'</div>
            </div>';
        } elseif ($type == 'gameinfo') {
            $ads_select['gameinfo'] = '
            <div class="ad-box ad-mg">
                <div>'.$ads_select['gameinfo'].'</div>
            </div>';
        } elseif ($type == 'column_one') {
            $ads_select['column_one'] = '
            <aside>
                <div class="ad-box">
                    <div>'.$ads_select['column_one'].'</div>
                </div>
            </aside>';
        }

        return $ads_select[$type];
    } else {
        return false;
    }
}


function feedID($data, $str=4, $key='x1') 
{
    $pro1 = substr($data, -$str); # Select last $str digits
    $pro2 = $pro1.$key;
    return $pro2;
}


function searchGames($sch_query='') 
{
    global $Tumdconnect;
    $q_result = array();
    
    if (!isset($sch_query) or empty($sch_query)) {
        return $q_result;
    }
    
    $sch_query = secureEncode($sch_query);

    $sql_query = $Tumdconnect->query("SELECT * FROM ".GAMES." WHERE name LIKE '%$sch_query%' AND published='1' ORDER BY name ASC");

    if ($sql_query->num_rows > 0) {
        while ($sql_fetch = $sql_query->fetch_array()) {
            $q_result[] = $sql_fetch;
        }
    }
    
    return $q_result;
}


function lastUser($type='logged', $limit=5) 
{
    global $Tumdconnect;
    $q_lb_user = array();

    if ($type == 'logged') {
        $last_users_logged = $Tumdconnect->query("SELECT avatar_id,name,username,id FROM ".ACCOUNTS." ORDER BY last_logged DESC LIMIT ".$limit);
        while ($last_user = $last_users_logged->fetch_array()) {
            $q_lb_user[] = $last_user;
        }
    } elseif ($type == 'registered') {
        $last_users_registered = $Tumdconnect->query("SELECT avatar_id,name,username,id FROM ".ACCOUNTS." ORDER BY registration_date DESC LIMIT ".$limit);
        while ($last_user = $last_users_registered->fetch_array()) {
            $q_lb_user[] = $last_user;
        }
    }

    return $q_lb_user;
}


function getStats($type='games', $num_format=true) 
{
    global $Tumdconnect;

    if ($type == 'games') {
        $stats_query = $Tumdconnect->query("SELECT game_id FROM ".GAMES." WHERE game_id!=0");
    } elseif ($type == 'users') {
        $stats_query = $Tumdconnect->query("SELECT id FROM ".ACCOUNTS." WHERE id!=0");
    } elseif ($type == 'categories') {
        $stats_query = $Tumdconnect->query("SELECT id FROM ".CATEGORIES." WHERE id!=0");
    } elseif ($type == 'catalog_games') {
        $stats_query = $Tumdconnect->query("SELECT id FROM ".CATALOG." WHERE id!=0");
    }

    if ($num_format) {
        return numberFormat($stats_query->num_rows);
    } else {
        return $stats_query->num_rows;
    }
}


function incCheck($array, $entry, $type='check') 
{
    foreach ($array as $c_array) {
        if ($type == 'check') {
            if (stripos(strtolower($entry), strtolower($c_array)) !== false) {
                return true;
            }
        }
        elseif ($type == 'scan') {
            if ($c_array == $entry) {
                return true;
            }
        }
    }
}


/**
* @since 2.0.1
*/

function untrailingslashit($string) 
{
    return rtrim( $string, '/\\' );
}


function trailingslashit($string) 
{
    return untrailingslashit( $string ) . '/';
}


function addon_path($file) 
{
    return trailingslashit( dirname($file) );
}


function td_installing($install_step=0) 
{
    if ($install_step == 1) {
        return ( file_exists( ABSPATH . 'assets/includes/config.php') ) ? true : false;
    } elseif ($install_step == 2) {
        return ( file_exists( ABSPATH . 'assets/includes/install-blank.php') ) ? true : false;
    } else {
        return ( file_exists( ABSPATH . 'assets/includes/config.php') && file_exists( ABSPATH . 'assets/includes/install-blank.php') ) ? true : false;
    }
}


function get_addons($type) 
{
    if (! isset($_SESSION['addons'][$type])) {
        return false;
    }

    $get = $_SESSION['addons'][$type];

    if ( !is_array($get) ) {
        return false;
    }

    return $get;
}


function addon() 
{
    $params = func_get_args();
    $countParams = func_num_args();
    $type = $params[0];
    $args = "";
    unset($params[0]);

    if (is_array ($type)) {
        $return_type = $type[1];

        if (isset ($type[2])) {
            if ($type[2] == "no_append") {
                $noAppend = true;
            }
        }

        $type = $type[0];
    }

    if ( isset($params[1]) ) {
        $args = $params[1];
    }

    if ($countParams > 2) {
        $args = $params[($countParams - 1)];
    }
        
    $get_addons = get_addons($type);

    if ( is_array($get_addons) ) {
        foreach ($get_addons as $name) {
            $adds = call_user_func_array($name, $params);

            if ( !empty($adds) ) {
                if ( $return_type == "string" ) {
                    if ( is_array($args) ) {
                        $args = "";
                    }

                    if (isset ($noAppend)) {
                        $args = "";
                    }
                        
                    $args .= $adds;
                }
                elseif ( $return_type == "array" ) {
                    $args = array_merge($args, $adds);
                }
                else {
                    return false;
                }
            }
        }
    }

    return $args;
}


function register_addon($type, $func) 
{
    $name = $func;
    $func_invalid = (preg_match('/[A-Za-z0-9_]/i', $name)) ? false : true;

    if ( isset($_SESSION['addons'][$type][$name]) ) {
        return false;
    }
        
    if ( !preg_match('/[A-Za-z0-9_]/i', $type) or $func_invalid) {
        return false;
    }

    $type = strtolower($type);
    $_SESSION['addons'][$type][$name] = $func;
}


function call_addon() 
{
    $args = func_get_args();
    $type = $args[0];
    $func = $args[1];
    unset($args[0], $args[1]);

    return call_user_func_array($func, $args);
}


function getSidebarWidget($type='top-star') 
{
    global $Tumdconnect, $themeData, $lang, $config;
    if ($type == 'top-star') {
        $query_gm_topStar = $Tumdconnect->query("SELECT * FROM ".GAMES." WHERE published='1' ORDER BY rating DESC, date_added DESC LIMIT 5");
        $top_star_list = '';
        if ($query_gm_topStar->num_rows != 0) {
            while ($topStar = $query_gm_topStar->fetch_array()) {
                $game_top = gameData($topStar);
                $themeData['widget_topstar_url'] = $game_top['game_url'];
                $themeData['widget_topstar_image'] = $game_top['image_url'];
                $themeData['widget_topstar_name'] = $game_top['name'];
                $themeData['widget_topstar_rating'] = $topStar['rating'];
                $top_star_list .= \Tumder\UI::view('game/sections/widget-each/top-stars-list');
            }
        } else {
            $top_star_list .= \Tumder\UI::view('game/sections/widget-each/top-stars-notfound');
        }
        $themeData['widget_sidebar_top_star_list'] = $top_star_list;
        return \Tumder\UI::view('game/sections/widget.top-stars');
    }

    elseif ($type == 'top-user') {
        $sql_community_xp_query = $Tumdconnect->query("SELECT id,avatar_id,username,name,xp FROM ".ACCOUNTS." WHERE active='1' AND admin!='1' ORDER BY xp DESC LIMIT 3");
        $top_users_list = '';
        if ($sql_community_xp_query->num_rows != 0) {
            $i=0;
            while ($community_xp = $sql_community_xp_query->fetch_array()) {
                $userinfo_xp = getInfo($community_xp['id']);
                $themeData['avatar'] = getAvatar($community_xp['avatar_id'], $userinfo_xp['gender'], 'thumb');
                $themeData['name'] = $community_xp['name'];
                $themeData['profile_url'] = siteUrl().'/profile/'.$community_xp['username'];
                $i++;
                $top_xp_medal = '';
                if ($i == 1) { $top_xp_medal = $config['theme_path'] . '/image/icon-color/medal_1.png'; } 
                elseif ($i == 2) { $top_xp_medal = $config['theme_path'] . '/image/icon-color/medal_2.png'; } 
                elseif ($i == 3) { $top_xp_medal = $config['theme_path'] . '/image/icon-color/medal_3.png'; }
                $themeData['top_xp_medal'] = ($i <= 3) ? '<img src="'.$top_xp_medal.'" width="18">':'';

                $top_users_list .= \Tumder\UI::view('game/sections/widget-each/top-users-list');
            }
        } else {
            $top_users_list .= \Tumder\UI::view('game/sections/widget-each/top-users-notfound');
        }
        $themeData['widget_sidebar_top_users_list'] = $top_users_list;
        return \Tumder\UI::view('game/sections/widget.top-users');
    }

    elseif ($type == 'random') {
        $query_gm_Random = $Tumdconnect->query("SELECT * FROM ".GAMES." WHERE published='1' ORDER BY rand() LIMIT 5");
        $random_games_list = '';
        if ($query_gm_Random->num_rows != 0) {
            while ($gRandom = $query_gm_Random->fetch_array()) {
                $gmRandom = gameData($gRandom);
                $themeData['random_game_url'] = $gmRandom['game_url'];
                $themeData['random_game_image'] = $gmRandom['image_url'];
                $themeData['random_game_name'] = $gmRandom['name'];
                $themeData['random_game_rating'] = $gRandom['rating'];
                $random_games_list .= \Tumder\UI::view('game/sections/widget-each/random-games-list');
            }
        } else {
            $random_games_list .= \Tumder\UI::view('game/sections/widget-each/random-games-notfound');
        }

        $themeData['widget_sidebar_random_list'] = $random_games_list;
        return \Tumder\UI::view('game/sections/widget.random-games');
    }
}


function getCarouselWidget($type='carousel_random_games', $items=2) 
{
    global $Tumdconnect, $themeData, $userData, $lang, $config;

    switch ( $type ) {
        case 'carousel_random_games':
            $query_gm_Random = $Tumdconnect->query("SELECT * FROM ".GAMES." WHERE published='1' ORDER BY rand() LIMIT 20");
            $carousel_random_games_list = '';
            if ($query_gm_Random->num_rows > 0) {
                while ($gRandom = $query_gm_Random->fetch_array()) {
                    $gmRandom = gameData($gRandom);
                    $themeData['random_game_url'] = $gmRandom['game_url'];
                    $themeData['random_game_image'] = $gmRandom['image_url'];
                    $themeData['random_game_name'] = $gmRandom['name'];
                    $themeData['random_game_rating'] = $gRandom['rating'];
                    $carousel_random_games_list .= \Tumder\UI::view('widgets/carousel-random-games/item');
                }
            } else {
                $carousel_random_games_list .= '';
            }
            $themeData['carousel_random_games_items'] = $items;
            $themeData['carousel_random_list'] = $carousel_random_games_list;
            return \Tumder\UI::view('widgets/carousel-random-games/container');
        break;

    }
}


function db_array_install($site_url, $site_title, $admin_user, $admin_password, $admin_email) 
{
    $db = array();

    $db[0] = "CREATE TABLE IF NOT EXISTS `tumd_account` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` text COLLATE utf8_unicode_ci NOT NULL,
                `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `admin` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
                `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `avatar_id` int(11) NOT NULL,
                `xp` int(11) NOT NULL,
                `language` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
                `profile_theme` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'style-1',
                `ip` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                `registration_date` int(11) NOT NULL,
                `last_logged` int(11) NOT NULL,
                `last_update_info` int(11) NOT NULL,
                `active` enum('1','0') COLLATE utf8_unicode_ci NOT NULL,
                PRIMARY KEY (`id`) 
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    # >>
    $db[1] = "INSERT INTO `tumd_account` (`id`, `name`, `username`, `password`, `admin`, `email`, `xp`, `language`, `profile_theme`, `ip`, `registration_date`, `active`) VALUES (1, 'Administrator', '{$admin_user}', '{$admin_password}', '1', 'admin@admin.com', 0, 'english', 'style-1', '::0', 1478417322, '1')";
    # >>
    $db[2] = "CREATE TABLE IF NOT EXISTS `tumd_users` (
                `user_id` int(11) NOT NULL,
                `gender` enum('1','2') CHARACTER SET utf8 NOT NULL DEFAULT '1',
                `about` text COLLATE utf8_unicode_ci NOT NULL,
                UNIQUE KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    # >>
    $db[3] = "INSERT INTO `tumd_users` (`user_id`, `gender`) VALUES (1, '1')";
    # >>
    $db[4] = "CREATE TABLE IF NOT EXISTS `tumd_ads` (
                `id` int(11) NOT NULL,
                `header` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
                `footer` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
                `column_one` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
                `gametop` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
                `gamebottom` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
                `gameinfo` varchar(400) COLLATE utf8_unicode_ci NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    # >>
    $db[5] = "INSERT INTO `tumd_ads` (`id`, `header`, `footer`, `column_one`, `gametop`, `gamebottom`, `gameinfo`) VALUES (1, 'Ads 1', 'Ads 2', 'Ads 3', 'Ads 4', 'Ads 5', 'Ads 6')";
    # >>
    $db[6] = "CREATE TABLE IF NOT EXISTS `tumd_categories` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `category_pilot` varchar(100) CHARACTER SET utf8 NOT NULL,
                `name` text COLLATE utf8_unicode_ci NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    # >>
    $db[7] = "INSERT INTO `tumd_categories` (`id`, `category_pilot`, `name`) VALUES (1, 'default', 'Default')";
    # >>
    $db[8] = "CREATE TABLE IF NOT EXISTS `tumd_favourites` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `game_id` int(11) NOT NULL,
                `date_added` int(11) NOT NULL,
                `type` enum('favorite','played') COLLATE utf8_unicode_ci NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    # >>
    $db[10] = "CREATE TABLE IF NOT EXISTS `tumd_games` (
                `game_id` int(11) NOT NULL AUTO_INCREMENT,
                `catalog_id` varchar(100) CHARACTER SET utf8 NOT NULL,
                `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `image` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
                `import` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
                `category` int(11) NOT NULL,
                `plays` int(11) NOT NULL,
                `rating` enum('0','0.5','1','1.5','2','2.5','3','3.5','4','4.5','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
                `description` varchar(600) COLLATE utf8_unicode_ci NOT NULL,
                `instructions` varchar(600) COLLATE utf8_unicode_ci NOT NULL,
                `file` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `game_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
                `w` int(10) NOT NULL,
                `h` int(10) NOT NULL,
                `date_added` int(11) NOT NULL,
                `published` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
                `featured` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
                PRIMARY KEY (`game_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    # >>
    $db[11] = "CREATE TABLE IF NOT EXISTS `tumd_media` (
                `id` int(255) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `extension` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
                `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
                `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    # >>
    $db[12] = "CREATE TABLE IF NOT EXISTS `tumd_reports` (
                `report_id` int(11) NOT NULL AUTO_INCREMENT,
                `id_reported` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `report_info` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `report_date` int(11) NOT NULL,
                PRIMARY KEY (`report_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    # >>
    $db[13] = "CREATE TABLE IF NOT EXISTS `tumd_setting` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `site_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `site_url` varchar(50) CHARACTER SET utf8 NOT NULL,
                `site_theme` varchar(100) CHARACTER SET utf8 NOT NULL,
                `site_description` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A platform for the fun',
                `site_keywords` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'games, puzzle, arcade',
                `ads_status` enum('0','1') CHARACTER SET utf8 NOT NULL DEFAULT '0',
                `ad_time` int(11) NOT NULL DEFAULT '10',
                `language` varchar(100) CHARACTER SET utf8 NOT NULL,
                `featured_game_limit` int(11) NOT NULL,
                `mp_game_limit` int(11) NOT NULL,
                `xp_play` int(11) NOT NULL,
                `xp_report` int(11) NOT NULL,
                `xp_register` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    # >>
    $db[14] = "INSERT INTO `tumd_setting` (`id`, `site_name`, `site_url`, `site_theme`, `ad_time`, `language`, `featured_game_limit`, `mp_game_limit`, `xp_play`, `xp_report`, `xp_register`) VALUES (1, '{$site_title}', '{$site_url}', 'tumder', 10, 'english', 8, 12, 50, 100, 10)";
    # >>
    $db[15] = "CREATE TABLE IF NOT EXISTS `tumd_theme` (
                `theme_id` int(11) NOT NULL AUTO_INCREMENT,
                `theme_class` varchar(50) CHARACTER SET utf8 NOT NULL,
                PRIMARY KEY (`theme_id`), 
                UNIQUE KEY `theme_class` (`theme_class`), 
                UNIQUE KEY `theme_class_3` (`theme_class`), 
                KEY `theme_class_2` (`theme_class`)
            ) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    # >>
    $db[16] = "INSERT INTO `tumd_theme` (`theme_id`, `theme_class`) VALUES (1, 'style-1'), (2, 'style-1-image'), (3, 'style-2'), (4, 'style-2-image'), (5, 'style-3'), (6, 'style-3-image'), (7, 'style-4'), (8, 'style-5'), (9, 'style-6'), (10, 'style-7')";
    # >>

    return (array) $db;
}