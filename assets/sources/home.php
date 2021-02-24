<?php
	$themeData['ads_header'] = getADS('header');
	$themeData['ads_footer'] = getADS('footer');
	$themeData['ads_sidebar'] = getADS('column_one');
	# >>

	$newGames_query = $Tumdconnect->query("SELECT * FROM ".GAMES." WHERE published='1' ORDER BY date_added DESC LIMIT 15");
	$ngm_r = '';
	while ($newGames = $newGames_query->fetch_array()) {
		$newGame_data = gameData($newGames);
		$themeData['new_game_url'] = $newGame_data['game_url'];
		$themeData['new_game_image'] = $newGame_data['image_url'];
		$themeData['new_game_name'] = $newGame_data['name'];
		$themeData['new_game_rating'] = $newGames['rating'];

		$ngm_r .= \Tumder\UI::view('game/list-each/new-games-list');
	}

	$themeData['new_games_list'] = $ngm_r;
	$themeData['new_games'] = \Tumder\UI::view('game/new-games');
	# >>

	$ftdGames_query = $Tumdconnect->query("SELECT * FROM ".GAMES." WHERE published='1' AND featured='1' LIMIT ".$config['featured_game_limit']);
	$ftdgm_r = '';
	while ($ftdGames = $ftdGames_query->fetch_array()) {
		$ftdGame_data = gameData($ftdGames);
		$themeData['featured_game_url'] = $ftdGame_data['game_url'];
		$themeData['featured_game_image'] = $ftdGame_data['image_url'];
		$themeData['featured_game_name'] = $ftdGame_data['name'];
		$themeData['featured_game_rating'] = $ftdGames['rating'];
		$themeData['featured_game_rating_class'] = ($ftdGames['rating'] <= 3) ? 'emp':'';

		$ftdgm_r .= \Tumder\UI::view('game/list-each/featured-games-list');
	}

	$themeData['featured_games_list'] = $ftdgm_r;
	$themeData['featured_games'] = \Tumder\UI::view('game/featured-games');
	# >>

	$MP_query = $Tumdconnect->query("SELECT * FROM ".GAMES." WHERE published='1' ORDER BY plays DESC LIMIT ".$config['mp_game_limit']);
	$MPgm_r = '';
	while ($MPGames = $MP_query->fetch_array()) {
		$MPGame_data = gameData($MPGames);
		$themeData['mp_game_url'] = $MPGame_data['game_url'];
		$themeData['mp_game_image'] = $MPGame_data['image_url'];
		$themeData['mp_game_name'] = $MPGame_data['name'];
		$themeData['mp_game_rating'] = $MPGames['rating'];
		$themeData['mp_game_plays'] = numberFormat($MPGames['plays']);
		$themeData['mp_game_ftd_icon'] = ($MPGames['featured']) ? '<span class="card-icon-corner"></span><i class="fa fa-heart icon-18 icon-corner"></i>':'';

		$MPgm_r .= \Tumder\UI::view('game/list-each/mostplayed-games-list');
	}

	$themeData['mostplayed_games_list'] = $MPgm_r;
	$themeData['mostplayed_games'] = \Tumder\UI::view('game/mostplayed-games');
	# >>

	$themeData['main_sidebar_widgets'] = getSidebarWidget('top-star');
	$themeData['main_sidebar_widgets'] .= getSidebarWidget('top-user');
	$themeData['main_sidebar_widgets'] .= getSidebarWidget('random');

	$themeData['main_sidebar'] = \Tumder\UI::view('game/main-sidebar');

	$themeData['page_content'] = \Tumder\UI::view('home/content');