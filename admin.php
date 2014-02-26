<?php
require_once('config.php');
require_once('db.php');
require_once('filter.php');
require_once('comp.php');
require_once('creds.php');

session_start();

if (isset($_SESSION['csrf_token'])) {
	$old_csrf_token = $_SESSION['csrf_token'];
}

$_SESSION['csrf_token'] = $new_csrf_token = get_csrf_token();

$csrf_passed = (
	isset($old_csrf_token) &&
	isset($_POST['csrf_token'] &&
	$old_csrf_token == $_POST['csrf_token']
);

if (!isset($_SESSION['user_priv']) || $_SESSION['user_priv'] != 'admin') {
	http_response_code(401);
	echo 'Not Authorized';
	exit();
}

$add_user_form = formify(
	inputify('hidden', 'csrf_token', array('value' => $new_csrf_token)),
	inputify('text', 'new_user_login', array('label' => 'User name: ')),
	inputify('password', 'new_user_pass', array('label' => 'Password: ')),
	inputify('password', 'verify_new_user_pass', array('label' => 'Verify Password: ')),
	inputify('submit', 'submit', array('value' => 'Create User'))
);

$new_filter_form = formify(
	inputify('hidden', 'csrf_token', array('value' => $new_csrf_token)),
	inputify('text', 'new_filter_regex', array('label' => 'Regex: ', 'placeholder' => '/(.*)/')),
	inputify('text', 'new_filter_replacement', array('label' => 'Replacement: ', 'placeholder' => '$1')),
	inputify('submit', 'submit', array('value' => 'Create Filter'))
);




echo 'Admin Page!';

if (isset($_GET['action'])) {
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
