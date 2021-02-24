<?php 
    if (!defined('R_PILOT')) { exit(); }
    
        $ads_area = array();
        $ads_area['header']     = '';
        $ads_area['footer']     = '';
        $ads_area['column_one'] = '';
        $ads_area['gametop']    = '';
        $ads_area['gamebottom'] = '';
        $ads_area['gameinfo']   = '';
        if (!empty($_POST['ad_header'])) {
        	$ads_area['header'] = $_POST['ad_header'];
        	$ads_area['header'] = str_replace("'", '"', $ads_area['header']);
        }
        if (!empty($_POST['ad_footer'])) {
        	$ads_area['footer'] = $_POST['ad_footer'];
        	$ads_area['footer'] = str_replace("'", '"', $ads_area['footer']);
        }
        if (!empty($_POST['ad_column_one'])) {
        	$ads_area['column_one'] = $_POST['ad_column_one'];
        	$ads_area['column_one'] = str_replace("'", '"', $ads_area['column_one']);
        }
        if (!empty($_POST['ad_gameTop'])) {
        	$ads_area['gametop'] = $_POST['ad_gameTop'];
        	$ads_area['gametop'] = str_replace("'", '"', $ads_area['gametop']);
        }
        if (!empty($_POST['ad_gameBottom'])) {
        	$ads_area['gamebottom'] = $_POST['ad_gameBottom'];
        	$ads_area['gamebottom'] = str_replace("'", '"', $ads_area['gamebottom']);
        }
        if (!empty($_POST['ad_gameInfo'])) {
        	$ads_area['gameinfo'] = $_POST['ad_gameInfo'];
        	$ads_area['gameinfo'] = str_replace("'", '"', $ads_area['gameinfo']);
        }

        $Tumdconnect->query("UPDATE ".ADS." SET header='{$ads_area['header']}', footer='{$ads_area['footer']}', column_one='{$ads_area['column_one']}', gametop='{$ads_area['gametop']}', gamebottom='{$ads_area['gamebottom']}', gameinfo='{$ads_area['gameinfo']}'") or die();
        $data['status'] = 200;
        $data['success_message'] = $lang['ads_saved'];