<?php 
    if (!defined('R_PILOT')) { exit(); }

    	if (!empty($_POST['game_name']) && !empty($_POST['game_width']) && !empty($_POST['game_height']) && !empty($_POST['game_category'])) {
            if (isset($_POST['game_name']) && isset($_POST['game_width']) && isset($_POST['game_height']) && isset($_POST['game_category']) && isset($_POST['game_published']) && isset($_POST['game_featured'])) {
                $addgame = array();
                $addgame['name']         = secureEncode($_POST['game_name']);
                $addgame['description']  = $_POST['game_description'];
                $addgame['instructions'] = $_POST['game_instructions'];
                $addgame['width']        = secureEncode($_POST['game_width']);
                $addgame['height']       = secureEncode($_POST['game_height']);
                $addgame['category']     = secureEncode($_POST['game_category']);
                $addgame['published']    = secureEncode($_POST['game_published']);
                $addgame['featured']     = secureEncode($_POST['game_featured']);
                $addgame['import']       = secureEncode($_POST['game_import']);
                $addgame['game_type']    = secureEncode($_POST['game_file_type']);
                $addgame['rating']       = secureEncode($_POST['game_rating']);

                if ($addgame['import'] == 0) {
                    if (!empty($_POST['game_image'])) {
                        if (!empty($_POST['game_file'])) {
                            $addgame_mediaurl = secureEncode($_POST['game_image']);
                            $addgame['file']  = secureEncode($_POST['game_file']);

                            $Tumdconnect->query("INSERT INTO ".GAMES." (name, image, import, category, description, instructions, file, game_type, w, h, date_added, published, featured, rating) VALUES ('{$addgame['name']}', '{$addgame_mediaurl}', '0', '{$addgame['category']}', '{$addgame['description']}', '{$addgame['instructions']}', '{$addgame['file']}', '{$addgame['game_type']}', '{$addgame['width']}', '{$addgame['height']}', '{$time}', '{$addgame['published']}', '{$addgame['featured']}', '{$addgame['rating']}')");

                            $data['status'] = 200;
                            $data['success_message'] = $lang['game_saved'];
                        } else { $data['error_message'] = $lang['fileurl_empty']; }
                    } else { $data['error_message'] = $lang['imageurl_empty']; }
                } 
                else if ($addgame['import'] == 1) {
                    if (isset($_FILES['__game_image']['tmp_name']) && isset($_FILES['__game_file']['tmp_name'])) {
                        if ($_FILES['__game_image']['size'] > 1024) {
                            $addgame_media = $_FILES['__game_image'];
                            $addgame['image'] = uploadGameMedia($addgame_media);
                            $addgame_mediaurl = $addgame['image']['url'].'.'.$addgame['image']['extension'];

                            $game_target_path = "data-photo/data-game/games/";
                            $game_target_path = $game_target_path.basename($_FILES['__game_file']['name']); 
                            if ($_FILES['__game_file']['type'] == "application/x-shockwave-flash") {
                                if(move_uploaded_file($_FILES['__game_file']['tmp_name'], $game_target_path)) {
                                   $Tumdconnect->query("INSERT INTO ".GAMES." (name, image, import, category, description, instructions, file, game_type, w, h, date_added, published, featured) VALUES ('{$addgame['name']}', '{$addgame_mediaurl}', '1', '{$addgame['category']}', '{$addgame['description']}', '{$addgame['instructions']}', '{$game_target_path}', '{$addgame['game_type']}', '{$addgame['width']}', '{$addgame['height']}', '{$time}', '{$addgame['published']}', '{$addgame['featured']}')");

                                    $data['status'] = 200;
                                    $data['success_message'] = $lang['game_saved'];
                                } else { $data['error_message'] = $lang['error_file_upload']; }
                            } else { $data['error_message'] = $lang['error_file_extension']; }
                        } else { $data['error_message'] = $lang['error_image_size']; }
                    } else { $data['error_message'] = $lang['message_select_img_files']; }
                } else { $data['error_message'] = $lang['error_message']; }
            } else { $data['error_message'] = $lang['error_message']; }
        } else { $data['error_message'] = $lang['empty_place']; }