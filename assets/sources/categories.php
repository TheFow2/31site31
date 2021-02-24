<?php
	if ( !empty($_GET['category']) && is_numeric($_GET['category']) && $_GET['category'] > 0 ) {
		$get_category_id = secureEncode($_GET['category']);
		$sql_cat_query = $Tumdconnect->query("SELECT * FROM ".CATEGORIES." WHERE id=".$get_category_id);
		if ($sql_cat_query->num_rows > 0) {
			$get_category = $sql_cat_query->fetch_array();
			$sql_c_games_query = $Tumdconnect->query("SELECT * FROM ".GAMES." WHERE category = '{$get_category['id']}' AND published = '1' ORDER BY date_added DESC");
			$themeData['category_name'] = $get_category['name'];
			if ($sql_c_games_query->num_rows > 0) {
				$ctgm_r = '';
				while($cat_games = $sql_c_games_query->fetch_array()) {
					$get_game_data = gameData($cat_games);
					$themeData['category_game_name'] = $get_game_data['name'];
					$themeData['category_game_url'] = $get_game_data['game_url'];
					$themeData['category_game_image'] = $get_game_data['image_url'];
					$themeData['category_game_rating'] = $cat_games['rating'];

					$ctgm_r .= \Tumder\UI::view('category/category-games-list');
				}
				$themeData['category_games_list'] = $ctgm_r;
			} else {
				$themeData['category_games_list'] = \Tumder\UI::view('category/category-games-notfound');
			}

			$themeData['category_content'] = \Tumder\UI::view('category/category-games');
		} else {
			$themeData['category_content'] = \Tumder\UI::view('category/category-notfound');
		}
	} 

	else {
		$sql_cat_query = $Tumdconnect->query("SELECT * FROM ".CATEGORIES);
		$ct_r = '';
		while($category = $sql_cat_query->fetch_array()) {
			$themeData['category_id'] = $category['id'];
			$themeData['category_name'] = $category['name'];
			$themeData['category_url'] = siteUrl() . '/category/' . $category['id'] . '-' . slugify($category['name']);
			$ct_r .= \Tumder\UI::view('category/categories-list');
		}

		$themeData['categories_list'] = $ct_r;
		$themeData['category_content'] = \Tumder\UI::view('category/categories');
	}

	$themeData['page_content'] = \Tumder\UI::view('category/content');