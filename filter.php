<?php
require_once('db.php');


function get_filters() {
	/* FIXME:
	 * dumb list for now, but should be
	 * stored in a database, editable
	 * from * the admin panel.
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
	);*/
	$potential_filters = array();
	$result = db_query('SELECT regex, replacement FROM filters', array());
	$rows = db_all_results($result);
	foreach ($rows as $row) {
		$potential_filters[$row['regex']] = $row['replacement'];
	}
	return $potential_filters;

}

function store_filter($title, $regex, $replacement, $comment) {
	$query = 'INSERT into filters(title, regex, replacement, comment) VALUE("%s", "%s", "%s", "%s");';
	$result = db_query($query, array($title, $regex, $replacement, $comment));
}

/* used once  to export dumb list to database
function store_all_filters() {
	$potential_filters = get_filters();
	foreach ($potential_filters as $regex => $replacement) {
		store_filter($regex, $regex, $replacement, 'No comment');
	}
}
 */

function get_filters_select() {
	$potential_filters = get_filters();
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
	$potential_filters = get_filters();
	$regexes = array();
	$replaces = array();
	if (isset($_SESSION['filters'])) {
		foreach ($_SESSION['filters'] as $regex) {
			if (!$_SESSION['case_sensitive']) {
				$regexes[] = $regex . 'i';
			} else {
				$regexes[] = $regex;
			}
			$replaces[] = $potential_filters[$regex];
		}
		return preg_replace($regexes, $replaces, $input);
	} else {
		return $input;
	}
}

?>
