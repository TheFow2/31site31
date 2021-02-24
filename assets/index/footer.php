<?php
$get_game_id = ( is_page('play') ) ? getGame($_GET['id']) : 0;

$themeData['game_ads_status_counter'] = ( $config['ads_status'] ) ? 'true' : 'false';
$themeData['get_game_id'] = $get_game_id['game_id'];

$themeData['footer'] = \Tumder\UI::view('global/footer/all');
$themeData['footer'] .= ( is_logged() ) ? \Tumder\UI::view('global/footer/logged-scripts') : \Tumder\UI::view('global/footer/unlogged-scripts');
$themeData['footer'] .= ( is_page('play') ) ? \Tumder\UI::view('global/footer/game-scripts') : '';
$themeData['footer'] .= ( is_admin() && is_page('admin') ) ? \Tumder\UI::view('global/footer/admin-panel-scripts') : '';
$themeData['footer'] .= ( is_admin() && is_page('play') ) ? \Tumder\UI::view('global/footer/admin-game-scripts') : '';
$themeData['footer'] .= addon(array('footer_tags_add_content', 'string'));