<?php
require_once('config.php');
require_once('db.php');
require_once('filter.php');
require_once('comp.php');
require_once('creds.php');
require_once('csrf.php');

session_start();

if (isset($_SESSION['csrf_token'])) {
	$old_csrf_token = $_SESSION['csrf_token'];
}

$_SESSION['csrf_token'] = $new_csrf_token = get_csrf_token();

$csrf_passed = (
	isset($old_csrf_token) &&
	isset($_POST['csrf_token']) &&
	$old_csrf_token == $_POST['csrf_token']
);

if (!isset($_SESSION['user_priv']) || $_SESSION['user_priv'] != 'admin') {
	http_response_code(401);
	echo 'Not Authorized';
	exit();
}

$add_user_form = formify(
	'POST', BASE_URL . '/admin.php?action=add+user',
	array(
		inputify('hidden', 'csrf_token', array('value' => $new_csrf_token)),
		inputify('text', 'new_user_login', array('label' => 'User name: ')),
		inputify('password', 'new_user_pass', array('label' => 'Password: ')),
		inputify('password', 'verify_new_user_pass', array('label' => 'Verify Password: ')),
		inputify('submit', 'submit', array('value' => 'Create User'))
	),
	array()
);

$new_filter_form = formify(
	'POST', BASE_URL . '/admin.php?action=new+filter',
	array(
		inputify('hidden', 'csrf_token', array('value' => $new_csrf_token)),
		inputify('text', 'new_filter_regex', array('label' => 'Regex: ', 'placeholder' => '/(.*)/')),
		inputify('text', 'new_filter_replacement', array('label' => 'Replacement: ', 'placeholder' => '$1')),
		inputify('submit', 'submit', array('value' => 'Create Filter'))
	),
	array()
);

function get_filter_selections() {
	$query = 'SELECT id, regex, replacement  from filters';
	$result = db_query($query, array());
	$rows = db_all_results($result);
	$selection_format = '<selection value="%s"><tr><td>%s</td><td>%s</td></tr></selection>';
	$filter_selections = '<table id="filter_selections_table">';
	foreach ($rows as $row) {
		$filter_selections .=
			sprintf($selection_format, $row['id'],
				htmlentities($row['regex']),
				htmlentities($row['replacement'])
			);
	}
	$filter_selections .= '</table>';
	return $filter_selections;
}

$remove_filter_form = formify(
	'POST', BASE_URL . '/admin.php?action=remove+filter',
	array(
		inputify('hidden', 'csrf_token', array('value' => $new_csrf_token)),
		tagify(
			array(
				'tag' => 'options',
				'name' => 'remove_filter_id[]',
				'multiselect' => 'multiselect',
				'innerHTML' => get_filter_selections()
			)
		),
		inputify('submit', 'submit', array('value' => 'Remove'))
	),
	array()
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
