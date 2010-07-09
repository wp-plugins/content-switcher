<?php
/*
Plugin Name: Content Switcher
Plugin URI: http://www.kleor-editions.com/content-switcher
Description: Easily display a random number, a random or variable content on your website. Optimize your website with Google Optimizer.
Version: 1.0
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
define('CONTENT_SWITCHER_OPTIONS_OPTIMIZER_TRACKING_ID', $content_switcher_options['optimizer_tracking_id']);
define('CONTENT_SWITCHER_OPTIONS_JAVASCRIPT_ENABLED', $content_switcher_options['javascript_enabled']);


function random_number($atts) {
extract(shortcode_atts(array(set => '', 'min' => 0, 'max' => 0, 'digits' => 0), $atts));

$set = $atts['set'];
if ($set == '') {
$min = floor($atts['min']);
$max = floor($atts['max']);
if ($min <= $max) { $n = mt_rand($min, $max); } else { $n = mt_rand($max, $min); } }
else { $set = explode('/', $set); $n = $set[mt_rand(0, count($set) - 1)]; }

if ($n >= 0) { $symbol = ''; } else { $symbol = '-'; $n = -$n; }
$number = (string) $n;
$length = strlen($number);
$digits = floor($atts['digits']);
while ($length < $digits) { $number = '0'.$number; $length = $length + 1; }
$number = $symbol.$number;

return $number; }


function random_content($atts, $content) {
extract(shortcode_atts(array('string' => ''), $atts));

$string = $atts['string'];
if ($string != '') {
$string = str_replace('(', '[', $string);
$string = str_replace(')', ']', $string);
$string = do_shortcode($string); }

$content = explode('[other]', $content);
$m = count($content) - 1;
$n = mt_rand(0, $m);
$content[$n] = str_ireplace('[string]', $string, $content[$n]);
$content[$n] = do_shortcode($content[$n]);

return $content[$n]; }


function variable_content($atts, $content) {
extract(shortcode_atts(array('name' => '', 'string' => ''), $atts));

$name = $atts['name'];
if ($name == '') { $name = 'content'; }

$string = $atts['string'];
if ($string != '') {
$string = str_replace('(', '[', $string);
$string = str_replace(')', ']', $string);
$string = do_shortcode($string); }

$content = explode('[other]', $content);
$m = count($content);

if (isset($_GET[$name])) {
	if ($m == 1) { $n = 0; $content[0] = utf8_encode($_GET[$name]); }
	else { $values = $atts['values'];
		if ($values == '') { $n = (floor($_GET[$name]))%$m; }
		else {
		$values = explode('/', $values);
		$v = count($values) - 1;
		for ($i = 0; $i <= $v; $i++) { { if ($_GET[$name] == $values[$i]) { $n = $i; } } }
		}
	}
}
else { $n = 0; }

$content[$n] = str_ireplace('[string]', $string, $content[$n]);
$content[$n] = do_shortcode($content[$n]);

return $content[$n]; }


function optimizer_content($atts, $content) {
extract(shortcode_atts(array('name' => ''), $atts));
$name = $atts['name'];
if ($name == '') { $name = 'Content'; }
$content = do_shortcode($content);

if (CONTENT_SWITCHER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') {
$content = '<script type="text/javascript">utmx_section("'.$name.'")</script>'
.$content.'</noscript>'; }

return $content; }


function optimizer_control_js() {
global $post;
$optimizer = get_post_meta($post->ID, 'optimizer', true);
$optimizer = explode('/', $optimizer);
if ($optimizer[2] == 'test') { ?>
<script type="text/javascript">var optimizer_experiment_id = "<?php echo $optimizer[1]; ?>";</script>
<script type="text/javascript" src="<?php echo CONTENT_SWITCHER_URL; ?>optimizer-control.js"></script><?php } }


function optimizer_tracking_js() {
global $post;
$optimizer = get_post_meta($post->ID, 'optimizer', true);
$type = substr($optimizer, -4);
if (($type == 'test') || ($type == 'goal')) { ?>
<script type="text/javascript">var optimizer_tracking_id = "<?php echo CONTENT_SWITCHER_OPTIONS_OPTIMIZER_TRACKING_ID; ?>"; var optimizer = "<?php echo $optimizer; ?>";</script>
<script type="text/javascript" src="<?php echo CONTENT_SWITCHER_URL; ?>optimizer-tracking.js"></script><?php } }


function content_switcher_options_page() {
if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ($_REQUEST['submit'] ==  __('Save Changes')) {
$optimizer_tracking_id = mysql_real_escape_string($_REQUEST['optimizer-tracking-id']);
if ($_REQUEST['javascript-enabled'] == 'yes') { $javascript_enabled = 'yes'; } else { $javascript_enabled = 'no'; }
$content_switcher_options = array('optimizer_tracking_id' => $optimizer_tracking_id, 'javascript_enabled' => $javascript_enabled);
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
<label for="optimizer-tracking-id">'.__('Google Optimizer Account Tracking ID', 'content-switcher').':</label> <input type="text" name="optimizer-tracking-id" id="optimizer-tracking-id" value="'.$content_switcher_options['optimizer_tracking_id'].'" size="16" />
'.__('<a href="http://www.kleor-editions.com/content-switcher/en/#part5.2">More informations</a>', 'content-switcher').'</p>
<p><input type="checkbox" name="javascript-enabled" id="javascript-enabled" value="yes" '.($content_switcher_options['javascript_enabled'] == 'yes' ? 'checked="checked"' : '' ).' /> <label for="javascript-enabled">'.__('Add JavaScript code', 'content-switcher').'</label><br />
'.__('If you uncheck this box, Content Switcher will never add any JavaScript code to the pages of your website, but Google Optimizer will not work.', 'content-switcher').' '.__('<a href="http://www.kleor-editions.com/content-switcher/en/#part6.1">More informations</a>', 'content-switcher').'</p>
<p><input class="button" name="submit" value="'.__('Save Changes').'" type="submit" /></p>
</form>
</div>';
	
echo $content; }


function content_switcher_admin_menu() {
add_options_page('Content Switcher', 'Content Switcher', 'manage_options', 'content-switcher', 'content_switcher_options_page'); }


add_filter('widget_text', 'do_shortcode');
add_shortcode('random-number', 'random_number');
add_shortcode('random-content', 'random_content');
add_shortcode('variable-content', 'variable_content');
add_shortcode('optimizer-content', 'optimizer_content');
add_action('admin_menu', 'content_switcher_admin_menu');

add_option('content_switcher', array(
'optimizer_tracking_id' => 'UA-XXXXXXXX-X',
'javascript_enabled' => 'no'));

if (CONTENT_SWITCHER_OPTIONS_JAVASCRIPT_ENABLED == 'yes') {
add_action('wp_head', 'optimizer_control_js');
add_action('wp_footer', 'optimizer_tracking_js'); }