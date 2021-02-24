<?php 
    if (!defined('R_PILOT')) { exit(); }

        if (!empty($_POST['ac_name'])) {
            if (isset($_POST['ac_name'])) {
                $category_name = secureEncode($_POST['ac_name']);
                $sql_chk_category = $Tumdconnect->query("SELECT id FROM ".CATEGORIES." WHERE name='{$category_name}'");
                if ($sql_chk_category->num_rows < 1) {
                    if (preg_match("/^[a-zA-Z- ]+$/", $category_name)) {
                        if (strlen($category_name) <= 15) {
                            $Tumdconnect->query("INSERT INTO ".CATEGORIES." (name) VALUES ('{$category_name}')");

                            $data['status'] = 200;
                            $data['success_message'] = $lang['category_registered'];
                        } else { $data['error_message'] = $lang['category_name_exceed']; }
                    } else { $data['error_message'] = $lang['invalid_characters']; }
                } else { $data['error_message'] = $lang['category_exists']; }
            } else { $data['error_message'] = $lang['error_message']; }
        } else { $data['error_message'] = $lang['must_enter_name']; }