<?php if (strstr($_SERVER['REQUEST_URI'], '/plugins.php')) { load_plugin_textdomain('installations-manager', false, 'installations-manager/languages'); }
if ((isset($_GET['page'])) && (strstr($_GET['page'], 'installations-manager'))) { include_once INSTALLATIONS_MANAGER_PATH.'/admin-pages-functions.php'; }


function installations_manager_admin_menu() {
$lang = strtolower(substr(WPLANG, 0, 2)); if ($lang == '') { $lang = 'en'; }
include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php';
$options = (array) get_option('installations_manager_back_office');
if ((!isset($options['menu_title_'.$lang])) || ($options['menu_title_'.$lang] == '') || (!isset($options['pages_titles_'.$lang]))
 || ($options['pages_titles_'.$lang] == '')) { install_installations_manager(); $options = (array) get_option('installations_manager_back_office'); }
$menu_title = $options['menu_title_'.$lang]; $pages_titles = (array) $options['pages_titles_'.$lang];
if (((isset($_GET['page'])) && (strstr($_GET['page'], 'installations-manager'))) || ($menu_title == '')) { $menu_title = __('Installations', 'installations-manager'); }
if ((defined('INSTALLATIONS_MANAGER_DEMO')) && (INSTALLATIONS_MANAGER_DEMO == true)) { $capability = 'manage_options'; }
else { $role = $options['minimum_roles']['view']; $capability = $roles[$role]['capability']; }
if ($options['custom_icon_used'] == 'yes') { $icon_url = format_url($options['custom_icon_url']); } else { $icon_url = ''; }
add_menu_page('Installations Manager', $menu_title, $capability, 'installations-manager', create_function('', 'include_once INSTALLATIONS_MANAGER_PATH."/options-page.php";'), $icon_url, 999);
$admin_menu_pages = installations_manager_admin_menu_pages();
foreach ($admin_pages as $key => $value) { if (in_array($key, $admin_menu_pages)) {
$slug = 'installations-manager'.($key == '' ? '' : '-'.str_replace('_', '-', $key));
if ((!isset($_GET['page'])) || (!strstr($_GET['page'], 'installations-manager'))) { $value['menu_title'] = $pages_titles[$key]; }
add_submenu_page('installations-manager', $value['page_title'], $value['menu_title'], $capability, $slug, create_function('', 'include_once INSTALLATIONS_MANAGER_PATH."/'.$value['file'].'";')); } } }

add_action('admin_menu', 'installations_manager_admin_menu');


function installations_manager_admin_menu_pages() {
include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php';
$options = (array) get_option('installations_manager_back_office');
$menu_items = (array) $options['menu_items'];
$numbers = (array) $options['menu_displayed_items'];
$menu_displayed_items = array();
foreach ($numbers as $i) { $menu_displayed_items[] = $menu_items[$i]; }
$admin_menu_pages = array(); foreach ($admin_pages as $key => $value) {
$slug = 'installations-manager'.($key == '' ? '' : '-'.str_replace('_', '-', $key));
if (($key == '') || ($key == 'back_office') || ((isset($_GET['page'])) && ($_GET['page'] == $slug))
 || (in_array($key, $menu_displayed_items))) { $admin_menu_pages[] = $key; } }
return $admin_menu_pages; }


function installations_manager_user_can($back_office_options, $capability) {
if ((defined('INSTALLATIONS_MANAGER_DEMO')) && (INSTALLATIONS_MANAGER_DEMO == true)) { $capability = 'manage_options'; }
else { include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php'; $role = $back_office_options['minimum_roles'][$capability]; $capability = $roles[$role]['capability']; }
return current_user_can($capability); }


function installations_manager_action_links($links) {
if (!is_network_admin()) {
$links = array_merge($links, array(
'<span class="delete"><a href="admin.php?page=installations-manager&amp;action=uninstall" title="'.__('Delete the options and tables of Installations Manager', 'installations-manager').'">'.__('Uninstall', 'installations-manager').'</a></span>',
'<span class="delete"><a href="admin.php?page=installations-manager&amp;action=reset" title="'.__('Reset the options of Installations Manager', 'installations-manager').'">'.__('Reset', 'installations-manager').'</a></span>',
'<a href="admin.php?page=installations-manager">'.__('Options', 'installations-manager').'</a>')); }
else {
$links = array_merge($links, array(
'<span class="delete"><a href="../admin.php?page=installations-manager&amp;action=uninstall&amp;for=network" title="'.__('Delete the options and tables of Installations Manager for all sites in this network', 'installations-manager').'">'.__('Uninstall', 'installations-manager').'</a></span>')); }
return $links; }

foreach (array('', 'network_admin_') as $prefix) { add_filter($prefix.'plugin_action_links_installations-manager/installations-manager.php', 'installations_manager_action_links', 10, 2); }


function reset_installations_manager() {
load_plugin_textdomain('installations-manager', false, 'installations-manager/languages');
include INSTALLATIONS_MANAGER_PATH.'/initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
update_option(substr('installations_manager'.$_key, 0, 64), $value); } }


function uninstall_installations_manager($for = 'single') { include INSTALLATIONS_MANAGER_PATH.'/includes/uninstall.php'; }