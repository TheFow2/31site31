<?php 
    if (!defined('R_PILOT')) exit();
    
		if (is_logged() && isset($_POST['gid'])) {
            $manage_game_id = secureEncode($_POST['gid']);
            $sql_manage_pb = $Tumdconnect->query("SELECT published FROM ".GAMES." WHERE game_id='{$manage_game_id}'");
            $manage_pb = $sql_manage_pb->fetch_array();

            if ($manage_pb['published'] == 1) {
                # Private game
                $Tumdconnect->query("UPDATE ".GAMES." SET published='0' WHERE game_id='{$manage_game_id}'");
            } else {
                # Publish game
                $Tumdconnect->query("UPDATE ".GAMES." SET published='1' WHERE game_id='{$manage_game_id}'");
            }
        }