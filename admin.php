<?php
require_once('config.php');
require_once('db.php');
require_once('filter.php');
require_once('comp.php');
require_once('creds.php');
require_once('csrf.php');

session_start();
//prevent csrf
if (isset($_SESSION['csrf_token'])) {
	$old_csrf_token = $_SESSION['csrf_token'];
}

$_SESSION['csrf_token'] = $new_csrf_token = get_csrf_token();

$csrf_passed = (
	isset($old_csrf_token) &&
	isset($_POST['csrf_token']) &&
	$old_csrf_token == $_POST['csrf_token']
);
//prevent unauthorized access
if (!isset($_SESSION['user_priv']) || $_SESSION['user_priv'] != 'admin') {
	http_response_code(401);
	echo 'Not Authorized';
	exit();
}

//handle database changes...
if (isset($_GET['action']) && $csrf_passed) {
	switch($_GET['action']) {
	case 'add user':
		// add user logic
		if (add_user(
			$_POST['new_user_login'],
			$_POST['new_user_pass'],
			$_POST['verify_new_user_pass'])
		) {
			echo "// add user successful";
		} else {
			echo "// add user failed";
		}
		break;
	case 'new filter':
		echo 'new filter';
		// new filter logic
		break;
	case 'remove filter':
		echo 'remove filter';
		// remove filter logic
		break;
	default:
		die("<br/>invalid action: csrf_passed=" . $csrf_passed);
	}
}
//build the forms...
$admin_forms = array();

$add_user_form = formify(
	'POST', BASE_URL . '/admin.php?action=add+user',
	array(
		inputify('hidden', 'csrf_token', array('value' => $new_csrf_token)),
		inputify('text', 'new_user_login', array('label' => 'User name: ')), '<br/>',
		inputify('password', 'new_user_pass', array('label' => 'Password: ')), '<br/>',
		inputify('password', 'verify_new_user_pass', array('label' => 'Verify Password: ')), '<br/>',
		inputify('submit', 'submit', array('value' => 'Create User'))
	),
	array('id' => 'add_user_form')
);

$admin_forms['add_user'] = array(
	'title' => 'Add user',
	'innerHTML' => $add_user_form
);

$new_filter_form = formify(
	'POST', BASE_URL . '/admin.php?action=new+filter',
	array(
		inputify('hidden', 'csrf_token', array('value' => $new_csrf_token)),
		inputify('text', 'new_filter_title', array('label' => 'Title: ', 'placeholder' => 'title')), '<br/>',
		inputify('text', 'new_filter_regex', array('label' => 'Regex: ', 'placeholder' => '/(.*)/')), '<br/>',
		inputify('text', 'new_filter_replacement', array('label' => 'Replacement: ', 'placeholder' => '$1')), '<br/>',
		tagify(
			array(
				'tag' => 'label',
				'id' => 'new_filter_note_label',
				'for' => 'new_filter_note',
				'innerHTML' => 'Note: '
			)
		),
		tagify(
			array(
				'tag' => 'textarea',
				'id' => 'new_filter_note',
				'name' => 'new_filter_note',
				'innerHTML' => ''
			)
		), '<br/>',
		inputify('submit', 'submit', array('value' => 'Create Filter'))
	),
	array('enctype' => 'multipart/form-data', 'id' => 'new_filter_form')
);

$admin_forms['new_filter'] = array(
	'title' => 'New filter',
	'innerHTML' => $new_filter_form
);

function get_filter_selections() {
	$query = 'SELECT id, regex, replacement  from filters';
	$result = db_query($query, array());
	$rows = db_all_results($result);
	$selection_format = '<option value="%s">%s =&gt; %s</option>';
	$filter_selections = '';
	foreach ($rows as $row) {
		$filter_selections .=
			sprintf($selection_format, $row['id'],
				htmlentities($row['regex']),
				htmlentities($row['replacement'])
			);
	}
	return $filter_selections;
}

$remove_filter_form = formify(
	'POST', BASE_URL . '/admin.php?action=remove+filter',
	array(
		inputify('hidden', 'csrf_token', array('value' => $new_csrf_token)),
		tagify(
			array(
				'tag' => 'select',
				'name' => 'remove_filter_id[]',
				'multiselect' => 'multiselect',
				'innerHTML' => get_filter_selections()
			)
		), '<br/>',
		inputify('submit', 'submit', array('value' => 'Remove'))
	),
	array('id' => 'remove_filter_form')
);

$admin_forms['remove_filter'] = array(
	'title' => 'Remove filter',
	'innerHTML' => $remove_filter_form
);

$buttons = '';

$admin_forms_divs = '';

foreach ($admin_forms as $key => $value) {
	$buttons .= tagify(
		array(
			'tag' => 'button',
			'onclick' => "toggleVisible('${key}_div')",
			'innerHTML' => $value['title'],
			'class' => 'header_button'
		)
	);
	$admin_forms_divs .= tagify(
		array(
			'tag' => 'div',
			'id' => "${key}_div",
			'innerHTML' => $value['innerHTML'],
			'class' => 'admin_forms_div hidden feature-box'
		)
	);
}


$head = get_default_head();

$body = (
	tagify(
		array(
			'tag' => 'div',
			'id' => 'admin_header',
			'innerHTML' => $buttons
		)
	) .
	'<h1>Admin Page</h1>' .
	$admin_forms_divs
);

echo get_document($head, $body, array());

?>
