<?php 

if ( is_logged() ) {
	$themeData['header_user_avatar'] = getAvatar($userData['avatar_id'], $userData['gender'], 'thumb');
	$themeData['user_panel_xp'] = numberFormat($userData['xp']);
	$themeData['csrf_logout_token'] = \Tumder\CSRF::set(3, 3600);
}

$themeData['header_class_access_menu'] = ( is_logged() ) ? '_rP5':'';

$themeData['header_menu_offline'] = ( !is_logged() ) ? \Tumder\UI::view('header/header_menu_offline') : '';

$themeData['header_panel_menu_admin'] = ( is_logged() && $userData['admin'] == 1 ) ? \Tumder\UI::view('header/header_panel_menu_admin') : '';

$themeData['footer_content'] = \Tumder\UI::view('footer/content');

$themeData['header_panel_dropdown'] = ( is_logged() ) ? \Tumder\UI::view('header/header_user_panel') : '';

$themeData['header'] = \Tumder\UI::view('header/content');