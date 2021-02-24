<?php
	if ( is_logged() && $userData['admin'] ) {
		$navigation_menu_data = ( isset($_GET['section']) ) ? $_GET['section'] : 'global';
		$themeData['nav_menu_global'] = listMenu($navigation_menu_data, 'global');
		$themeData['nav_menu_addgame'] = listMenu($navigation_menu_data, 'addgame');
		$themeData['nav_menu_setting'] = listMenu($navigation_menu_data, 'setting');
		$themeData['nav_menu_games'] = listMenu($navigation_menu_data, 'games');
		$themeData['nav_menu_categories'] = listMenu($navigation_menu_data, 'categories');
		$themeData['nav_menu_users'] = listMenu($navigation_menu_data, 'users');
		$themeData['nav_menu_ads'] = listMenu($navigation_menu_data, 'ads');
		$themeData['nav_menu_reports'] = listMenu($navigation_menu_data, 'reports');
		//{{
		$q_reports = $Tumdconnect->query("SELECT report_id FROM ".REPORTS." WHERE report_id!=0");
   		$reports = $q_reports->num_rows;
		$themeData['admin_menu_reports'] = ( $reports ) ? '<span class="notif-menu"><i class="fa fa-bell"></i></span>' : '';
		//}}

		$themeData['admin_navigation_menu'] = \Tumder\UI::view('admin/nav-menu');

		if (!isset($_GET['section']) || $_GET['section'] == "global") {
			$themeData['admin_stats_games'] = getStats('games');
			$themeData['admin_stats_users'] = getStats('users');
			$themeData['admin_stats_categories'] = getStats('categories');

			$getLastUser_registered = lastUser('registered', 4);
			$lsturgtd_r = '';
			foreach ($getLastUser_registered as $last_user) {
				$getInfo = getInfo($last_user['id']);
				$themeData['stats_user_avatar'] = getAvatar($last_user['avatar_id'], $getInfo['gender'], 'thumb');
				$themeData['stats_user_name'] = $last_user['name'];
				$themeData['stats_user_username'] = $last_user['username'];

				$lsturgtd_r .= \Tumder\UI::view('admin/stats-list-user');
			}
			$themeData['stats_last_user_registered_list'] = $lsturgtd_r;
			# >>

			$getLastUser_registered = lastUser('logged', 4);
			$lstulggd_r = '';
			foreach ($getLastUser_registered as $last_user) {
				$getInfo = getInfo($last_user['id']);
				$themeData['stats_user_avatar'] = getAvatar($last_user['avatar_id'], $getInfo['gender'], 'thumb');
				$themeData['stats_user_name'] = $last_user['name'];
				$themeData['stats_user_username'] = $last_user['username'];

				$lstulggd_r .= \Tumder\UI::view('admin/stats-list-user');
			}
			$themeData['stats_last_user_logged_list'] = $lstulggd_r;
			# >>

			$themeData['page_admin_content'] = \Tumder\UI::view('admin/stats');
		} 

		elseif (isset($_GET['section']) && $_GET['section'] == "addgame") {
			$addgame_category = $Tumdconnect->query("SELECT * FROM ".CATEGORIES." WHERE id!=0");
			$ctop_r = '';
			while ( $select_category = $addgame_category->fetch_array() ) {
				$ctop_r .='<option value="'.$select_category['id'].'">'.$select_category['name'].'</option>';
			}
			$themeData['get_categories'] = $ctop_r;

			$themeData['page_admin_content'] = \Tumder\UI::view('admin/add-game');
		}

		elseif (isset($_GET['section']) && $_GET['section'] == "setting") {
			$THEME_dir = opendir('templates/');
			$THEME_dr_array = array();
			while (false !== ($file = readdir($THEME_dir))) {
				$THEME_dr_array[] = $file;
			}
			closedir($THEME_dir);
			$thm_r = '';
			foreach($THEME_dr_array as $file) {
				if ($file != "." && $file != ".." && $file != "Thumbs.db" && $file != ".DS_Store" && $file != "images") {
					if ($config['site_theme'] == $file) {
						$thm_r .= '<option value="'.$file.'" selected>'.$file.'</option>';
					} else {
						$thm_r .= '<option value="'.$file.'">'.$file.'</option>';
					}
				}
			}
			$themeData['setting_get_themes'] = $thm_r;
			# >>

			$LANG_dir = opendir('assets/language/');
			$LANG_dr_array = array();
			while (false !== ($file = readdir($LANG_dir))) {
				$LANG_dr_array[] = $file;
			}
			closedir($LANG_dir);
			$lng_r = '';
			foreach($LANG_dr_array as $file) {
				if ($file != "." && $file != ".." && $file != "Thumbs.db" && $file != ".DS_Store" && $file != "images") {
					$val_file = str_replace('.php', '', $file);
					if ($config['language'] == $val_file) {
						$lng_r .= '<option value="'.$val_file.'" selected>'.$val_file.'</option>';
					} else {
						$lng_r .= '<option value="'.$val_file.'">'.$val_file.'</option>';
					}
				}
			}
			$themeData['setting_get_languages'] = $lng_r;
			# >>

			$themeData['setting_ads_checked'] = ( $config['ads_status'] ) ? 'checked' : '';

			$themeData['page_admin_content'] = \Tumder\UI::view('admin/setting');
		}

		elseif (isset($_GET['section']) && $_GET['section'] == "games") {
			if (!isset($_GET['action']) || $_GET['action'] == "view") {
				$pageno = isset($_GET['page']) ? (int) $_GET['page'] : 1;
		        $no_of_records_per_page = 25;
		        $offset = ($pageno-1) * $no_of_records_per_page;

		        $result = $Tumdconnect->query("SELECT COUNT(*) FROM " . GAMES);
		        $total_pages = ceil($result->fetch_array()[0] / $no_of_records_per_page);

				$sql_global_games = $Tumdconnect->query("SELECT * FROM ".GAMES." WHERE game_id!=0 ORDER BY date_added DESC LIMIT $offset, $no_of_records_per_page");

				if ( $sql_global_games->num_rows > 0 ) {
					$vgmsgbl_r = '';
					while ( $global_games = $sql_global_games->fetch_array() ) {
						$themeData['view_game_id'] = $global_games['game_id'];
						$themeData['view_game_name'] = $global_games['name'];
						$themeData['view_game_image'] = $global_games['image'];
						$themeData['view_game_featured'] = $global_games['featured'];
						$themeData['view_game_published'] = $global_games['published'];
						$themeData['view_published_class_status'] = ($global_games['published'] == 1) ? 'pub-active' : '';
						$themeData['view_featured_class_status'] = ($global_games['featured'] == 1) ? 'feat-active' : '';

						$vgmsgbl_r .= \Tumder\UI::view('admin/sections/view-games-list');
					}
					$themeData['view_games_list'] = $vgmsgbl_r;

					$themeData['view_games_pagination'] = '
					<ul class="pagination">
						<li><a href="'. siteUrl() .'/admin/games/1">First</a></li>
						<li class="' . ( ($pageno <= 1) ? 'disabled' : '' ) .'">
							<a href="' . siteUrl() .'/admin/games'. ( ($pageno <= 1) ? '#' : '/'.($pageno - 1) ) .'">Prev</a>
						</li>
						<li class="' . ( ($pageno >= $total_pages) ? 'disabled' : '' ) .'">
							<a href="' . siteUrl() .'/admin/games'. ( ($pageno >= $total_pages) ? '#' : '/'.($pageno + 1) ) .'">Next</a>
						</li>
						<li><a href="'. siteUrl() .'/admin/games/'. $total_pages .'">Last</a></li>
					</ul>
					';



					$themeData['games_container'] = \Tumder\UI::view('admin/sections/view-games-container');
				} else {
					$themeData['games_container'] = \Tumder\UI::view('admin/sections/view-games-notfound');
				}

				$themeData['games_section_content'] = \Tumder\UI::view('admin/sections/view-games-section');
			}
			elseif (isset($_GET['action']) && $_GET['action'] == "edit" && !empty($_GET['gid'])) {
				$get_game_id = secureEncode($_GET['gid']);
				$get_game = getGame($get_game_id);

				if ( $get_game ) {
					$themeData['edit_game_id'] = $get_game['game_id'];
					$themeData['edit_game_name'] = $get_game['name'];
					$themeData['edit_game_image'] = $get_game['image'];
					$themeData['edit_game_description'] = $get_game['description'];
					$themeData['edit_game_instructions'] = $get_game['instructions'];
					$themeData['edit_game_file'] = $get_game['file'];
					$themeData['edit_game_width'] = $get_game['w'];
					$themeData['edit_game_height'] = $get_game['h'];
					$themeData['edit_game_type_swf_status'] = ($get_game['game_type'] == 'swf') ? 'selected' : '';
					$themeData['edit_game_type_other_status'] = ($get_game['game_type'] !== 'swf') ? 'selected' : '';

					$themeData['edit_game_rating_0'] = ($get_game['rating']==0) ? 'selected' : '';
					$themeData['edit_game_rating_0_5'] = ($get_game['rating']==0.5) ? 'selected' : '';
					$themeData['edit_game_rating_1'] = ($get_game['rating']==1) ? 'selected' : '';
					$themeData['edit_game_rating_1_5'] = ($get_game['rating']==1.5) ? 'selected' : '';
					$themeData['edit_game_rating_2'] = ($get_game['rating']==2) ? 'selected' : '';
					$themeData['edit_game_rating_2_5'] = ($get_game['rating']==2.5) ? 'selected' : '';
					$themeData['edit_game_rating_3'] = ($get_game['rating']==3) ? 'selected' : '';
					$themeData['edit_game_rating_3_5'] = ($get_game['rating']==3.5) ? 'selected' : '';
					$themeData['edit_game_rating_4'] = ($get_game['rating']==4) ? 'selected' : '';
					$themeData['edit_game_rating_4_5'] = ($get_game['rating']==4.5) ? 'selected' : '';
					$themeData['edit_game_rating_5'] = ($get_game['rating']==5) ? 'selected' : '';

					$addgame_category = $Tumdconnect->query("SELECT * FROM ".CATEGORIES." WHERE id!=0");
					$gcts_r = '';
					while ( $select_category = $addgame_category->fetch_array() ) {
						if ($get_game['category'] == $select_category['id']) {
							$gcts_r .= '<option value="'.$select_category['id'].'" selected>'.$select_category['name'].'</option>';
						} else {
							$gcts_r .= '<option value="'.$select_category['id'].'">'.$select_category['name'].'</option>';
						}
					}
					$themeData['edit_game_categories'] = $gcts_r;

					$themeData['games_section_content'] = \Tumder\UI::view('admin/sections/edit-games-section');
				} else {
					$themeData['games_section_content'] = \Tumder\UI::view('welcome/error-section');
				}
			}
			else {
				$themeData['games_section_content'] = \Tumder\UI::view('welcome/error-section');
			}

			$themeData['page_admin_content'] = \Tumder\UI::view('admin/games');
		}

		elseif (isset($_GET['section']) && $_GET['section'] == "categories") {
			if (!isset($_GET['action']) || $_GET['action'] == "view") {
				$sql_global_categories = $Tumdconnect->query("SELECT * FROM ".CATEGORIES." WHERE id!=0");
				$gcts_r = '';
				while ($global_categories = $sql_global_categories->fetch_array() ) {
					$themeData['view_category_id'] = $global_categories['id'];
					$themeData['view_category_name'] = $global_categories['name'];
					$themeData['view_category_button_delete'] = ($global_categories['id'] != 1) ? \Tumder\UI::view('admin/sections/view-categories-button-delete') : '';

					$gcts_r .= \Tumder\UI::view('admin/sections/view-categories-list');
				}
				$themeData['view_categories_list'] = $gcts_r;

				$themeData['categories_section_content'] = \Tumder\UI::view('admin/sections/view-categories-section');
			}
			elseif (isset($_GET['action']) && $_GET['action'] == "add") {
				$themeData['categories_section_content'] = \Tumder\UI::view('admin/sections/view-categories-add');
			}
			elseif (isset($_GET['action']) && $_GET['action'] == "edit" && !empty($_GET['cid'])) {
				$category_id = secureEncode($_GET['cid']);
				$sql_select_editcategory = $Tumdconnect->query("SELECT * FROM ".CATEGORIES." WHERE id='{$category_id}'");
				if ($sql_select_editcategory->num_rows == 1) {
					$edit_category = $sql_select_editcategory->fetch_array();
					$themeData['edit_category_id'] = $edit_category['id'];
					$themeData['edit_category_name'] = $edit_category['name'];
					$themeData['categories_section_content'] = \Tumder\UI::view('admin/sections/view-categories-edit');
				} else {
					$themeData['categories_section_content'] = \Tumder\UI::view('welcome/error-section');
				}
			}
			else {
				$themeData['categories_section_content'] = \Tumder\UI::view('welcome/error-section');
			}

			$themeData['page_admin_content'] = \Tumder\UI::view('admin/categories');
		}

		elseif (isset($_GET['section']) && $_GET['section'] == "users") {
			if (isset($_GET['action']) && $_GET['action'] == "edit" && !empty($_GET['uid'])) {
				$get_user_uid = secureEncode($_GET['uid']);
				if (is_numeric($get_user_uid)) {
		        	$user_uid_type = "id = " . $get_user_uid;
		    	} elseif (preg_match('/[A-Za-z0-9_]/', $get_user_uid)) {
		        	$user_uid_type = "username = '{$get_user_uid}'";
		    	}
		    	$get_user_query = $Tumdconnect->query("SELECT * FROM ".ACCOUNTS." WHERE " . $user_uid_type);

		    	if ($get_user_query->num_rows > 0) {
		    		$get_user_account = $get_user_query->fetch_array();
		    		$get_user_info = getInfo($get_user_account['id']);
		    		$themeData['user_profile_avatar'] = getAvatar($get_user_account['avatar_id'], $get_user_info['gender']);
		    		$themeData['user_profile_id'] = $get_user_account['id'];
		    		$themeData['user_profile_username'] = $get_user_account['username'];
		    		$themeData['user_profile_name'] = $get_user_account['name'];
		    		$themeData['user_profile_ip'] = $get_user_account['ip'];
		    		$themeData['user_profile_email'] = $get_user_account['email'];
		    		$themeData['user_profile_xp'] = $get_user_account['xp'];
		    		$themeData['user_profile_about'] = $get_user_info['about'];
		    		$themeData['user_profile_rank_status_0'] = ($get_user_account['admin'] == 0) ? 'selected' : '';
		    		$themeData['user_profile_rank_status_1'] = ($get_user_account['admin'] == 1) ? 'selected' : '';
		    		$themeData['user_profile_gender_status_1'] = ($get_user_info['gender'] == 1) ? 'selected' : '';
		    		$themeData['user_profile_gender_status_2'] = ($get_user_info['gender'] == 2) ? 'selected' : '';
		    		$themeData['user_profile_active_status'] = ( $get_user_account['active'] ) ? 'checked' : '';
		    		$ueLANG_dir = opendir('assets/language/');
					$ueLANG_dr_array = array();
					while (false !== ($file = readdir($ueLANG_dir))) {
						$ueLANG_dr_array[] = $file;
					}
					closedir($ueLANG_dir);
					$gusrlng_r = '';
					foreach($ueLANG_dr_array as $file) {
						if ($file != "." && $file != ".." && $file != "Thumbs.db" && $file != ".DS_Store" && $file != "images") {
							$val_file = str_replace('.php', '', $file);
							$gusrlng_r .= ($get_user_account['language'] == $val_file) ? 
								'<option value="'.$val_file.'" selected>'.$val_file.'</option>' : 
								'<option value="'.$val_file.'">'.$val_file.'</option>';
						}
					}
					$themeData['user_profile_language_option'] = $gusrlng_r;

					$themeData['user_section_content'] = \Tumder\UI::view('admin/sections/view-user-edit');
				} 
				else {
					$themeData['user_section_content'] = \Tumder\UI::view('welcome/error');
				}
			}
			else {
				$themeData['user_section_content'] = \Tumder\UI::view('admin/sections/view-user-search');
			}

			$themeData['page_admin_content'] = \Tumder\UI::view('admin/users');
		}

		elseif (isset($_GET['section']) && $_GET['section'] == "ads") {
			$get_ads_data = $Tumdconnect->query("SELECT * FROM ".ADS.""); 
			$get_ads = $get_ads_data->fetch_array();
			$themeData['ads_area_header'] = $get_ads['header'];
			$themeData['ads_area_footer'] = $get_ads['footer'];
			$themeData['ads_area_column_one'] = $get_ads['column_one'];
			$themeData['ads_area_gametop'] = $get_ads['gametop'];
			$themeData['ads_area_gamebottom'] = $get_ads['gamebottom'];
			$themeData['ads_area_gameinfo'] = $get_ads['gameinfo'];

			$themeData['page_admin_content'] = \Tumder\UI::view('admin/ads');
		}

		elseif (isset($_GET['section']) && $_GET['section'] == "reports") {
			$get_reports = $Tumdconnect->query("SELECT * FROM ".REPORTS." WHERE report_id!=0");
			if($get_reports->num_rows > 0) {

				$rpts_r = '';
				while ($report = $get_reports->fetch_array()) {
					$datauser = getData($report['user_id'], 'id,name,username');
					$themeData['report_data_user_id'] = ( $datauser ) ? $datauser['id'] : 0;
					$themeData['report_data_user'] = ( $datauser ) ? '<a href="'.siteUrl().'/profile/'.$datauser['username'].'">'.$datauser['name'].'</a>':'Anom';
					$themeData['report_data_info'] = $report['report_info'];
					$themeData['report_data_id_reported'] = $report['id_reported'];
					$themeData['report_data_id'] = $report['report_id'];

					$rpts_r .= \Tumder\UI::view('admin/sections/view-reports-list');
				}
				$themeData['reports_list'] = $rpts_r;
				$themeData['reports_section_content'] = \Tumder\UI::view('admin/sections/view-reports-section');
			}
			else {
				$themeData['reports_section_content'] = \Tumder\UI::view('admin/sections/view-reports-notfound');
			}

			$themeData['page_admin_content'] = \Tumder\UI::view('admin/reports');
		}

		else {
			$themeData['page_admin_content'] = \Tumder\UI::view('welcome/error-section');
		}

		$themeData['page_content'] = \Tumder\UI::view('admin/content');
	}