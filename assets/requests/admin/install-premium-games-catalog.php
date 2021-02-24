<?php 

if ( !defined('R_PILOT') ) exit();
    
$catalog = file_get_contents( 'https://gamemonetize.com/feed.json' );

if ( !!$catalog ) {
    $games = json_decode($catalog, true);

    $i = 0;
    foreach( $games as $game ) {
        $query_feed_game = $Tumdconnect->query("SELECT catalog_id FROM ".GAMES." WHERE catalog_id='gamemonetize-" . $game['id']);

        if ($query_feed_game->num_rows == 0) {

            $game_data = array();
            $game_data['catalog_id'] = secureEncode($game['id']);
            $game_data['name'] = secureEncode($game['title']);
            $game_data['description'] = !empty($game['description']) ? secureEncode($game['description']) : '';
            $game_data['instructions'] = !empty($game['instructions']) ? secureEncode($game['instructions']) : '';
            $game_data['file'] = secureEncode($game['url']);
            $game_data['width'] = $game['width'];
            $game_data['height'] = $game['height'];
            $game_data['image'] = $game['thumb'];

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
                'gamemonetize-{$game_data['catalog_id']}',
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

            $i++;
        }
    }

    sleep(0.7);

    $data['message'] = $i . ' ' . $lang['admin_premium_games_installed'];
} else {
    $data['error_message'] = 'Something went wrong!';
}