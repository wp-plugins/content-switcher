<?php if ((strstr($_GET['page'], 'content-switcher')) || (strstr($_SERVER['REQUEST_URI'], '/plugins.php'))) {
load_plugin_textdomain('content-switcher', false, 'content-switcher/languages'); }


function content_switcher_options_page() {
add_options_page('Content Switcher', 'Content Switcher', 'manage_options', 'content-switcher', create_function('', 'include "options-page.php";')); }

add_action('admin_menu', 'content_switcher_options_page');


function content_switcher_action_links($links, $file) {
if ($file == 'content-switcher/content-switcher.php') {
if (!is_multisite()) {
$links = array_merge($links, array(
'<a href="options-general.php?page=content-switcher&amp;action=uninstall">'.__('Uninstall', 'content-switcher').'</a>')); }
$links = array_merge($links, array(
'<a href="options-general.php?page=content-switcher&amp;action=reset">'.__('Reset', 'content-switcher').'</a>',
'<a href="options-general.php?page=content-switcher">'.__('Options', 'content-switcher').'</a>')); }
return $links; }

add_filter('plugin_action_links', 'content_switcher_action_links', 10, 2);


function content_switcher_row_meta($links, $file) {
if ($file == 'content-switcher/content-switcher.php') {
$links = array_merge($links, array(
'<a href="http://www.kleor-editions.com/content-switcher">'.__('Documentation', 'content-switcher').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'content_switcher_row_meta', 10, 2);


function install_content_switcher() {
load_plugin_textdomain('content-switcher', false, 'content-switcher/languages');
include 'initial-options.php';
$options = get_option('content_switcher');
foreach ($initial_options as $key => $value) {
if (($key == 'version') || ($options[$key] == '')) { $options[$key] = $value; } }
update_option('content_switcher', $options); }

register_activation_hook('content-switcher/content-switcher.php', 'install_content_switcher');


function reset_content_switcher() {
load_plugin_textdomain('content-switcher', false, 'content-switcher/languages');
include 'initial-options.php';
update_option('content_switcher', $initial_options); }