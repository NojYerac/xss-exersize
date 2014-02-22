<?php
function db_connect() {
	switch (DB_TYPE) {
		case 'MySQL':
			$db_server = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if (!$db_server) die("Unable to connect to MySQL server: " . mysqli_error());
			break;
		case 'PgSQL':
			die("Not implemented!");
			break;
		default:
			die("Invalid db_type specifed in config.php");
			break;
	}
	return $db_server;
}

function db_disconnect($db_server) {
	switch (DB_TYPE) {
		case 'MySQL':
			mysqli_close($db_server);
			break;
		case 'PgSQL':
			die("Not implemented!");
			break;
		default:
			die("Invalid db_type specifed in config.php");
			break;
	}
}

function db_query($query_format, $user_inputs) {
	$db_server = db_connect();
	$sanitized_inputs = array();
	foreach ($user_inputs as $input) {
		$input = mysqli_real_escape_string($db_server, $input);
		array_push($sanitized_inputs, $input);
	}
	switch (DB_TYPE) {
		case 'MySQL':
			$result = mysqli_query($db_server, vsprintf($query_format, $sanitized_inputs));
			if (!$result) die("Unable to query database: " . mysqli_error($db_server));
			db_disconnect($db_server);
			break;
		case 'PgSQL':
			die("Not implemented!");
			break;
		default:
			die("Invalid db_type specifed in config.php");
			break;
	}
	return $result;
}

function db_all_results($result) {
	switch(DB_TYPE) {
	case 'MySQL':
		$return_value = array();
		for ($i=0; $i < mysqli_num_rows($result); $i++) {
			array_push($return_value, mysqli_fetch_assoc($result));
		}
		break;
	case 'PgSQL':
		die("Not implemented!");
		break;
	default:
		die("Invalid db_type specifed in config.php");
			break;
	}
	return $return_value;
}
