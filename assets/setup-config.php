<?php 
/**
* @package Tumder
* @subpackage Install Setup
*
* Install setup design by WordPress
* All Rights Reserved
*/

error_reporting(0);

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( dirname( __FILE__ ) ) . '/' );
}

$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 0;

function setup_config_display_header() 
{
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow" />
	<title>Tumder &rsaquo; Setup Configuration File</title>
	<link rel="stylesheet" href="../static/libs/css/install.css">
	<link rel="stylesheet" href="../static/libs/css/buttons.css">
</head>
<body class="td-core-ui">
<p id="logo"><a href="https://codecanyon.net/item/tumder-an-arcade-games-platform/18726994?ref=Tumder"></a></p>
<?php 
} // end function setup_config_display_header();

// Check if config.php not exist
if ( !file_exists( ABSPATH . 'assets/includes/config.php') ) {
	switch($step) {
		case 0:
			setup_config_display_header();
?>
<p>Welcome to Tumder. Before getting started, we need some information on the database. You will need to know the following items before proceeding.</p>
<ol>
	<li>Database name</li>
	<li>Database username</li>
	<li>Database password</li>
	<li>Database host</li>
</ol>

<p>In all likelihood, these items were supplied to you by your Web Host. If you don&#8217;t have this information, then you will need to contact them before you can continue. If you&#8217;re all ready&hellip;</p>

<p class="step"><a href="setup-config.php?step=1" class="button button-large">Let&#8217;s go!</a></p>
<?php
	break;

	case 1:
		setup_config_display_header();
?>
<h1 class="screen-reader-text">Set up your database connection</h1>
<form method="post" action="setup-config.php?step=2">
	<p>Below you should enter your database connection details. If you&#8217;re not sure about these, contact your host.</p>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="dbname">Database Name</label></th>
			<td><input name="dbname" id="dbname" type="text" size="25" /></td>
			<td>The name of the database you want to use with Tumder</td>
		</tr>
		<tr>
			<th scope="row"><label for="uname">Username</label></th>
			<td><input name="uname" id="uname" type="text" size="25" /></td>
			<td>Your database username.</td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd">Password</label></th>
			<td><input name="pwd" id="pwd" type="text" size="25" autocomplete="off" /></td>
			<td>Your database password.</td>
		</tr>
		<tr>
			<th scope="row"><label for="dbhost">Database Host</label></th>
			<td><input name="dbhost" id="dbhost" type="text" size="25" value="localhost" /></td>
			<td>You should be able to get this info from your web host, if <code>localhost</code> doesn&#8217;t work.</td>
		</tr>
	</table>
	<p class="step"><input name="submit" type="submit" value="Submit" class="button button-large" /></p>
</form>
<?php
	break;

	case 2:
	if ( isset($_POST['dbname']) && !empty($_POST['dbname']) && isset($_POST['dbhost']) && !empty($_POST['dbhost']) ) :
		$dbname = trim( stripslashes( $_POST['dbname'] ) );
		$uname = trim( stripslashes( $_POST['uname'] ) );
		$pwd = trim( stripslashes( $_POST['pwd'] ) );
		$dbhost = trim( stripslashes( $_POST['dbhost'] ) );

		$td_db = @new mysqli($dbhost, $uname, $pwd, $dbname);

		setup_config_display_header();
		if (!$td_db->connect_errno) :
			sleep(2);

			$path_to_config = ABSPATH . 'assets/includes/config.php';
			$handle = fopen( $path_to_config, 'w' );
			$config = "<?php\r\n";
			$config .= "\$dbTumd['host'] = '".$dbhost."';\r\n";
			$config .= "\$dbTumd['name'] = '".$dbname."';\r\n";
			$config .= "\$dbTumd['user'] = '".$uname."';\r\n";
			$config .= "\$dbTumd['pass'] = '".$pwd."';\r\n";
			$config .= "\$encryption = \"vmbtrvw95105595885345**#3738s**A\";";

			fwrite( $handle, $config );
			fclose( $handle );
			chmod( $path_to_config, 0666 );
?>
<h1 class="screen-reader-text">Successful database connection</h1>
<p>All right, sparky! You&#8217;ve made it through this part of the installation. Tumder can now communicate with your database. If you are ready, time now to&hellip;</p><p class="step"><a href="install.php" class="button button-large">Run the install</a></p>
<?php else : ?>
<p>
	<h1>Error establishing a database connection</h1>
	<p>This either means that the username and password information in your <code>config.php</code> file is incorrect or we can't contact the database server at <code>localhost</code>. This could mean your host's database server is down.</p>
	<ul>
		<li>Are you sure you have the correct username and password?</li>
		<li>Are you sure that you have typed the correct hostname?</li>
		<li>Are you sure that the database server is running?</li>
	</ul>
</p>
<p class="step"><a href="setup-config.php?step=1" class="button button-large">Try again</a></p>
<?php 
	endif; 
	else :
	setup_config_display_header();
?>
<p><strong>ERROR</strong>: Your database connection details must not be empty.</p>
<p class="step"><a href="setup-config.php?step=1" class="button button-large">Try again</a></p>
<?php
	endif;
	break;
}
} else {
	setup_config_display_header();
?>

<p>The file 'config.php' already exists. If you need to reset any of the configuration items in this file, please delete it first. You may try <a href="install.php">installing now</a>.</p>

<?php } ?>
</body>
</html>