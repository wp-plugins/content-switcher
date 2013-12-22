<?php
/*
Plugin Name: Installations Manager
Plugin URI: http://www.kleor.com
Description: Saves into the database the websites that installed Affiliation Manager, Commerce Manager, Contact Manager, Membership Manager or Optin Manager.
Version: 5.8
Author: Kleor
Author URI: http://www.kleor.com
Text Domain: installations-manager
*/


if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('ROOT_URL')) { $url = explode('/', str_replace('//', '||', HOME_URL)); define('ROOT_URL', str_replace('||', '//', $url[0])); }
if (!defined('HOME_PATH')) { $path = str_replace(ROOT_URL, '', HOME_URL); define('HOME_PATH', ($path == '' ? '/' : $path)); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('INSTALLATIONS_MANAGER_PATH', dirname(__FILE__));
define('INSTALLATIONS_MANAGER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('INSTALLATIONS_MANAGER_VERSION', $plugin_data['Version']);

if (!function_exists('fix_url')) { include_once INSTALLATIONS_MANAGER_PATH.'/libraries/formatting-functions.php'; }
if (is_admin()) { include_once INSTALLATIONS_MANAGER_PATH.'/admin.php'; }

function install_installations_manager() { include INSTALLATIONS_MANAGER_PATH.'/includes/install.php'; }

register_activation_hook(__FILE__, 'install_installations_manager');

global $wpdb;
$installations_manager_options = (array) get_option('installations_manager');
if ((!isset($installations_manager_options['version'])) || ($installations_manager_options['version'] != INSTALLATIONS_MANAGER_VERSION)) { install_installations_manager(); }

fix_url();


function installations_cron() {
$cron = get_option('installations_manager_cron');
if ($cron) {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$current_time = time();
$installation = (array) $cron['previous_installation'];
if ($installation['version'] != INSTALLATIONS_MANAGER_VERSION) {
$cron['previous_installation'] = array('version' => INSTALLATIONS_MANAGER_VERSION, 'number' => 0, 'timestamp' => $current_time); }
elseif (($installation['number'] < 12) && (($current_time - $installation['timestamp']) >= pow(2, $installation['number'] + 2))) {
$cron['previous_installation']['timestamp'] = $current_time; }
if ($cron['previous_installation'] != $installation) {
update_option('installations_manager_cron', $cron);
wp_remote_get(INSTALLATIONS_MANAGER_URL.'?action=install&key='.md5(AUTH_KEY)); } }
else { wp_remote_get(INSTALLATIONS_MANAGER_URL.'?action=install&key='.md5(AUTH_KEY)); } }

if ((!defined('INSTALLATIONS_MANAGER_DEMO')) || (INSTALLATIONS_MANAGER_DEMO == false)) {
foreach (array('admin_footer', 'login_footer', 'wp_footer') as $hook) { add_action($hook, 'installations_cron'); } }


function installations_data($atts) { include INSTALLATIONS_MANAGER_PATH.'/includes/data.php'; return $data; }


function installations_decimals_data($decimals, $data) {
if (($decimals != '') && (is_numeric($data))) {
$decimals = explode('/', $decimals);
for ($i = 0; $i < count($decimals); $i++) { $decimals[$i] = (int) $decimals[$i]; }
if ($data == round($data)) { $data = number_format($data, min($decimals), '.', ''); }
else { $data = number_format($data, max($decimals), '.', ''); } }
return $data; }


function installations_do_shortcode($string) {
$string = (string) $string;
$string = do_shortcode(str_replace(array('(', ')'), array('[', ']'), $string));
$string = str_replace(array('[', ']'), array('(', ')'), $string);
$string = str_replace(array('&#40;', '&#41;', '&#91;', '&#93;'), array('(', ')', '[', ']'), $string);
return $string; }


function installations_excerpt($data, $length = 80) {
$data = (string) $data;
if (strlen($data) > $length) { $data = substr($data, 0, ($length - 4)).' […]'; }
return $data; }


function installations_filter_data($filter, $data) {
if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', installations_do_shortcode($filter)), 0, PREG_SPLIT_NO_EMPTY); }
if (is_array($filter)) { foreach ($filter as $function) {
if (!function_exists($function)) { $function = 'installations_'.$function; }
if (!function_exists($function)) { $function = 'installations_manager_'.$function; }
if (function_exists($function)) { $data = $function($data); } } }
return $data; }


function installations_format_data($field, $data) { include INSTALLATIONS_MANAGER_PATH.'/includes/format-data.php'; return $data; }


function installations_i18n($string) {
load_plugin_textdomain('installations-manager', false, 'installations-manager/languages');
return __(__($string), 'installations-manager'); }


function installations_user_data($atts) { include INSTALLATIONS_MANAGER_PATH.'/includes/user-data.php'; return $data; }


function installations_item_data($type, $atts) { include INSTALLATIONS_MANAGER_PATH.'/includes/item-data.php'; return $data; }


function website_data($atts) {
return installations_item_data('website', $atts); }


function installations_jquery_js() {
if (!defined('KLEOR_JQUERY_LOADED')) { define('KLEOR_JQUERY_LOADED', true); ?>
<script type="text/javascript" src="<?php echo INSTALLATIONS_MANAGER_URL; ?>libraries/jquery.js"></script>
<?php } }


function installations_shortcode_atts($default_values, $atts) { include INSTALLATIONS_MANAGER_PATH.'/includes/shortcode-atts.php'; return $atts; }


function installations_sql_array($table, $array) { include INSTALLATIONS_MANAGER_PATH.'/includes/sql-array.php'; return $sql; }


for ($i = 0; $i < 4; $i++) {
add_shortcode('installations-counter'.($i == 0 ? '' : $i), create_function('$atts, $content', 'include_once INSTALLATIONS_MANAGER_PATH."/shortcodes.php"; return installations_counter($atts, $content);')); }

foreach (array(
'' => '',
'upgrades-' => '$atts["type"] = "upgrades"; ') as $key => $value) {
add_shortcode('installations-'.$key.'form', create_function('$atts', $value.'include_once INSTALLATIONS_MANAGER_PATH."/forms.php"; return installations_form($atts);')); }


add_shortcode('user', 'installations_user_data');
add_shortcode('installations-manager', 'installations_data');
add_shortcode('website', 'website_data');


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