<?php
/**
* Tumder Project - An awesome arcade platform
* @copyright (c) All rights reserved
* @author Loois Sndr
*
* @package Tumder - Developers API
* @since v2.1
*/
if ( !defined('API_PILOT') ) exit();

$l = ( !isset($_GET['l']) ) ? '' : secureEncode( $_GET['l'] );
$g = ( !isset($_GET['c']) ) ? '' : secureEncode( $_GET['c'] );

if ( empty($l) ) die();

$data = array();

/* Load API */
include( ABSPATH . 'assets/api/sources/' . $l . '.api.php' );