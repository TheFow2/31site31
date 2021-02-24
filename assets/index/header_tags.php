<?php
$themeData['title_tag'] = title_tag();
$themeData['header_metatags'] = \Tumder\UI::view('global/header/metatags');
if ( is_page('play') ) {
	$game_data = getGame($_GET['id']);
	$game_info = gameData($game_data);
	$themeData['game_meta_name'] = $game_info['name'];
	$themeData['game_meta_game_url'] = $game_info['game_url'];
	$themeData['game_meta_image'] = $game_info['image_url'];
	$themeData['game_meta_description'] = $game_info['description'];
	$themeData['header_metatags'] .= '<link rel="canonical" href="'.$game_info['game_url'].'">';
	$themeData['header_metatags'] .= \Tumder\UI::view('global/header/game_metatags');
}
$themeData['header_title'] = \Tumder\UI::view('global/header/title');
$themeData['header_favicon'] = \Tumder\UI::view('global/header/favicon');

$themeData['header_stylesheets'] = \Tumder\UI::view('global/header/stylesheets');
$themeData['header_stylesheets'] .= ( is_admin() ) ? \Tumder\UI::view('global/header/admin-stylesheets') : '';

$themeData['header_scripts'] = \Tumder\UI::view('global/header/scripts');

$themeData['header_tags'] = \Tumder\UI::view('global/header/all');
$themeData['header_tags'] .= addon(array('header_tags_add_content', 'string'));
$themeData['header_tags'] .= ( is_page('play') ) ? addon(array('header_game_tags_add_content', 'string')) : '';