<?php global $wpdb;
include_once ABSPATH.'wp-admin/includes/upgrade.php';
$charset_collate = '';
if (!empty($wpdb->charset)) { $charset_collate .= 'DEFAULT CHARACTER SET '.$wpdb->charset; }
if (!empty($wpdb->collate)) { $charset_collate .= ' COLLATE '.$wpdb->collate; }
include INSTALLATIONS_MANAGER_PATH.'/tables.php';
foreach (array(
'affiliation_manager_websites',
'commerce_manager_websites',
'contact_manager_websites',
'membership_manager_websites',
'optin_manager_websites') as $table_slug) { unset($tables[$table_slug]); }
foreach ($tables as $table_slug => $table) {
$list = ''; foreach ($table as $key => $value) { $list .= "
".$key." ".$value['type']." ".($key == "id" ? "auto_increment" : "NOT NULL").","; }
$sql = "CREATE TABLE ".$wpdb->prefix."installations_manager_".$table_slug." (".$list."
PRIMARY KEY  (id)) $charset_collate;"; dbDelta($sql);
foreach ($table as $key => $value) {
if (isset($value['constraint'])) {
$results = $wpdb->query("ALTER TABLE ".$wpdb->prefix."installations_manager_".$table_slug." ADD ".$value['constraint']." (".$key.")"); }
if (isset($value['default'])) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."installations_manager_".$table_slug." SET ".$key." = '".$value['default']."' WHERE ".$key." = ''"); } } }

load_plugin_textdomain('installations-manager', false, 'installations-manager/languages');
include INSTALLATIONS_MANAGER_PATH.'/initial-options.php';
$overwrited_options = array('menu_title_'.$lang, 'pages_titles_'.$lang, 'version');
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
if (is_array($value)) {
$options = (array) get_option('installations_manager'.$_key);
$current_options = $options;
if ((isset($options[0])) && ($options[0] === false)) { unset($options[0]); }
foreach ($value as $option => $initial_value) {
if ((!isset($options[$option])) || ($options[$option] == '') || (in_array($option, $overwrited_options))) { $options[$option] = $initial_value; } }
if ($options != $current_options) { update_option('installations_manager'.$_key, $options); } }
else { add_option(substr('installations_manager'.$_key, 0, 64), $value); } }

if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$cron = (array) get_option('installations_manager_cron');
if ((!isset($cron['previous_installation'])) || ($cron['previous_installation']['version'] != INSTALLATIONS_MANAGER_VERSION)) {
$cron['previous_installation'] = array('version' => INSTALLATIONS_MANAGER_VERSION, 'number' => 1); }
else { $cron['previous_installation']['number'] = $cron['previous_installation']['number'] + 1; }
$cron['previous_installation']['timestamp'] = time();
update_option('installations_manager_cron', $cron);