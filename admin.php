<?php
require_once('comp.php');
require_once('creds.php');

session_start();

if (isset($_SESSION['csrf_token'])) {
	$old_csrf_token = $_SESSION['csrf_token'];
}

$_SESSION['csrf_token'] = $new_csrf_token = get_csrf_token();

if (!isset($_SESSION['user_priv']) || $_SESSION['user_priv'] != 'admin') {
	http_response_code(401);
	echo 'Not Authorized';
	exit();
}

echo 'Admin Page!';

if (isset($_GET['action'])) {
	$csrf_passed = (isset($old_csrf_token) &&
		$old_csrf_token == $_POST['csrf_token']);
	switch($_GET['action']) {
	case 'add user':
		// add user logic
	case 'new filter':
		// new filter logic
	case 'remove filter':
		// remove filter logic
	default:
		die('invalid action');
	}
}










?>
