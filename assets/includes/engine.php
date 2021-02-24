<?php 
/**
* Tumder Project - A platform for the fun
* @copyright (c) 2016 Loois Sndr. All rights reserved.
*
* @author Loois Sndr
* @since 2016-2017
*/
	if (!defined('CORE_PILOT')) exit();

	$config = array();
	$confQ = $Tumdconnect->query("SELECT * FROM ".SETTING." WHERE id='1'");
	$config = $confQ->fetch_array(MYSQLI_ASSOC);
	$config['site_path'] = str_replace('index.php', '', $_SERVER['PHP_SELF']);
	$config['theme_path'] = siteUrl() . '/templates/' . $config['site_theme'];
	$config['addons_path'] = siteUrl() . '/td-content/addons/';

	foreach ($config as $cf => $cfg)
	{
    	$themeData['config_' . $cf] = $cfg;
	}

	if (!isset($_SESSION['language'])) 
	{
    	$_SESSION['language'] = $config['language'];
	}

	if( is_logged() ) 
	{
		$c_password = secureEncode($_COOKIE["tumd_ac_p"]);
		$c_username = secureEncode($_COOKIE["tumd_ac_u"]);

		$user_check_query = $Tumdconnect->query("SELECT * FROM ".ACCOUNTS." WHERE id=$c_username AND password = '{$c_password}'");

		if($user_check_query->num_rows > 0) 
		{
			$user['data'] = $user_check_query->fetch_array(MYSQLI_BOTH);
			$user['info'] = getInfo($user['data']['id']);
			$userData = array_merge($user['data'], $user['info']);
			$userData['avatar'] = getAvatar($user['data']['avatar_id']);
			
			foreach ($userData as $key => $value)
        	{
            	if (! is_array($value))
            	{
                	$themeData['user_' . $key] = $value;
            	}
        	}
		}
		else 
		{
			$access = false;
			setcookie("tumd_ac_u", 0, time() - (60 * 60 * 24 * 1));
			setcookie("tumd_ac_p", 0, time() - (60 * 60 * 24 * 1));
		}
			
	}
	
	if (!empty($_GET['lang'])) 
	{
    	$DIRECT_LANG = secureEncode($_GET['lang']);
	    if (file_exists('assets/language/' . $DIRECT_LANG . '.php')) 
	    {
	        $config['language'] = $DIRECT_LANG;
	        $_SESSION['language'] = $DIRECT_LANG;
	        if ( $access ) 
	        {
	            $Tumdconnect->query("UPDATE ".ACCOUNTS." SET language='".$DIRECT_LANG."' WHERE id=".$user['data']['id']);
			}
		}
	}

	require_once ABSPATH . 'assets/language/'.$_SESSION['language'].'.php';