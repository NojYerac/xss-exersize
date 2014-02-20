<?php
$potential_filters = array(
	'/<script>/' =>	'',
	'/</'	=>	'&lt;',
	'/>/'	=>	'&gt;',
	'/"/'	=>	'&quot;',
	"/'/"	=>	'&apos;',
	'/on[a-z]=/' =>	'',
	'/<.*>/' =>	'',
	'/=/'	=>	'',
	'/[()]/' =>	'',
	'/;/'	=>	'&semi;',
	'/:/'	=>	'&colon;',

);


function get_filters_select() {
	$cs_defs = array(
		'label' => 'Case Sensitive: ',
	);
	if (!isset($_SESSION['case_sensitive']) || $_SESSION['case_sensitive']) {
		$cs_defs['checked'] = 'checked';
	}
	$filters_select = inputify('checkbox', 'case_sensitive', $cs_defs) .
		'<select name="filters" multiple="multiple">';
	foreach ($potential_filters as $key => $value) {
		$option_defs = array(
			'tag' => 'option',
			'value' => $key,
			'innerHTML' => htmlentities($key . " => " . $value),
		);
		if (isset($_SESSION['filters']) &&
			array_key_exists($key, $_SESSION['filters'])) {
			$option_defs['checked'] = 'checked';
		}
		$filters_select .= tagify($option_defs);
	}
	$filters_select .= '</select>';
	return $filters_select;
}

function filter_user_input($input) {
	$regexes = array();
	$replaces = array();
	foreach ($_SESSION['filters'] as $regex) {
		if (!$_SESSION['case_sensitive']) {
			$regexes[] = $regex . 'i';
		} else {
			$regexes[] = $regex;
		}
		$replaces[] = $potential_filters[$regex];
	}
	return preg_replace($regexes, $replaces, $input);
}

?>
