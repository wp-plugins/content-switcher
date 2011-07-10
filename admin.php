<?php function content_switcher_options_page() { include 'options-page.php'; }

function content_switcher_admin_menu() {
add_options_page('Content Switcher', 'Content Switcher', 'manage_options', 'content-switcher', 'content_switcher_options_page'); }

add_action('admin_menu', 'content_switcher_admin_menu');


function content_switcher_action_links($links, $file) {
if ($file == 'content-switcher/content-switcher.php') {
return array_merge($links, array(
'<a href="options-general.php?page=content-switcher&amp;action=uninstall">'.__('Uninstall', 'content-switcher').'</a>',
'<a href="options-general.php?page=content-switcher">'.__('Options', 'content-switcher').'</a>')); }
return $links; }

add_filter('plugin_action_links', 'content_switcher_action_links', 10, 2);


function content_switcher_row_meta($links, $file) {
if ($file == 'content-switcher/content-switcher.php') {
return array_merge($links, array(
'<a href="http://www.kleor-editions.com/content-switcher">'.__('Documentation', 'content-switcher').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'content_switcher_row_meta', 10, 2);


function install_content_switcher() {
include 'initial-options.php';
$options = get_option('content_switcher');
foreach ($initial_options as $key => $value) {
if (($key == 'version') || ($options[$key] == '')) { $options[$key] = $value; } }
update_option('content_switcher', $options); }

register_activation_hook('content-switcher/content-switcher.php', 'install_content_switcher');