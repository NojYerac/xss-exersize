<?php
require_once('config.php');
require_once('comp.php');
require_once('filter.php');
session_start();

if (isset($_POST['case_sensitive'])) {
	$_SESSION['case_sensitive'] = 
		($_POST['case_sensitive'] == 'on');
}

if (isset($_POST['filters'])) {
	$_SESSION['filters'] = $_POST['filters'];
}

/*
if (!isset($_SESSION['blacklist'])) {
	$_SESSION['blacklist'] = '0';
}

if (isset($_POST['blacklist'])) {
	$bl_levels = array('0','1','2','3');
	$_SESSION['blacklist'] = 
		(in_array($_POST['blacklist'], $bl_levels)?$_POST['blacklist']:'0');
}

$bl_level = $_SESSION['blacklist'];

$spaces = array('script', 'attribute', 'text');

function script($value, $bl_level) {
	switch ($bl_level) {
	case '0':
		return $value;
		break;
	case '1':
		return preg_replace('/<script>/', '', $value);
		break;
	case '2':
		return preg_replace(
			array('/</', '/>/'),
			array('&lt;', '&gt;'),
			$value);
		break;
	case '3':
		return preg_replace('/[<>\"\'()]/', '', $value);
	default:
		return 'Filter level not implemented try 0, 1, 2 or 3.';
	}
}

function attribute($value, $bl_level) {
	switch ($bl_level) {
	case '0':
		return $value;
		break;
	case '1':
		return preg_replace('/<script>/', '', $value);
		break;
	case '2':
		return preg_replace(
			array('/</', '/>/'),
			array('&lt;', '&gt;'),
			$value);
		break;
	case '3':
		return preg_replace('/[<>\"\'()]/', '', $value);
	default:
		return 'Filter level not implemented try low, medium, or high.';
	}
}

function text($value, $bl_level) {
	switch ($bl_level) {
	case '0':
		return $value;
		break;
	case '1':
		return preg_replace('/<script>/', '', $value);
		break;
	case '2':
		return preg_replace(
			array('/</', '/>/'),
			array('&lt;', '&gt;'),
			$value);
		break;
	case '3':
		return preg_replace('/[<>\"\'()]/', '', $value);
	default:
		return 'Filter level not implemented try low, medium, or high.';
	}
}
 */

$spaces = array('script', 'attribute', 'text');
$xss = array();

foreach ($spaces as $space) {
//	$xss[$space] = ($_GET[$space]?$space($_GET[$space], $bl_level):'');
	$xss[$space] = ($_GET[$space]?filter_user_input($_GET[$space]):'');
};

$inputs = '<h2>Injectors</h2>' .
	inputify(
		'text',
		'script',
		array('label' => 'Script space reflection: ')
	) . '<br/>' .
	inputify(
		'text',
		'attribute',
		array('label' => 'Attribute space reflection: ')
	) . '<br/>' .
	inputify(
		'text',
		'text',
		array('label' => 'Text Space reflection: ')
	) . '<br/>' .
	inputify(
		'submit',
		'submit',
		array('value' => 'Reflect!')
	) . '<br/>';

$form = formify('GET', BASE_URL, $inputs, array());


$title = '<h1>Try to execute javascript.</h1>';

$instructions = "<h2>Instructions</h2><p>Don&apos;t you wish there were some instructions here?</p>";

$script_space = sprintf('<script>var MadeUp = \'%s\';</script>', $xss['script']);

$attribute_space = sprintf('<input type="text" value="%s"></input>', $xss['attribute']);

$text_space = sprintf('<p>Hello, %s!</p>', $xss['text']);
/*
$set_bl_level_inputs = '<h2>Set the filter level.</h2><table><tr>';

foreach (array('0','1','2','3') as $level) {
	$set_bl_level_inputs .= '<td>' .
		inputify('radio', 'bl_' . $level, array(
		'value' => $level,
		'name' => 'blacklist',
		'label' => $level . ': ',
	       	(($level == $bl_level)?'checked':'unchecked') => '')
		) . '</td>';
}

$set_bl_level_inputs .= '</tr></table><br/>' . inputify('submit', 'submit', array('value' => 'Set!')) . '<br/>';

$set_bl_form = formify('POST', BASE_URL, $set_bl_level_inputs, array());
 */

$filters_form = '<h2>Select the filters</h2>' .
	formify('POST', BASE_URL, get_filters_select(), array());

$attribution = "<!--This source code for this site can be found" .
       " at https://github.com/NojYerac/xss-exersize -->";

$body = get_body(
	$title .
	$instructions .
	$form .
	'<hr/>' .
	$filters_form .
//	$set_bl_form .
	'<hr/>' .
	$script_space .
	$attribute_space .
	$attribution .
	$text_space,
	array()
);

echo get_document(get_default_head(), $body, array());
