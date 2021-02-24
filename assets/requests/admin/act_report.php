<?php 
    if (!defined('R_PILOT')) exit();
    
		$report_id = secureEncode($_POST['rp_id']);

        if (isset($_POST['uid'])) {
            $uid = secureEncode($_POST['uid']);
            $xp_report = $config['xp_report'];
            $Tumdconnect->query("UPDATE ".ACCOUNTS." SET xp = xp+$xp_report WHERE id='{$uid}'");
        }
        $Tumdconnect->query("DELETE FROM ".REPORTS." WHERE report_id='{$report_id}'");