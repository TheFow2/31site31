<?php 
    if (!defined('R_PILOT')) { exit(); }

        if (!empty($_POST['ec_name'])) {
            if (isset($_POST['ec_name'])) {
                $ec_name = secureEncode($_POST['ec_name']);
                $ec_id   = secureEncode($_POST['ec_id']);
                $sql_chk_category = $Tumdconnect->query("SELECT id FROM ".CATEGORIES." WHERE name='{$ec_name}'");
                if ($sql_chk_category->num_rows == 0) {
                    if (preg_match("/^[a-zA-Z- ]+$/", $ec_name)) {
                        if (strlen($ec_name) <= 15) {
                            $Tumdconnect->query("UPDATE ".CATEGORIES." SET name='{$ec_name}' WHERE id='{$ec_id}'");
                            $data = array(
                                'status' => 200,
                                'success_message' => $lang['category_edited']
                            );
                        } else { $data['error_message'] = $lang['category_name_exceed']; }
                    } else { $data['error_message'] = $lang['invalid_characters']; }
                } else { $data['error_message'] = $lang['category_exists']; }
            } else { $data['error_message'] = $lang['error_message']; }
        } else { $data['error_message'] = $lang['must_enter_name']; }