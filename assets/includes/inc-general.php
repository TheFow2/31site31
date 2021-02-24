<?php 
/**
* Tumder Project - A platform for the fun
* @copyright (c) 2016 Loois Sndr. All rights reserved.
*
* @author Loois Sndr
* @since 2016
*/
	
	error_reporting(0);
	
	session_cache_limiter('none');
	session_start();

	$config['setting'] = mysqli_fetch_array($Tumdconnect->query("SELECT * FROM `" . SETTING . "` WHERE `id` = '1'"));

	if (!isset($_SESSION['language'])) {
    	$_SESSION['language'] = $config['setting']['language'];
	}

	// Check login
	if (!isset($_COOKIE["tumd_ac_u"]) || !isset($_COOKIE["tumd_ac_p"])) {
		$access = false;
	}

	else {
		$c_password = secureEncode($_COOKIE["tumd_ac_p"]);
		$c_username = secureEncode($_COOKIE["tumd_ac_u"]);

		// Detect credentials type
		if (is_numeric($c_username)) {
        	$cc_query_part = "`id` = " . $c_username;
    	} elseif (preg_match('/@/', $c_username)) {
        	$cc_query_part = "`email` = '{$c_username}'";
    	} elseif (preg_match('/[A-Za-z0-9_]/', $c_username)) {
        	$cc_query_part = "`username` = '{$c_username}'";
    	}

    	$user_check = "SELECT * FROM `" . ACCOUNTS . "` WHERE `password` = '{$c_password}' AND $cc_query_part";
		$user_check_query = $Tumdconnect->query($user_check);

		if($user_check_query->num_rows > 0) {
			/* User data */
			$user['data'] = mysqli_fetch_array($user_check_query);
			/* User Media */
			$user['media'] = getMedia($user['data']['avatar_id']);
			/* User info verify */
			$user_info = "SELECT * FROM `" . USERS . "` WHERE `user_id` = '{$user['data']['id']}'";
			$user_info_query = $Tumdconnect->query($user_info);

			if ($user_info_query->num_rows == 0) {
				$user_info_new = $Tumdconnect->query("INSERT INTO " . USERS . " (user_id, gender) VALUES ({$user['data']['id']},'1')");
			}
			else {
				$user['info'] = mysqli_fetch_array($user_info_query);
			}
		}
		else {
			$access = false;
			setcookie("tumd_ac_u", 0, time() - (60 * 60 * 24 * 1));
			setcookie("tumd_ac_p", 0, time() - (60 * 60 * 24 * 1));
		}
			
	}
	// Fetch preferred language
	if (!empty($_GET['lang'])) {
    	$DIRECT_LANG = secureEncode($_GET['lang']);
	    if (file_exists('assets/language/' . $DIRECT_LANG . '.php')) {
	        $config['language'] = $DIRECT_LANG;
	        $_SESSION['language'] = $DIRECT_LANG;
        
	        if ($access == true) {
	            $Tumdconnect->query("UPDATE " . ACCOUNTS . " SET language='" . $DIRECT_LANG . "' WHERE id=" . $user['data']['id']);
			}
		}
	}

	require_once('assets/language/' . $_SESSION['language'] . '.php');

	// General array
	$Tumd = array();
	$Tumd['config'] = $config;
	$Tumd['theme_url'] = $config['setting']['site_url'] . '/templates/' . $config['setting']['site_theme'];
	$Tumd['access'] = $access;
	if ($Tumd['access'] == true) {
		// User information
		$Tumd['data'] = $user['data'];
		$Tumd['media'] = $user['media'];
		$Tumd['info'] = $user['info'];
	}
