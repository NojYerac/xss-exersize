<?php

function get_filters_select() {
	$potential_filters = array(
		'/none/' =>	'none',
		'/<script>/' =>	'',
		'/</'	=>	'&lt;',
		'/>/'	=>	'&gt;',
		'/"/'	=>	'&quot;',
		"/'/"	=>	'&apos;',
		'/on[a-z]*=/' =>	'onxxx=',
		'/<.*>/' =>	'<xxx>',
		'/=/'	=>	'',
		'/[()]/' =>	'',
		'/;/'	=>	'&semi;',
		'/:/'	=>	'&colon;',
	);

	$cs_label = tagify(
		array(
			'tag'	=>	'label',
			'for'	=>	'case_sensitive',
			'style' =>	'color:black',
			'innerHTML' =>	'Case Sensitive: ',
		)
	);
	$cs_defs = array();
	if (!isset($_SESSION['case_sensitive']) || $_SESSION['case_sensitive']) {
		$cs_defs['checked'] = 'checked';
	}
	$cs_checkbox = $cs_label . inputify('checkbox', 'case_sensitive', $cs_defs);
	$filters_select = $cs_checkbox .
		'<br/>' . '<select name="filters[]" style="width:40%;height:240px;" multiple="multiple">';
	foreach ($potential_filters as $key => $value) {
		$option_defs = array(
			'tag' => 'option',
			'value' => htmlentities($key),
			'innerHTML' => htmlentities($key . " => " . $value),
		);
		if (isset($_SESSION['filters']) &&
			in_array($key, $_SESSION['filters'])) {
			$option_defs['selected'] = 'selected';
		}
		$filters_select .= tagify($option_defs);
	}
	$filters_select .= '</select><br/>' .
		inputify('submit', 'submit', array('value' => 'Set Filters'));
	return formify('POST', BASE_URL,  $filters_select, array());
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
