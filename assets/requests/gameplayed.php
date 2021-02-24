<?php 
    if (!defined('R_PILOT')) { exit(); }
    	
	if (isset($_POST['gid'])) {
        $game_id = secureEncode($_POST['gid']);
        $Tumdconnect->query("UPDATE ".GAMES." SET plays=plays+1 WHERE game_id='{$game_id}'");
    }

    header("Content-type: application/json");
    echo json_encode($data);
    $Tumdconnect->close();
    exit();