<?php
/**
* @package Tumder
* @copyright (c) 2016 Loois Sndr. All rights reserved.
*
* @author Loois Sndr
* @since 2016-2017
*/
if ( !isset($_GET['p']) ) $_GET['p'] = 'home';

require_once dirname( __FILE__ ) . '/td-load.php';

require_once ABSPATH . 'assets/index/header_tags.php';
require_once ABSPATH . 'assets/index/header.php';
require_once ABSPATH . 'assets/index/footer.php';
require_once ABSPATH . 'assets/index/page.php';

echo \Tumder\UI::view('index');

$Tumdconnect->close();