<?php 
require_once('db.php');
if (!function_exists('password_hash')) {
	require_once('password.php');
}

function get_priv($user_login) {
	$query =  "SELECT user_priv FROM users WHERE user_login='%s'";
	$result = db_query($query, array($user_login));
	$user_priv = db_all_results($result)[0]['user_priv'];
	return $user_priv;
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

function password_is_acceptable($password) {
	if (preg_match(
		'/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W).{8,255}$/',
		$password)) {
		return true;
	} else {
		return false;
	}
}

function add_user($user_login, $user_pass, $verify_pass, $admin=false) {
	if ($user_pass == $verify_pass && password_is_acceptable($user_pass)) {
		$hashed_pass = password_hash($user_pass, 1);
		$query = "INSERT into users(user_login, user_pass) VALUES ('%s', '$hashed_pass')";
		db_query($query, array($user_login));
		return true;
	} else {
		return false;
	}
}



?>

