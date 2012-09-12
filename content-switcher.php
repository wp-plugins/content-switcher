<?php
/*
Plugin Name: Content Switcher
Plugin URI: http://www.kleor-editions.com/content-switcher
Description: Allows you to easily display a random number, a random or variable content on your website, and to optimize your website with Google Optimizer and Google Analytics.
Version: 3.1
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


define('CONTENT_SWITCHER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('CONTENT_SWITCHER_VERSION', $plugin_data['Version']);

if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

$content_switcher_options = get_option('content_switcher');
if (((is_multisite()) || ($content_switcher_options)) && ((!isset($content_switcher_options['version']))
 || ($content_switcher_options['version'] != CONTENT_SWITCHER_VERSION))) {
include_once dirname(__FILE__).'/admin.php'; install_content_switcher(); }


function analytics_tracking_js() {
$analytics_tracking = false;
if (current_user_can('manage_options')) { if (content_switcher_data('administrator_tracked') == 'yes') { $analytics_tracking = true; } }
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

if (content_switcher_data('javascript_enabled') == 'yes') {
if (content_switcher_data('back_office_tracked') == 'yes') { add_action('admin_footer', 'analytics_tracking_js'); }
if (content_switcher_data('front_office_tracked') == 'yes') { add_action('wp_footer', 'analytics_tracking_js'); } }


function content_switcher_data($atts) {
global $content_switcher_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; }
else {
$field = (isset($atts[0]) ? $atts[0] : '');
$default = (isset($atts['default']) ? $atts['default'] : '');
$filter = (isset($atts['filter']) ? $atts['filter'] : ''); }
$field = str_replace('-', '_', content_switcher_format_nice_name($field));
if ($field == '') { $field = 'version'; }
$data = (isset($content_switcher_options[$field]) ? $content_switcher_options[$field] : '');
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = content_switcher_filter_data($filter, $data);
return $data; }


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
load_plugin_textdomain('content-switcher', false, 'content-switcher/languages');
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


function optimizer_control_js() {
global $post;
if ((isset($post)) && (is_object($post))) {
$optimizer = do_shortcode(get_post_meta($post->ID, 'optimizer', true));
if (substr($optimizer, 0, 1) != '/') { $optimizer = '/'.$optimizer; }
$optimizer = explode('/', $optimizer);
if ((isset($optimizer[2])) && ($optimizer[2] == 'test')) { ?>
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
<?php } } }

if (content_switcher_data('javascript_enabled') == 'yes') { add_action('wp_head', 'optimizer_control_js'); }


function optimizer_tracking_js() {
global $post;
if ((isset($post)) && (is_object($post))) {
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
<?php } } }

if (content_switcher_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'optimizer_tracking_js'); }


for ($i = 0; $i < 4; $i++) {
foreach (array('random', 'variable') as $string) {
add_shortcode($string.'-content'.($i == 0 ? '' : $i), create_function('$atts, $content', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.$string.'_content($atts, $content);')); } }
add_shortcode('optimizer-content', create_function('$atts, $content', 'include_once dirname(__FILE__)."/shortcodes.php"; return optimizer_content($atts, $content);'));
foreach (array('random-number', 'variable-string') as $tag) {
add_shortcode($tag, create_function('$atts', 'include_once dirname(__FILE__)."/shortcodes.php"; return '.str_replace('-', '_', $tag).'($atts);')); }
add_shortcode('content-switcher', 'content_switcher_data');


foreach (array(
'get_the_excerpt',
'get_the_title',
'single_post_title',
'the_excerpt',
'the_excerpt_rss',
'the_title',
'the_title_attribute',
'the_title_rss',
'widget_text',
'widget_title') as $function) { add_filter($function, 'do_shortcode'); }