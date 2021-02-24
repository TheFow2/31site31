<?php 

    if ( ! defined('API_PILOT') ) exit();

    	if ( !isset($_GET['gid']) && ( !empty($_GET['gid']) ) ) exit();
    	
	    $get_game_data = getGame($_GET['gid']);

	    if ( !$get_game_data ) exit();

	    if ( $get_game_data['game_type'] == 'swf' ) 
	    {
	    	$get_game_type_container = ABSPATH . 'assets/api/sources/src.game-sandbox.api/flash.game.php';
	    }
	    else 
	    {
	    	$get_game_type_container = ABSPATH . 'assets/api/sources/src.game-sandbox.api/iframe.game.php';

	    }

	    include( ABSPATH . 'assets/api/sources/src.game-sandbox.api/template.html.php' );