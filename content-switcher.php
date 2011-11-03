<?php
/*
Plugin Name: Content Switcher
Plugin URI: http://www.kleor-editions.com/content-switcher
Description: Allows you to easily display a random number, a random or variable content on your website, and to optimize your website with Google Optimizer and Google Analytics.
Version: 1.8.4
Author: Kleor
Author URI: http://www.kleor-editions.com
Text Domain: content-switcher
License: GPL2
*/

/* 
Copyright 2010 Kleor Editions (http://www.kleor-editions.com)

This program is a free software. You can redistribute it and/or 
modify it under the terms of the GNU General Public License as 
published by the Free Software Foundation, either version 2 of 
the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, 
but without any warranty, without even the implied warranty of 
merchantability or fitness for a particular purpose. See the 
GNU General Public License for more details.
*/


load_plugin_textdomain('content-switcher', false, 'content-switcher/languages');

define('CONTENT_SWITCHER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('CONTENT_SWITCHER_VERSION', $plugin_data['Version']);

if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

$content_switcher_options = get_option('content_switcher');
if (($content_switcher_options) && ($content_switcher_options['version'] != CONTENT_SWITCHER_VERSION)) {
include_once dirname(__FILE__).'/admin.php';
install_content_switcher(); }


function analytics_tracking_js() {
if (current_user_can('activate_plugins')) { if (content_switcher_data('admin_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('moderate_comments')) { if (content_switcher_data('editor_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('publish_posts')) { if (content_switcher_data('author_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('edit_posts')) { if (content_switcher_data('contributor_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('read')) { if (content_switcher_data('subscriber_tracked') == 'yes') { $analytics_tracking = true; } }
else { if (content_switcher_data('visitor_tracked') == 'yes') { $analytics_tracking = true; } }
if ($analytics_tracking) { ?>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?php echo content_switcher_data('analytics_tracking_id'); ?>']);
_gaq.push(['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<?php } }

if (content_switcher_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'analytics_tracking_js'); }


function content_switcher_data($atts) {
global $content_switcher_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; }
$field = str_replace('-', '_', content_switcher_format_nice_name($field));
if ($field == '') { $field = 'version'; }
$data = $content_switcher_options[$field];
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = content_switcher_filter_data($filter, $data);
return $data; }

add_shortcode('content-switcher', 'content_switcher_data');


function content_switcher_filter_data($filter, $data) {
if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', $filter), 0, PREG_SPLIT_NO_EMPTY); }
if (is_array($filter)) { foreach ($filter as $function) { $data = content_switcher_string_map($function, $data); } }
return $data; }


function content_switcher_format_nice_name($string) {
$string = content_switcher_strip_accents(strtolower(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
return $string; }


function content_switcher_i18n($string) {
$strings = array(
__('no', 'content-switcher'),
__('yes', 'content-switcher'));
return __(__($string), 'content-switcher'); }


function content_switcher_string_map($function, $string) {
if (!function_exists($function)) { $function = 'content_switcher_'.$function; }
if (function_exists($function)) { $array = array_map($function, array($string)); $string = $array[0]; }
return $string; }


function content_switcher_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }


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

add_shortcode('optimizer-content', 'optimizer_content');


function optimizer_control_js() {
global $post;
$optimizer = do_shortcode(get_post_meta($post->ID, 'optimizer', true));
if (substr($optimizer, 0, 1) != '/') { $optimizer = '/'.$optimizer; }
$optimizer = explode('/', $optimizer);
if ($optimizer[2] == 'test') { ?>
<script type="text/javascript">
function utmx_section(){}function utmx(){}
(function(){var k='<?php echo $optimizer[1]; ?>',d=document,l=d.location,c=d.cookie;function f(n){
if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);return escape(c.substring(i+n.
length+1,j<0?c.length:j))}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;
d.write('<sc'+'ript src="'+
'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'
+'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='
+new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
'" type="text/javascript" charset="utf-8"></sc'+'ript>')})();
</script>
<?php } }

if (content_switcher_data('javascript_enabled') == 'yes') { add_action('wp_head', 'optimizer_control_js'); }


function optimizer_tracking_js() {
global $post;
$optimizer = do_shortcode(get_post_meta($post->ID, 'optimizer', true));
if (substr($optimizer, 0, 1) != '/') { $optimizer = '/'.$optimizer; }
$type = substr($optimizer, -4);
if (($type == 'test') || ($type == 'goal')) { ?>
<script type="text/javascript">
if(typeof(_gat)!='object')document.write('<sc'+'ript src="http'+
(document.location.protocol=='https:'?'s://ssl':'://www')+
'.google-analytics.com/ga.js"></sc'+'ript>');
try {
var gwoTracker=_gat._getTracker("<?php echo content_switcher_data('optimizer_tracking_id'); ?>");
gwoTracker._trackPageview("<?php echo $optimizer; ?>");
}catch(err){}
</script>
<?php } }

if (content_switcher_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'optimizer_tracking_js'); }


function random_content($atts, $content) {
extract(shortcode_atts(array('filter' => '', 'string' => ''), $atts));

if ($string != '') {
$string = str_replace('(', '[', $string);
$string = str_replace(')', ']', $string);
$string = do_shortcode($string); }

$content = explode('[other]', do_shortcode($content));
$m = count($content) - 1;
$n = mt_rand(0, $m);
$content[$n] = str_replace('[string]', $string, $content[$n]);

$content[$n] = content_switcher_filter_data($filter, $content[$n]);
return $content[$n]; }

add_shortcode('random-content', 'random_content');
add_shortcode('random-content0', 'random_content');
add_shortcode('random-content1', 'random_content');
add_shortcode('random-content2', 'random_content');
add_shortcode('random-content3', 'random_content');
add_shortcode('random-content4', 'random_content');
add_shortcode('random-content5', 'random_content');
add_shortcode('random-content6', 'random_content');
add_shortcode('random-content7', 'random_content');
add_shortcode('random-content8', 'random_content');
add_shortcode('random-content9', 'random_content');
add_shortcode('random-content10', 'random_content');


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

add_shortcode('random-number', 'random_number');


function variable_content($atts, $content) {
extract(shortcode_atts(array('filter' => '', 'name' => 'content', 'string' => '', 'type' => 'get', 'values' => ''), $atts));

if ($string != '') {
$string = str_replace('(', '[', $string);
$string = str_replace(')', ']', $string);
$string = do_shortcode($string); }

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

$content[$n] = str_replace('[string]', $string, $content[$n]);
$content[$n] = content_switcher_filter_data($filter, $content[$n]);
return $content[$n]; }

add_shortcode('variable-content', 'variable_content');
add_shortcode('variable-content0', 'variable_content');
add_shortcode('variable-content1', 'variable_content');
add_shortcode('variable-content2', 'variable_content');
add_shortcode('variable-content3', 'variable_content');
add_shortcode('variable-content4', 'variable_content');
add_shortcode('variable-content5', 'variable_content');
add_shortcode('variable-content6', 'variable_content');
add_shortcode('variable-content7', 'variable_content');
add_shortcode('variable-content8', 'variable_content');
add_shortcode('variable-content9', 'variable_content');
add_shortcode('variable-content10', 'variable_content');


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

add_shortcode('variable-string', 'variable_string');


add_filter('get_the_excerpt', 'do_shortcode');
add_filter('get_the_title', 'do_shortcode');
add_filter('single_post_title', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');
add_filter('the_excerpt_rss', 'do_shortcode');
add_filter('the_title', 'do_shortcode');
add_filter('the_title_attribute', 'do_shortcode');
add_filter('the_title_rss', 'do_shortcode');
add_filter('widget_text', 'do_shortcode');