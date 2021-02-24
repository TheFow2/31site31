<?php 
    if (!defined('R_PILOT')) { exit(); }

    	if (!empty($_POST['eg_name']) && !empty($_POST['eg_width']) && !empty($_POST['eg_height']) && !empty($_POST['eg_category'])) {
            if (isset($_POST['eg_name']) && isset($_POST['eg_width']) && isset($_POST['eg_height']) && isset($_POST['eg_category'])) {
                $editgame = array();
                $editgame['name']         = secureEncode($_POST['eg_name']);
                $editgame['description']  = $_POST['eg_description'];
                $editgame['instructions'] = $_POST['eg_instructions'];
                $editgame['width']        = secureEncode($_POST['eg_width']);
                $editgame['height']       = secureEncode($_POST['eg_height']);
                $editgame['category']     = secureEncode($_POST['eg_category']);
                $editgame['import']       = secureEncode($_POST['eg_import']);
                $editgame['type']         = secureEncode($_POST['eg_file_type']);
                $editgame['id']           = secureEncode($_POST['eg_id']);
                $editgame['rating']       = secureEncode($_POST['eg_rating']);

                if ($editgame['import'] == 0) {
                    if (!empty($_POST['eg_image'])) {
                        if (!empty($_POST['eg_file'])) {
                            $editgame_mediaurl = secureEncode($_POST['eg_image']);
                            $editgame['file'] = secureEncode($_POST['eg_file']);

                            $Tumdconnect->query("UPDATE ".GAMES." SET name='{$editgame['name']}', image='{$editgame_mediaurl}', import='0', category='{$editgame['category']}', description='{$editgame['description']}', instructions='{$editgame['instructions']}', file='{$editgame['file']}', game_type='{$editgame['type']}', w='{$editgame['width']}', h='{$editgame['height']}', rating='{$editgame['rating']}' WHERE game_id='{$editgame['id']}'");

                            $data = array(
                                'status' => 200,
                                'success_message' => $lang['game_saved'],
                                'game_name' => $editgame['name'],
                                'game_img' => $editgame_mediaurl
                            );
                        } else {
                            $data['error_message'] = $lang['fileurl_empty'];
                        }
                    } else {
                        $data['error_message'] = $lang['imageurl_empty'];
                    }
                } 
                else if ($editgame['import'] == 1) {
                    if (isset($_FILES['__eg_image']['tmp_name']) && isset($_FILES['__eg_file']['tmp_name'])) {
                        if ($_FILES['__eg_image']['size'] > 1024) {
                            $editgame_media = $_FILES['__eg_image'];
                            $editgame['image'] = uploadGameMedia($editgame_media);
                            $editgame_mediaurl = $editgame['image']['url'].'.'.$editgame['image']['extension'];

                            $game_target_path = "data-photo/data-game/games/";
                            $game_target_path = $game_target_path.basename($_FILES['__eg_file']['name']); 
                            if ($_FILES['__eg_file']['type'] == "application/x-shockwave-flash") {
                                if(move_uploaded_file($_FILES['__eg_file']['tmp_name'], $game_target_path)) {

                                   $Tumdconnect->query("UPDATE ".GAMES." SET name='{$editgame['name']}', image='{$editgame_mediaurl}', import='1', category='{$editgame['category']}', description='{$editgame['description']}', instructions='{$editgame['instructions']}', file='{$game_target_path}', game_type='{$editgame['type']}', w='{$editgame['width']}', h='{$editgame['height']}', rating='{$editgame['rating']}' WHERE game_id='{$editgame['id']}'");

                                    $data = array(
                                        'status' => 200,
                                        'success_message' => $lang['game_saved'],
                                        'game_name' => $editgame['name'],
                                        'game_img' => $editgame_mediaurl
                                    );
                                } else { $data['error_message'] = $lang['error_file_upload']; }
                            } else { $data['error_message'] = $lang['error_file_extension']; }
                        } else { $data['error_message'] = $lang['error_image_size']; }
                    } else { $data['error_message'] = $lang['message_select_img_files']; }
                } else { $data['error_message'] = $lang['error_message']; }
            } else { $data['error_message'] = $lang['error_message']; }
        } else { $data['error_message'] = $lang['empty_place']; }