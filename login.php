<?php
require_once('config.php');
require_once('comp.php');
require_once('creds.php');

function get_login_form() {
	$inputs=array( 
		inputify(
			'text',
			'user_login',
			array('label' => 'User name: ')
		),  '<br/>',
		inputify(
			'password',
			'user_pass',
			array('label' => 'Password: ')
		), '<br/>',
		inputify(
			'submit',
			'submit',
			array('value' => 'Login')
		));
	return formify(
		'POST',
		BASE_URL . 'login.php',
		$inputs,
		array()
	);
}

session_start();
$login_message = 'Please enter your credentials.';
if (isset($_POST['user_login']) && isset($_POST['user_pass'])) {
	if (check_creds($_POST['user_login'], $_POST['user_pass'])) {
		$_SESSION['user_login'] = $_POST['user_login'];
		$_SESSION['user_priv'] = $user_priv = get_priv($_POST['user_login']);
		http_response_code(302);
		$dest =  BASE_URL . ($user_priv == 'admin')?'admin.php':'';
		header('Location: ' . $dest);
		echo "<a href=$dest>Redirect</a>" .
			"<script>window.location=$dest</script>";
		exit();
	} else {
		$login_message = 'Failed login atempt';
	}
}

$login_message = '<h2 id="login_message">' . $login_message . '</h2>';
$login_form = '<div class="feature-box">' .
	$login_message . get_login_form() .
	'</div>';

$title = '<h1>Log in to XSS-exersize</h1>';

$body = get_body(
	$title .
	$login_form,
	array()
);

echo (get_document(get_default_head(), $body, array()));
