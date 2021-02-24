<?php 

    if (!empty($_GET['id'])) {
        $get_game_id = (int) secureEncode($_GET['id']);
    
        if (is_numeric($get_game_id) && $get_game_id > 0) {
            $get_game = getGame($get_game_id);
            
            if ( $get_game ) {
                $get_game_data = gameData($get_game);
                if ( is_logged() ) {
                    $sql_verify_fav = $Tumdconnect->query("SELECT game_id FROM ".USER_GAME." WHERE user_id='{$userData['id']}' AND game_id='{$get_game['game_id']}' AND type='favorite'");
                    $themeData['play_game_favorite_btn_class'] = ($sql_verify_fav->num_rows > 0) ? 'fav-added' : '';

                    $Tumdconnect->query("INSERT INTO ".USER_GAME." (user_id,game_id,date_added,type) VALUES ('{$userData['id']}', '{$get_game['game_id']}', '{$time}', 'played')");
                }

                $themeData['play_game_ads_gametop'] = getADS('gametop');
                $themeData['play_game_ads_gamebottom'] = getADS('gamebottom');
                $themeData['play_game_ads_gameinfo'] = getADS('gameinfo');
                $themeData['play_game_embed'] = $get_game_data['embed'];
                $themeData['play_game_name'] = $get_game_data['name'];
                $themeData['play_game_image'] = $get_game_data['image_url'];
                $themeData['play_game_url'] = $get_game_data['game_url'];
                $themeData['play_game_admin_btn'] = $get_game_data['admin_edit'];
                $themeData['play_game_date'] = $get_game_data['date_added'];
                $themeData['play_game_plays'] = $get_game_data['plays'];
                $themeData['play_game_desc'] = $get_game_data['description'];
                $themeData['play_game_inst'] = $get_game_data['instructions'];
                $themeData['play_game_rating'] = $get_game['rating'];
                $themeData['play_game_id'] = $get_game['game_id'];
                $themeData['play_game_ads_counter'] = ( $config['ads_status'] ) ? \Tumder\UI::view('game/play-ads-counter') : '';
                $themeData['play_game_display'] = ( $config['ads_status'] ) ? 'display:none;':'';
                $themeData['play_game_favorite_btn'] = ( is_logged() ) ? \Tumder\UI::view('game/buttons/favorite-button-on') : \Tumder\UI::view('game/buttons/favorite-button-off');
                $themeData['play_sidebar_widgets'] = getSidebarWidget('top-star');
                $themeData['play_widget_carousel_random_games'] = getCarouselWidget('carousel_random_games', 3);

                $themeData['playgame_main_content'] = ( is_admin() ) ? \Tumder\UI::view('game/play/admin-game-tools') : '';
                
                /* Load social share buttons */
                $themeData['playgame_main_content'] .= \Tumder\UI::view('game/play/social-share-buttons');

                /* Main page content */
                $themeData['page_content'] = \Tumder\UI::view('game/play');

            } else { $themeData['page_content'] = \Tumder\UI::view('game/error'); }
        } else { $themeData['page_content'] = \Tumder\UI::view('game/error'); }
    } else { $themeData['page_content'] = \Tumder\UI::view('game/error'); }