<?php 

if ( !defined('R_PILOT') ) exit();
    
if ( isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ) {
    $page = (int) $_GET['page'];
    $catalog = file_get_contents( 'https://norbigames.com/catalog/api/games?page=' . $page );

    if ( !!$catalog ) {
        $games = json_decode($catalog, true);

        foreach($games['data'] as $game) {
            $query_feed_game = $Tumdconnect->query("SELECT catalog_id FROM ".GAMES." WHERE catalog_id=" . $game['id']);

            if ($query_feed_game->num_rows == 0) {

                $game_data = array();
                $game_data['catalog_id'] = secureEncode($game['id']);
                $game_data['name'] = secureEncode($game['title']);
                $game_data['description'] = !empty($game['description']) ? secureEncode($game['description']) : '';
                $game_data['instructions'] = !empty($game['instructions']) ? secureEncode($game['instructions']) : '';
                $game_data['file'] = secureEncode($game['sandbox']);
                $game_data['width'] = $game['width'];
                $game_data['height'] = $game['height'];

                $image = json_decode($game['properties'], true);
                $game_data['image'] = ! empty($image['media']) ? $image['media'][0]['name'] : '';

                $Tumdconnect->query("INSERT INTO ".GAMES." (
                    catalog_id, 
                    name, 
                    image, 
                    description, 
                    instructions, 
                    category, 
                    file, 
                    game_type, 
                    w, 
                    h, 
                    date_added, 
                    published
                ) VALUES (
                    '{$game_data['catalog_id']}',
                    '{$game_data['name']}',
                    '{$game_data['image']}',
                    '{$game_data['description']}',
                    '{$array_item['instruction']}',
                    '1',
                    '{$game_data['file']}',
                    'html5',
                    '{$game_data['width']}',
                    '{$game_data['height']}',
                    '{$time}', 
                    '0'
                )");
            }
        }

        sleep(0.7);

        if ( $games['current_page'] !== $games['last_page'] ) {
            $data['games_procesing_message'] = '<div>'.$lang['admin_processed_pages'].' <strong>' . $games['current_page'] . ' <small>(' . round($games['current_page'] / $games['last_page'] * 100, 0) . '%)</small> </strong></div><div>'.$lang['admin_total_pages'].' <strong>' . $games['last_page'] . '</strong></div><div>'.$lang['admin_total_games'].' <strong>' . $games['total'] . '</strong></div><div style="color:#ef5350"><i class="fa fa-close"></i> '.$lang['admin_installing_dont_close'].'</div>';
            $data['next_page'] = $games['current_page'] + 1;
        } else {
            $data['reload_success'] = true;
        }
    } else {
        $data['error_message'] = 'Something went wrong!';
    }
}