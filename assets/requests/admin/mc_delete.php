<?php 
    if (!defined('R_PILOT')) { exit(); }
    
		if (is_logged() && isset($_POST['cid'])) {
            $category_id = secureEncode($_POST['cid']);
            $sql_category_dlt = $Tumdconnect->query("SELECT id FROM ".CATEGORIES." WHERE id='{$category_id}'");

            if ($sql_category_dlt->num_rows > 0) {
                $Tumdconnect->query("DELETE FROM ".CATEGORIES." WHERE id='{$category_id}' AND id!='1'");
            }
        }