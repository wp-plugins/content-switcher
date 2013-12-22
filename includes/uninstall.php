<?php global $wpdb;
if (($for != 'network') || (!is_multisite()) || (!current_user_can('manage_network'))) { $for = 'single'; }
deactivate_plugins('installations-manager/installations-manager.php');
if ($for == 'network') {
$blogs_ids = (array) $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
$blogs_prefixes = array();
$original_blog_id = get_current_blog_id();
foreach ($blogs_ids as $blog_id) {
$blogs_prefixes[] = $wpdb->get_blog_prefix($blog_id);
switch_to_blog($blog_id);
$active_plugins = (array) get_option('active_plugins');
$new_active_plugins = array(); foreach ($active_plugins as $plugin) {
if ($plugin != 'installations-manager/installations-manager.php') { $new_active_plugins[] = $plugin; } }
update_option('active_plugins', $new_active_plugins); }
switch_to_blog($original_blog_id); }
else { $blogs_prefixes = array($wpdb->prefix); }
include INSTALLATIONS_MANAGER_PATH.'/tables.php';
foreach (array(
'affiliation_manager_websites',
'commerce_manager_websites',
'contact_manager_websites',
'membership_manager_websites',
'optin_manager_websites') as $table_slug) { unset($tables[$table_slug]); }
foreach ($blogs_prefixes as $blog_prefix) {
foreach ($tables as $table_slug => $table) {
$results = $wpdb->query("DROP TABLE ".$blog_prefix.'installations_manager_'.$table_slug); } }
include INSTALLATIONS_MANAGER_PATH.'/initial-options.php';
if ($for == 'network') {
foreach ($blogs_ids as $blog_id) {
switch_to_blog($blog_id);
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
delete_option(substr('installations_manager'.$_key, 0, 64)); } }
switch_to_blog($original_blog_id); }
else {
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
delete_option(substr('installations_manager'.$_key, 0, 64)); } }