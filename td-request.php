<?php 
/**
* Tumder Project - A platform for the fun
* @copyright (c) 2016 Loois Sndr. All rights reserved.
*
* @author Loois Sndr
* @since 2016
*/

require_once( dirname( __FILE__ ) . '/td-load.php');

define('R_PILOT', true);

function accessOnly()
{
    if ( !is_logged() )
    {
        global $Tumdconnect;
        $Tumdconnect->close();
        exit("Log in to continue, please");
    }
}

$t = (!isset($_GET['t'])) ? "" : secureEncode($_GET['t']);
$a = (!isset($_GET['a'])) ? "" : secureEncode($_GET['a']);

$data = array(
    'status' => 417
);

if (empty($t))
{
    exit('a');
}

include(ABSPATH . 'assets/requests/' . $t . '.php');