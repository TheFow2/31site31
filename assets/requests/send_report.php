<?php 
    if (!defined('R_PILOT')) { exit(); }

    if (isset($_POST['gid']) && isset($_POST['report']) && !empty($_POST['gid']) && !empty($_POST['report'])) {
        $game_reported = secureEncode($_POST['gid']);
        $info_report   = secureEncode(shortStr($_POST['report'], 100));
        $user_report = ( is_logged() ) ? $userData['id'] : '0';

        $Tumdconnect->query("INSERT INTO ".REPORTS." (id_reported,report_info,user_id,report_date) VALUES ('{$game_reported}', '{$info_report}', '{$user_report}', '{$time}')");

        $data['status'] = 200;
        $data['success_message'] = $lang['msg_alert_report'];
    } else {
        $data['error_message'] = $lang['msg_alert_report_error'];
    }

    header("Content-type: application/json");
    echo json_encode($data);
    $Tumdconnect->close();
    exit();