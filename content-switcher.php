<?php
/*
Plugin Name: Content Switcher
Plugin URI: http://www.kleor-editions.com/content-switcher
Description: Easily display a random number, a random or variable content on your website. Optimize your website with Google Optimizer and add the Google Analytics code to each page of your website.
Version: 1.6.1
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


load_plugin_textdomain('content-switcher', 'wp-content/plugins/content-switcher/languages', 'content-switcher/languages');

$wpurl = get_bloginfo('wpurl');
if ((substr($wpurl, -1) == '/')) { $wpurl = substr($wpurl, 0, -1); }
define('CONTENT_SWITCHER_URL', $wpurl.'/wp-content/plugins/content-switcher/');

$content_switcher_options = get_option('content_switcher');
define('CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_ID', $content_switcher_options['analytics_tracking_id']);
define('CONTENT_SWITCHER_OPTIONS_OPTIMIZER_TRACKING_ID', $content_switcher_options['optimizer_tracking_id']);
define('CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_ADMIN', $content_switcher_options['analytics_tracking_admin']);
define('CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_EDITOR', $content_switcher_options['analytics_tracking_editor']);
define('CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_AUTHOR', $content_switcher_options['analytics_tracking_author']);
define('CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_CONTRIBUTOR', $content_switcher_options['analytics_tracking_contributor']);
define('CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_SUBSCRIBER', $content_switcher_options['analytics_tracking_subscriber']);
define('CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_VISITOR', $content_switcher_options['analytics_tracking_visitor']);
define('CONTENT_SWITCHER_OPTIONS_JAVASCRIPT_ENABLED', $content_switcher_options['javascript_enabled']);


function analytics_tracking_js() {
if (current_user_can('activate_plugins')) { if (CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_ADMIN == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('moderate_comments')) { if (CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_EDITOR == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('publish_posts')) { if (CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_AUTHOR == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('edit_posts')) { if (CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_CONTRIBUTOR == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('read')) { if (CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_SUBSCRIBER == 'yes') { $analytics_tracking = true; } }
else { if (CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_VISITOR == 'yes') { $analytics_tracking = true; } }
if ($analytics_tracking) { ?>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?php echo CONTENT_SWITCHER_OPTIONS_ANALYTICS_TRACKING_ID; ?>']);
_gaq.push(['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<?php } }


function optimizer_content($atts, $content) {
$content = do_shortcode($content);

if (CONTENT_SWITCHER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') {
global $post;
$optimizer = get_post_meta($post->ID, 'optimizer', true);
$optimizer = explode('/', $optimizer);
if ($optimizer[2] == 'test') {
extract(shortcode_atts(array('name' => 'Content'), $atts));
$content = '<script type="text/javascript">utmx_section("'.$name.'")</script>'
.$content.'</noscript>'; } }

return $content; }


function optimizer_control_js() {
global $post;
$optimizer = get_post_meta($post->ID, 'optimizer', true);
$optimizer = explode('/', $optimizer);
if ($optimizer[2] == 'test') { ?>
<script type="text/javascript">
function utmx_section(){}function utmx(){}
(function(){var k='<?php echo $optimizer[1]; ?>',d=document,l=d.location,c=d.cookie;function f(n){
if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);return c.substring(i+n.
length+1,j<0?c.length:j)}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;
d.write('<sc'+'ript src="'+
'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'
+'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='
+new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
'" type="text/javascript" charset="utf-8"></sc'+'ript>')})();
</script>
<?php } }


function optimizer_tracking_js() {
global $post;
$optimizer = get_post_meta($post->ID, 'optimizer', true);
$type = substr($optimizer, -4);
if (($type == 'test') || ($type == 'goal')) { ?>
<script type="text/javascript">
if(typeof(_gat)!='object')document.write('<sc'+'ript src="http'+
(document.location.protocol=='https:'?'s://ssl':'://www')+
'.google-analytics.com/ga.js"></sc'+'ript>');
try {
var gwoTracker=_gat._getTracker("<?php echo CONTENT_SWITCHER_OPTIONS_OPTIMIZER_TRACKING_ID; ?>");
gwoTracker._trackPageview("<?php echo $optimizer; ?>");
}catch(err){};
</script>
<?php } }


function random_content($atts, $content) {
extract(shortcode_atts(array('string' => ''), $atts));

if ($string != '') {
$string = str_replace('(', '[', $string);
$string = str_replace(')', ']', $string);
$string = do_shortcode($string); }

$content = explode('[other]', $content);
$m = count($content) - 1;
$n = mt_rand(0, $m);
$content[$n] = str_replace('[string]', $string, $content[$n]);
$content[$n] = do_shortcode($content[$n]);

return $content[$n]; }


function random_number($atts) {
extract(shortcode_atts(array(set => '', 'min' => 0, 'max' => 0, 'digits' => 0), $atts));

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

return $number; }


function variable_content($atts, $content) {
extract(shortcode_atts(array('name' => 'content', 'string' => '', 'type' => 'get', 'values' => ''), $atts));

if ($string != '') {
$string = str_replace('(', '[', $string);
$string = str_replace(')', ']', $string);
$string = do_shortcode($string); }

$content = explode('[other]', $content);
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
		$v = count($values) - 1;
		for ($i = 0; $i <= $v; $i++) { { if ($TYPE[$name] == $values[$i]) { $n = $i; } } }
		}
	}
}	
else { $n = 0; }

$content[$n] = str_replace('[string]', $string, $content[$n]);
$content[$n] = do_shortcode($content[$n]);

return $content[$n]; }


function variable_string($atts) {
extract(shortcode_atts(array('name' => 'content', 'type' => 'get'), $atts));

$type = strtolower($type); switch ($type) {
case 'cookie': $TYPE = $_COOKIE; break;
case 'env': $TYPE = $_ENV; break;
case 'post': $TYPE = $_POST; break;
case 'server': $TYPE = $_SERVER; break;
case 'session': $TYPE = $_SESSION; break;
default: $TYPE = $_GET; }

if (isset($TYPE[$name])) { $string = utf8_encode(htmlspecialchars($TYPE[$name])); }

return $string; }


function content_switcher_options_page() {
if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ($_REQUEST['submit'] ==  __('Save Changes')) {
$analytics_tracking_id = mysql_real_escape_string($_REQUEST['analytics-tracking-id']);
$optimizer_tracking_id = mysql_real_escape_string($_REQUEST['optimizer-tracking-id']);
if ($_REQUEST['analytics-tracking-admin'] == 'yes') { $analytics_tracking_admin = 'yes'; } else { $analytics_tracking_admin = 'no'; }
if ($_REQUEST['analytics-tracking-editor'] == 'yes') { $analytics_tracking_editor = 'yes'; } else { $analytics_tracking_editor = 'no'; }
if ($_REQUEST['analytics-tracking-author'] == 'yes') { $analytics_tracking_author = 'yes'; } else { $analytics_tracking_author = 'no'; }
if ($_REQUEST['analytics-tracking-contributor'] == 'yes') { $analytics_tracking_contributor = 'yes'; } else { $analytics_tracking_contributor = 'no'; }
if ($_REQUEST['analytics-tracking-subscriber'] == 'yes') { $analytics_tracking_subscriber = 'yes'; } else { $analytics_tracking_subscriber = 'no'; }
if ($_REQUEST['analytics-tracking-visitor'] == 'yes') { $analytics_tracking_visitor = 'yes'; } else { $analytics_tracking_visitor = 'no'; }
if ($_REQUEST['javascript-enabled'] == 'yes') { $javascript_enabled = 'yes'; } else { $javascript_enabled = 'no'; }
$content_switcher_options = array(
'analytics_tracking_id' => $analytics_tracking_id,
'optimizer_tracking_id' => $optimizer_tracking_id,
'analytics_tracking_admin' => $analytics_tracking_admin,
'analytics_tracking_editor' => $analytics_tracking_editor,
'analytics_tracking_author' => $analytics_tracking_author,
'analytics_tracking_contributor' => $analytics_tracking_contributor,
'analytics_tracking_subscriber' => $analytics_tracking_subscriber,
'analytics_tracking_visitor' => $analytics_tracking_visitor,
'javascript_enabled' => $javascript_enabled);
update_option('content_switcher', $content_switcher_options); }

$content_switcher_options = get_option('content_switcher');
if (isset($_POST['updated']) && $_POST['updated'] == 'true') {
$updated_message = '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; }

$content = '
<div class="wrap">
<h2>Content Switcher</h2>'
.$updated_message.'
<p>'.__('Complete Documentation', 'content-switcher').':</p>
<ul style="margin: 1.5em">
<li><a href="http://www.kleor-editions.com/content-switcher/en">'.__('in English', 'content-switcher').'</a></li>
<li><a href="http://www.kleor-editions.com/content-switcher">'.__('in French', 'content-switcher').'</a></li>
</ul>
<h3>'.__('Options', 'content-switcher').'</h3>
<form method="post" action="">
<p><input type="hidden" name="updated" value="true" />
<label for="analytics-tracking-id">'.__('Google Analytics Account Tracking ID', 'content-switcher').':</label> <input type="text" name="analytics-tracking-id" id="analytics-tracking-id" value="'.$content_switcher_options['analytics_tracking_id'].'" size="16" />
'.__('<a href="http://www.kleor-editions.com/content-switcher/en/#part5.2">More informations</a>', 'content-switcher').'<br />
<label for="optimizer-tracking-id">'.__('Google Optimizer Account Tracking ID', 'content-switcher').':</label> <input type="text" name="optimizer-tracking-id" id="optimizer-tracking-id" value="'.$content_switcher_options['optimizer_tracking_id'].'" size="16" />
'.__('<a href="http://www.kleor-editions.com/content-switcher/en/#part5.2">More informations</a>', 'content-switcher').'</p>
<p>'.__('Track with Google Analytics the', 'content-switcher').':</p>
<p><input type="checkbox" name="analytics-tracking-admin" id="analytics-tracking-admin" value="yes" '.($content_switcher_options['analytics_tracking_admin'] == 'yes' ? 'checked="checked"' : '' ).' /> <label for="analytics-tracking-admin">'.__('Administrators', 'content-switcher').'</label><br />
<input type="checkbox" name="analytics-tracking-editor" id="analytics-tracking-editor" value="yes" '.($content_switcher_options['analytics_tracking_editor'] == 'yes' ? 'checked="checked"' : '' ).' /> <label for="analytics-tracking-editor">'.__('Editors', 'content-switcher').'</label><br />
<input type="checkbox" name="analytics-tracking-author" id="analytics-tracking-author" value="yes" '.($content_switcher_options['analytics_tracking_author'] == 'yes' ? 'checked="checked"' : '' ).' /> <label for="analytics-tracking-author">'.__('Authors', 'content-switcher').'</label><br />
<input type="checkbox" name="analytics-tracking-contributor" id="analytics-tracking-contributor" value="yes" '.($content_switcher_options['analytics_tracking_contributor'] == 'yes' ? 'checked="checked"' : '' ).' /> <label for="analytics-tracking-contributor">'.__('Contributors', 'content-switcher').'</label><br />
<input type="checkbox" name="analytics-tracking-subscriber" id="analytics-tracking-subscriber" value="yes" '.($content_switcher_options['analytics_tracking_subscriber'] == 'yes' ? 'checked="checked"' : '' ).' /> <label for="analytics-tracking-subscriber">'.__('Subscribers', 'content-switcher').'</label><br />
<input type="checkbox" name="analytics-tracking-visitor" id="analytics-tracking-visitor" value="yes" '.($content_switcher_options['analytics_tracking_visitor'] == 'yes' ? 'checked="checked"' : '' ).' /> <label for="analytics-tracking-visitor">'.__('Visitors without any role', 'content-switcher').'</label><br />
<em>('.__('you can check several boxes', 'content-switcher').')</em></p>
<p><input type="checkbox" name="javascript-enabled" id="javascript-enabled" value="yes" '.($content_switcher_options['javascript_enabled'] == 'yes' ? 'checked="checked"' : '' ).' /> <label for="javascript-enabled">'.__('Add JavaScript code', 'content-switcher').'</label><br />
<em>'.__('If you uncheck this box, Content Switcher will never add any JavaScript code to the pages of your website, but Google Analytics and Google Optimizer will not work.', 'content-switcher').' '.__('<a href="http://www.kleor-editions.com/content-switcher/en/#part6.1">More informations</a>', 'content-switcher').'</em></p>
<p><input class="button" name="submit" value="'.__('Save Changes').'" type="submit" /></p>
</form>
</div>';
	
echo $content; }


function content_switcher_admin_menu() {
add_options_page('Content Switcher', 'Content Switcher', 'manage_options', 'content-switcher', 'content_switcher_options_page'); }


add_filter('get_the_excerpt', 'do_shortcode');
add_filter('get_the_title', 'do_shortcode');
add_filter('single_post_title', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');
add_filter('the_excerpt_rss', 'do_shortcode');
add_filter('the_title', 'do_shortcode');
add_filter('the_title_attribute', 'do_shortcode');
add_filter('the_title_rss', 'do_shortcode');
add_filter('widget_text', 'do_shortcode');
add_shortcode('optimizer-content', 'optimizer_content');
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
add_shortcode('random-number', 'random_number');
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
add_shortcode('variable-string', 'variable_string');
add_action('admin_menu', 'content_switcher_admin_menu');

add_option('content_switcher', array(
'analytics_tracking_id' => 'UA-XXXXXXXX-X',
'optimizer_tracking_id' => 'UA-XXXXXXXX-X',
'analytics_tracking_admin' => 'no',
'analytics_tracking_editor' => 'yes',
'analytics_tracking_author' => 'yes',
'analytics_tracking_contributor' => 'yes',
'analytics_tracking_subscriber' => 'yes',
'analytics_tracking_visitor' => 'yes',
'javascript_enabled' => 'no'));

if (CONTENT_SWITCHER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') {
add_action('wp_head', 'optimizer_control_js');
add_action('wp_footer', 'optimizer_tracking_js');
add_action('wp_footer', 'analytics_tracking_js'); }