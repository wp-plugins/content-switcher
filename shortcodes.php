<?php function content_switcher_string($atts) {
extract(shortcode_atts(array('default' => '', 'filter' => ''), $atts));
$string = $_GET['content_switcher_string'];
if ($string == '') { $string = $default; }
$string = content_switcher_filter_data($filter, $string);
return $string; }


function optimizer_content($atts, $content) {
$content = do_shortcode($content);
if (content_switcher_data('javascript_enabled') == 'yes') {
global $post;
$optimizer = do_shortcode(get_post_meta($post->ID, 'optimizer', true));
if (substr($optimizer, 0, 1) != '/') { $optimizer = '/'.$optimizer; }
$optimizer = explode('/', $optimizer);
if ($optimizer[2] == 'test') {
extract(shortcode_atts(array('name' => 'Content'), $atts));
$content = '<script type="text/javascript">utmx_section("'.$name.'")</script>'
.$content.'</noscript>'; } }
return $content; }


function random_content($atts, $content) {
extract(shortcode_atts(array('filter' => '', 'string' => ''), $atts));
if ($string != '') {
$string = str_replace('(', '[', $string);
$string = str_replace(')', ']', $string);
$string = do_shortcode($string); }
if (isset($_GET['content_switcher_string'])) { $original_content_switcher_string = $_GET['content_switcher_string']; }
$_GET['content_switcher_string'] = $string;
$content = explode('[other]', do_shortcode($content));
$m = count($content) - 1;
$n = mt_rand(0, $m);
add_shortcode('string', 'content_switcher_string');
$content[$n] = content_switcher_filter_data($filter, do_shortcode($content[$n]));
if (isset($original_content_switcher_string)) { $_GET['content_switcher_string'] = $original_content_switcher_string; }
remove_shortcode('string');
return $content[$n]; }


function random_number($atts) {
extract(shortcode_atts(array('digits' => 0, 'filter' => '', 'max' => 0, 'min' => 0, 'set' => ''), $atts));
if ($set == '') {
$min = floor($min); $max = floor($max);
if ($min <= $max) { $n = mt_rand($min, $max); } else { $n = mt_rand($max, $min); } }
else { $set = explode('/', $set); $n = $set[mt_rand(0, count($set) - 1)]; }
if ($n >= 0) { $symbol = ''; } else { $symbol = '-'; $n = -$n; }
$number = (string) $n;
$length = strlen($number);
$digits = floor($digits);
while ($length < $digits) { $number = '0'.$number; $length = $length + 1; }
$number = $symbol.$number;
$number = content_switcher_filter_data($filter, $number);
return $number; }


function variable_content($atts, $content) {
extract(shortcode_atts(array('filter' => '', 'name' => 'content', 'string' => '', 'type' => 'get', 'values' => ''), $atts));
if ($string != '') {
$string = str_replace('(', '[', $string);
$string = str_replace(')', ']', $string);
$string = do_shortcode($string); }
if (isset($_GET['content_switcher_string'])) { $original_content_switcher_string = $_GET['content_switcher_string']; }
$_GET['content_switcher_string'] = $string;
$content = explode('[other]', do_shortcode($content));
$m = count($content);

$type = strtolower($type); switch ($type) {
case 'cookie': $TYPE = $_COOKIE; break;
case 'env': $TYPE = $_ENV; break;
case 'post': $TYPE = $_POST; break;
case 'server': $TYPE = $_SERVER; break;
case 'session': $TYPE = $_SESSION; break;
default: $TYPE = $_GET; }

if (isset($TYPE[$name])) {
	if ($m == 1) { $n = 0; $content[0] = utf8_encode(htmlspecialchars($TYPE[$name])); }
	else {
		if ($values == '') { $n = (floor($TYPE[$name]))%$m; }
		else {
		$values = explode('/', $values);
		$v = count($values); $n = 0;
		for ($i = 0; $i < $v; $i++) { if ($TYPE[$name] == $values[$i]) { $n = $i; } }
		}
	}
}	
else { $n = 0; }

add_shortcode('string', 'content_switcher_string');
$content[$n] = content_switcher_filter_data($filter, do_shortcode($content[$n]));
if (isset($original_content_switcher_string)) { $_GET['content_switcher_string'] = $original_content_switcher_string; }
remove_shortcode('string');
return $content[$n]; }


function variable_string($atts) {
extract(shortcode_atts(array('default' => '', 'filter' => '', 'name' => 'content', 'type' => 'get'), $atts));

$type = strtolower($type); switch ($type) {
case 'cookie': $TYPE = $_COOKIE; break;
case 'env': $TYPE = $_ENV; break;
case 'post': $TYPE = $_POST; break;
case 'server': $TYPE = $_SERVER; break;
case 'session': $TYPE = $_SESSION; break;
default: $TYPE = $_GET; }

if (isset($TYPE[$name])) { $string = utf8_encode(htmlspecialchars($TYPE[$name])); }
if ($string == '') { $string = $default; }
$string = content_switcher_filter_data($filter, $string);
return $string; }