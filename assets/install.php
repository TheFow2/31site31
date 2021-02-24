<?php 
/**
* @package Tumder
*/

error_reporting(0);

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( dirname( __FILE__ ) ) . '/' );
}

require_once( dirname( dirname( __FILE__ ) ) . '/td-load.php' );

$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 1;

function display_header( $body_classes = '' ) {
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex,nofollow" />
	<title>Tumder &rsaquo; Installation</title>
	<link rel="stylesheet" href="../static/libs/css/install.css">
	<link rel="stylesheet" href="../static/libs/css/buttons.css">
</head>
<body class="td-core-ui">
<p id="logo"><a href="https://codecanyon.net/item/tumder-an-arcade-games-platform/18726994?ref=Tumder"></a></p>

<?php
} // end display_header()

function display_setup_form( $error = null ) {
	$weblog_title = isset( $_POST['weblog_title'] ) ? trim( stripslashes( $_POST['weblog_title'] ) ) : '';
	$weblog_url = isset( $_POST['weblog_url'] ) ? trim( stripslashes( $_POST['weblog_url'] ) ) : '';
	$user_name = isset($_POST['user_name']) ? trim( stripslashes( $_POST['user_name'] ) ) : '';
	$admin_email  = isset( $_POST['admin_email']  ) ? trim( stripslashes( $_POST['admin_email'] ) ) : '';

	if ( ! is_null( $error ) ) {
?>
<h1>Welcome</h1>
<p class="message"><?php echo $error; ?></p>
<?php } ?>
<form id="setup" method="post" action="install.php?step=2" novalidate="novalidate">
	<table class="form-table">
		<tr>
			<th scope="row"><label for="weblog_title">Site Title</label></th>
			<td><input name="weblog_title" type="text" id="weblog_title" size="25" value="<?php echo $weblog_title ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="weblog_url">Site Url</label></th>
			<td>
				<input name="weblog_url" type="text" id="weblog_url" size="25" value="<?php echo $weblog_url ?>" />
				<p><strong>e.g</strong> http://mysite.com</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="user_login">Username</label></th>
			<td>
				<input name="user_name" type="text" id="user_login" size="25" value="<?php echo $user_name ?>" />
				<p>Usernames can have only alphanumeric characters and underscores</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="pass1">Password</label></th>
			<td><input name="admin_password" type="password" id="pass1" size="25" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="pass2">Repeat Password</label></th>
			<td><input name="admin_password2" type="password" id="pass2" size="25" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="admin_email">Your Email</label></th>
			<td><input name="admin_email" type="email" id="admin_email" size="25" value="<?php echo $admin_email ?>" /></td>
		</tr>
	</table>
	<p class="step"><input type="submit" name="Submit" id="submit" class="button button-large" value="Install Tumder" /></p>
</form>
<?php
} // end display_setup_form()

// Let's check to make sure Tumder isn't already installed.
if ( td_installing() ) {
	display_header();
	die(
		'<h1>Already Installed</h1>' .
		'<p>You appear to have already installed Tumder.</p>' .
		'<p class="step"><a href="'.siteUrl().'/login" class="button button-large">Log In</a></p>' .
		'</body></html>'
	);
}

if ( !td_installing(1) ) {
	header( 'Location: setup-config.php' );
	exit;
}

$required_php_version = '5.4.0';
$php_version    = phpversion();
$php_compat     = version_compare( $php_version, $required_php_version, '>=' );
$mysqli_required   = function_exists('mysqli_connect');

if ( !$mysqli_required && !$php_compat ) {
	$compat = 'You cannot install because Tumder requires PHP version '.$required_php_version.' or higher and MySQLi extension to work. You are running PHP version '.$php_version;
} elseif ( !$php_compat ) {
	$compat = 'You cannot install because Tumder requires PHP version '.$required_php_version.' or higher. You are running version '.$php_version;
} elseif ( !$mysqli_required ) {
	$compat = 'You cannot install because Tumder requires MySQLi extension to work. Please enable or install MySQLi and try again.';
}

