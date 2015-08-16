<?php
/*
Plugin Name: Content Switcher
Plugin URI: http://www.kleor.com/content-switcher/
Description: Allows you to easily display a random number, a random or variable content on your website, and to optimize your website with Google Optimizer and Google Analytics.
Version: 3.7
Author: Kleor
Author URI: http://www.kleor.com
Text Domain: content-switcher
License: GPL2
*/

/* 
Copyright 2010 Kleor (http://www.kleor.com)

This program is a free software. You can redistribute it and/or 
modify it under the terms of the GNU General Public License as 
published by the Free Software Foundation, either version 2 of 
the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, 
but without any warranty, without even the implied warranty of 
merchantability or fitness for a particular purpose. See the 
GNU General Public License for more details.
*/


define('CONTENT_SWITCHER_PATH', plugin_dir_path(__FILE__));
define('CONTENT_SWITCHER_URL', plugin_dir_url(__FILE__));
define('CONTENT_SWITCHER_FOLDER', str_replace('/content-switcher.php', '', plugin_basename(__FILE__)));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('CONTENT_SWITCHER_VERSION', $plugin_data['Version']);

if (!function_exists('kleor_do_shortcode')) { include_once CONTENT_SWITCHER_PATH.'libraries/shortcodes-functions.php'; }
if (is_admin()) { include_once CONTENT_SWITCHER_PATH.'admin.php'; }

function install_content_switcher() { include CONTENT_SWITCHER_PATH.'includes/install.php'; }

register_activation_hook(__FILE__, 'install_content_switcher');

$content_switcher_options = (array) get_option('content_switcher');
if ((!isset($content_switcher_options['version'])) || ($content_switcher_options['version'] != CONTENT_SWITCHER_VERSION)) { install_content_switcher(); }


function content_switcher_analytics_tracking_js() { include CONTENT_SWITCHER_PATH.'includes/analytics-tracking-js.php'; }

if (content_switcher_data('javascript_enabled') == 'yes') {
if (content_switcher_data('back_office_tracked') == 'yes') { add_action('admin_footer', 'content_switcher_analytics_tracking_js'); }
if (content_switcher_data('front_office_tracked') == 'yes') { foreach (array('login_footer', 'wp_footer') as $hook) { add_action($hook, 'content_switcher_analytics_tracking_js'); } } }


function content_switcher_data($atts) { include CONTENT_SWITCHER_PATH.'includes/data.php'; return $data; }


function content_switcher_filter_data($filter, $data) { include CONTENT_SWITCHER_PATH.'includes/filter-data.php'; return $data; }


function content_switcher_format_nice_name($string) {
$string = strtolower(content_switcher_strip_accents(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-z0-9_-]/', '', $string);
return $string; }


function content_switcher_i18n($string) {
load_plugin_textdomain('content-switcher', false, CONTENT_SWITCHER_FOLDER.'/languages');
return __(__($string), 'content-switcher'); }


function content_switcher_optimizer_control_js() { include CONTENT_SWITCHER_PATH.'includes/optimizer-control-js.php'; }

if (content_switcher_data('javascript_enabled') == 'yes') { add_action('wp_head', 'content_switcher_optimizer_control_js'); }


function content_switcher_optimizer_tracking_js() { include CONTENT_SWITCHER_PATH.'includes/optimizer-tracking-js.php'; }

if (content_switcher_data('javascript_enabled') == 'yes') { add_action('wp_footer', 'content_switcher_optimizer_tracking_js'); }


function content_switcher_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }


$tags = array();
foreach (array('random', 'variable') as $string) {
$function = create_function('$atts, $content', 'include_once CONTENT_SWITCHER_PATH."shortcodes.php"; return content_switcher_'.$string.'_content($atts, $content);');
for ($i = 0; $i < 4; $i++) { $tag = $string.'-content'.($i == 0 ? '' : $i); $tags[] = $tag; add_shortcode($tag, $function); } }
$tags[] = 'optimizer-content'; add_shortcode('optimizer-content', create_function('$atts, $content', 'include_once CONTENT_SWITCHER_PATH."shortcodes.php"; return content_switcher_optimizer_content($atts, $content);'));
foreach (array('random-number', 'variable-string') as $tag) {
$tags[] = $tag; add_shortcode($tag, create_function('$atts', 'include_once CONTENT_SWITCHER_PATH."shortcodes.php"; return content_switcher_'.str_replace('-', '_', $tag).'($atts);')); }
$tags[] = 'content-switcher'; add_shortcode('content-switcher', 'content_switcher_data');
$content_switcher_shortcodes = $tags;


function replace_content_switcher_shortcodes($data) { include CONTENT_SWITCHER_PATH.'includes/replace-shortcodes.php'; return $data; }

add_filter('wp_insert_post_data', 'replace_content_switcher_shortcodes', 10, 1);


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