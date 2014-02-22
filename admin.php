<?php
require_once('comp.php');

session_start();
if (!isset($_SESSION['user_priv']) || $_SESSION['user_priv'] != 'admin') {
	http_response_code(401);
	echo 'Not Authorized';
	exit();
}
echo 'Admin Page!';
?>