if ( !$mysqli_required || !$php_compat ) {
	display_header();
	die( '<h1>Insufficient Requirements</h1><p>' . $compat . '</p></body></html>' );
}

switch($step) {
	case 1:
		display_header();
?>
<h1>Welcome</h1>
<p>Please provide the following information. Don&#8217;t worry, you can always change these settings later.</p>

<?php
		display_setup_form();
	break;

	case 2:
		require_once( ABSPATH . 'assets/includes/config.php');
		$td_db = @new mysqli($dbTumd['host'], $dbTumd['user'], $dbTumd['pass'], $dbTumd['name']);

		if ($td_db->connect_errno) {
			display_header();
			die( '<h1>Error establishing a database connection</h1><p>This either means that the username and password information in your <code>config.php</code> file is incorrect or we can\'t contact the database server at <code>localhost</code>. This could mean your host\'s database server is down.</p></body></html>' );
		}

		// Fill in the data we gathered
		$weblog_title = isset( $_POST['weblog_title'] ) ? trim( stripslashes( $_POST['weblog_title'] ) ) : 'Tumder';
		$weblog_url = isset( $_POST['weblog_url'] ) ? trim( stripslashes( $_POST['weblog_url'] ) ) : '';
		$user_name = isset($_POST['user_name']) ? trim( stripslashes( $_POST['user_name'] ) ) : '';
		$admin_password = isset($_POST['admin_password']) ? sha1( str_rot13( $_POST['admin_password'] . $encryption ) ) : '';
		$admin_password_check = isset($_POST['admin_password2']) ? sha1( str_rot13( $_POST['admin_password2'] . $encryption ) ) : '';
		$admin_email  = isset( $_POST['admin_email'] ) ? trim( stripslashes( $_POST['admin_email'] ) ) : '';

		// Check email address.
		$error = false;
		if ( empty( $weblog_url ) ) {
			display_header();
			display_setup_form( 'Please provide a valid URL' );
			$error = true;
		} elseif ( empty( $user_name ) ) {
			display_header();
			display_setup_form( 'Please provide a valid username' );
			$error = true;
		} elseif ( !preg_match("/^[a-zA-Z0-9_]+$/", $user_name) && ctype_digit($user_name) ) {
			display_header();
			display_setup_form( 'The username you provided has invalid characters.' );
			$error = true;
		} elseif ( empty( $admin_password ) && $admin_password == NULL) {
			display_header();
			display_setup_form( 'Please provide a valid and secure password' );
			$error = true;
		} elseif ( $admin_password != $admin_password_check ) {
			display_header();
			display_setup_form( 'Your passwords do not match. Please try again.' );
			$error = true;
		} elseif ( empty( $admin_email ) ) {
			display_header();
			display_setup_form( 'You must provide an email address.' );
			$error = true;
		} elseif ( !filter_var($admin_email, FILTER_VALIDATE_EMAIL) ) {
			display_header();
			display_setup_form( 'Sorry, that isn&#8217;t a valid email address. Email addresses look like <code>username@example.com</code>.' );
			$error = true;
		}

		if ( $error === false ) {
			$db_tables = db_array_install($weblog_url, $weblog_title, $user_name, $admin_password, $admin_email);
			foreach ($db_tables as $table) {
				$td_db->query($table);
			}
			$handle = fopen( ABSPATH . 'assets/includes/install-blank.php', 'w' );
			fwrite( $handle );
			fclose( $handle );

			display_header();
?>
<h1>Success!</h1>
<p>Tumder has been installed. Thank you, and enjoy!</p>
<table class="form-table install-success">
	<tr>
		<th>Username</th>
		<td><?php echo $user_name ?></td>
	</tr>
	<tr>
		<th>Password</th>
		<td>•••••••</td>
	</tr>
</table>
<p class="step"><a href="<?php echo $weblog_url; ?>/login" class="button button-large">Log In</a></p>
<?php
		}
	break;
} ?>
</body>
</html>