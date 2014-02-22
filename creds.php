<?php 
if (!function_exists('password_hash')) {
	require_once('password.php');
}

function check_creds($login, $pass) {
	$query = "SELECT user_pass FROM users WHERE user_login = '%s'";
	$result = db_query($query, array($login));
	try {
		$pass_result = db_all_results($result)[0]['user_pass'];
	} catch (Exception $e) {
		$pass_result = '';
	}
	if (password_verify($pass, $pass_result)) {
		return true;
	} else { 
		return false;
	}
}

function reset_creds($login, $old_pass, $new_pass) {
	if (!check_creds($login, $old_pass)) {
		return false;
	} else {
		set_creds($login, $new_pass);
		return true;
	}
}

function set_creds($login, $pass) {
	$hashed_pass = password_hash($pass, 1);
	$query = "UPDATE users SET user_pass = '$hashed_pass' WHERE user_login = '%s'";
	db_query($query, array($login));
	return true;
}

?>

