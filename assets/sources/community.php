<?php
	$i=0;
	$sql_community_xp_query = $Tumdconnect->query("SELECT * FROM ".ACCOUNTS." WHERE active='1' AND admin!='1' ORDER BY xp DESC LIMIT 20");
	$txp_r = '';
	if ($sql_community_xp_query->num_rows > 0) {
		while ($community_xp = $sql_community_xp_query->fetch_array()) {
			$userinfo_xp = getInfo($community_xp['id']);
			$themeData['avatar'] = getAvatar($community_xp['avatar_id'], $userinfo_xp['gender'], 'medium');
			$themeData['username'] = $community_xp['username'];
			$themeData['user_xp'] = $community_xp['xp'];
			$themeData['user_str_xp'] = shortStr($community_xp['xp'], 8);
				
			$i++;
			$themeData['top_xp_class'] = ($i <= 3) ? '_community-top-xp':'_community-xp';
			switch ($i) {
				case '1':
					$user_top_position = $config['theme_path'].'/image/icon-color/medal_1.png'; 
				break;
				case '2':
					$user_top_position = $config['theme_path'].'/image/icon-color/medal_2.png'; 
				break;
				case '3':
					$user_top_position = $config['theme_path'].'/image/icon-color/medal_3.png'; 
				break;
				default:
					$user_top_position = $i;
				break;
			}
			$themeData['user_top_position'] = ( $i <= 3 ) ? '<img src="'.$user_top_position.'" width="18">' : $user_top_position;

			$themeData['admin_user_control'] = ( is_logged() && $userData['admin'] ) ? \Tumder\UI::view('community/admin-user-control') : '';
			$txp_r .= \Tumder\UI::view('community/top-xp-list');
		}
	} else {
		$txp_r .= \Tumder\UI::view('community/top-xp-notfound');
	}
	$themeData['top_xp_list'] = $txp_r;

	$themeData['page_content'] = \Tumder\UI::view('community/content');