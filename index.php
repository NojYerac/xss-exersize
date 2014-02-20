<?php
require_once('config.php');
require_once('comp.php');
require_once('filter.php');
session_start();
if (isset($_POST['filters']) || isset($_POST['case_sensitive'])) {
	$_SESSION['filters'] = $_POST['filters'];
	$_SESSION['case_sensitive'] = isset($_POST['case_sensitive']);
} elseif (!isset($_SESSION['filters']) || !isset($_SESSION['case_sensitive'])) {
	$_SESSION['filters'] = array();
	$_SESSION['case_sensitive'] = true;
}
/* uncoment to see Session variables printed to the page
foreach ($_SESSION as $key => $value) {
	if (is_array($value)) {
		foreach ($value as $v) {
			echo htmlentities("$key => $v");
		}
	} else {
		echo htmlentities("$key => $value");
	}
}*/

$spaces = array('script', 'attribute', 'text');
$xss = array();

foreach ($spaces as $space) {
	$xss[$space] = (isset($_GET[$space])?filter_user_input($_GET[$space]):'');
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

$instructions = "<h2>Instructions</h2><p>Inject the javascript function call " .
	"&quot;youWin()&quot to see a neat picture.</p>";

$script_space = sprintf('<script>var MadeUp = \'%s\';</script>', $xss['script']);

$attribute_space = sprintf('<input type="text" value="%s"></input>', $xss['attribute']);

$text_space = sprintf('<p>Hello, %s!</p>', $xss['text']);

$filters_form = '<h2>Select the filters</h2>' .	get_filters_select();

$attribution = "<!--This source code for this site can be found" .
       " at https://github.com/NojYerac/xss-exersize -->";

$tah_dah = '<div id="tah-dah" class="hidden">' .
	'<img src="' . BASE_URL . '/tah-dah/' . rand(1, 5) . '.jpeg" />' .
	'<br/>' .
	'<button onclick="resetChalenge()">Reset</button>' .
	'</div>';

$body = get_body(
	$title .
	'<div class="feature-box">' . 
	$tah_dah .
	'<div id="chalenge">' . 
	$instructions .
	$form .
	'</div></div>' .
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
