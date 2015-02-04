<?php global $wpdb;
if (($for != 'network') || (!is_multisite()) || (!current_user_can('manage_network'))) { $for = 'single'; }
include_once ABSPATH.'wp-admin/includes/plugin.php';
deactivate_plugins(CONTENT_SWITCHER_FOLDER.'/content-switcher.php');
if ($for == 'network') {
$blogs_ids = (array) $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
$original_blog_id = get_current_blog_id();
foreach ($blogs_ids as $blog_id) {
switch_to_blog($blog_id);
$active_plugins = (array) get_option('active_plugins');
$new_active_plugins = array(); foreach ($active_plugins as $plugin) {
if ($plugin != CONTENT_SWITCHER_FOLDER.'/content-switcher.php') { $new_active_plugins[] = $plugin; } }
update_option('active_plugins', $new_active_plugins);
delete_option('content_switcher'); }
switch_to_blog($original_blog_id); }
else { delete_option('content_switcher'); }