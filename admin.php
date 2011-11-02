<?php wp_enqueue_script('dashboard');

function membership_manager_back_office_page() { include 'back-office-page.php'; }
function membership_manager_member_page() { include 'member-page.php'; }
function membership_manager_member_area_page() { include 'member-area-page.php'; }
function membership_manager_options_page() { include 'options-page.php'; }
function membership_manager_statistics_page() { include 'statistics-page.php'; }
function membership_manager_table_page() { include 'table-page.php'; }

function membership_manager_admin_menu() {
include 'admin-pages.php';
add_menu_page('Membership Manager', __('Membership', 'membership-manager'), 'manage_options', 'membership-manager', 'membership_manager_options_page', '', 103);
foreach ($admin_pages as $key => $value) {
add_submenu_page('membership-manager', $value['page_title'], $value['menu_title'], 'manage_options', 'membership-manager'.($key == '' ? '' : '-'.str_replace('_', '-', $key)), $value['function']); } }

add_action('admin_menu', 'membership_manager_admin_menu');


function membership_manager_pages_date_picker($start_date, $end_date) {
echo '<p style="margin: 0 0 1em 0;"><label><strong>'.__('Start', 'membership-manager').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="start_date" id="start_date" size="20" value="'.$start_date.'" /></label>
<label style="margin-left: 3em;"><strong>'.__('End', 'membership-manager').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="end_date" id="end_date" size="20" value="'.$end_date.'" /></label>
<input style="margin-left: 3em;" type="submit" class="button-secondary" name="submit" value="'.__('Display', 'membership-manager').'" /></p>'; }


function membership_manager_pages_links($back_office_options) {
if ($back_office_options['links_displayed'] == 'yes') {
include 'admin-pages.php';
$links = (array) $back_office_options['links'];
if ($back_office_options['title_displayed'] == 'yes') { $left_margin = '6em'; } else { $left_margin = '0'; }
echo '<ul class="subsubsub" style="margin: 2em 0 1.5em '.$left_margin.'; float: left; white-space: normal;">';
$links_markup = array(
'Commerce Manager' => (function_exists('commerce_manager_admin_menu') ? '<a href="admin.php?page=commerce-manager'.
(strstr($_GET['page'], 'back-office') ? '-back-office' : '').
(strstr($_GET['page'], 'statistics') ? '-statistics' : '').'">Commerce Manager</a>' : '<a href="http://www.kleor-editions.com/commerce-manager">Commerce Manager</a>'),
'Documentation' => '<a href="http://www.kleor-editions.com/membership-manager/documentation">'.__('Documentation', 'membership-manager').'</a>');
for ($i = 0; $i < count($links); $i++) {
$link = $links[$i];
echo '<li>'.($i == 0 ? '' : ' | ').$links_markup[$link].'</li>'; }
echo '</ul>'; } }


function membership_manager_pages_menu($back_office_options) {
if ($back_office_options['menu_displayed'] == 'yes') {
include 'admin-pages.php';
echo '<ul class="subsubsub" style="margin: 0 0 1em; float: left; white-space: normal;">';
for ($i = 0; $i < $back_office_options['menu_items_number']; $i++) {
$menu_items = (array) $back_office_options['menu_items'];
$item = $menu_items[$i];
$slug = 'membership-manager'.($item == '' ? '' : '-'.str_replace('_', '-', $item));
echo '<li>'.($i == 0 ? '' : ' | ').'<a href="admin.php?page='.$slug.'"'.($_GET['page'] == $slug ? ' class="current"' : '').'>'.$admin_pages[$item]['menu_title'].'</a></li>'; }
echo '</ul>'; } }


function membership_manager_pages_module($back_office_options, $module, $undisplayed_modules) {
include 'admin-pages.php';
$page_slug = str_replace('-', '_', str_replace('-page', '', $module));
$page_undisplayed_modules = (array) $back_office_options[$page_slug.'_page_undisplayed_modules']; ?>
<div class="postbox"<?php if (in_array($module, $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="<?php echo $module; ?>"><strong><?php echo $modules['back_office'][$module]['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="<?php echo $page_slug; ?>_page_summary_displayed" id="<?php echo $page_slug; ?>_page_summary_displayed" value="yes"<?php if ($back_office_options[$page_slug.'_page_summary_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the summary', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Modules displayed', 'membership-manager'); ?></strong></th>
<td><?php foreach ($modules[$page_slug] as $key => $value) {
$name = $page_slug.'_page_'.str_replace('-', '_', $key).'_module_displayed';
if ($value['required'] == 'yes') { echo '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes" checked="checked" disabled="disabled" /> '.$value['name'].'<br />'; }
else { echo '<label'.((($page_slug != 'member') || (!isset($_GET['id'])) || (!in_array($key, array('registration-confirmation-email', 'registration-notification-email', 'autoresponders', 'custom-instructions')))) ? '' : ' style="display: none;"').'><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes"'.(in_array($key, $page_undisplayed_modules) ? '' : ' checked="checked"').' /> '.$value['name'].'<br /></label>'; }
if (is_array($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
$module_name = $page_slug.'_page_'.str_replace('-', '_', $module_key).'_module_displayed';
if ($module_value['required'] == 'yes') { echo '<input style="margin-left: 2em;" type="checkbox" name="'.$module_name.'" id="'.$module_name.'" value="yes" checked="checked" disabled="disabled" /> '.$module_value['name'].'<br />'; }
else { echo '<label><input style="margin-left: 2em;" type="checkbox" name="'.$module_name.'" id="'.$module_name.'" value="yes"'.(in_array($module_key, $page_undisplayed_modules) ? '' : ' checked="checked"').' /> '.$module_value['name'].'<br /></label>'; } } } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>
<?php }


function membership_manager_pages_search_field($type, $searchby, $searchby_options) { ?>
<p class="search-box" style="float: right;"><label><?php _e(ucfirst($type).' by', 'membership-manager'); ?> <select name="<?php echo $type; ?>by" id="<?php echo $type; ?>by">
<?php if ($type == 'search') { echo '<option value=""'.($searchby == '' ? ' selected="selected"' : '').'>'.__('all fields', 'membership-manager').'</option>'; } ?>
<?php foreach ($searchby_options as $key => $value) {
echo '<option value="'.$key.'"'.($searchby == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></label><br />
<input type="text" name="s" id="s" value="<?php echo $_GET['s']; ?>" />
<input type="submit" class="button" name="submit" id="<?php echo $type; ?>-submit" value="<?php _e(ucfirst($type), 'membership-manager'); ?>" /></p>
<div class="clear"></div>
<?php }


function membership_manager_pages_summary($back_office_options) {
if ($_GET['page'] == 'membership-manager') { $page_slug = 'options'; }
else { $page_slug = str_replace('-', '_', str_replace('membership-manager-', '', $_GET['page'])); }
if ($back_office_options[$page_slug.'_page_summary_displayed'] == 'yes') {
include 'admin-pages.php';
$modules = $modules[$page_slug];
$undisplayed_modules = (array) $back_office_options[$page_slug.'_page_undisplayed_modules'];
foreach ($modules as $key => $value) {
if (($page_slug != 'member') || (!isset($_GET['id'])) || (!in_array($key, array('registration-confirmation-email', 'registration-notification-email', 'autoresponders', 'custom-instructions')))) {
if (!in_array($key, $undisplayed_modules)) { $list .= '<li> | <a href="#'.$key.'">'.$value['name'].'</a></li>'; } } }
echo '<ul class="subsubsub" style="float: none; white-space: normal;">
<li>'.substr($list, 7).'
</ul>'; } }


function membership_manager_pages_title($back_office_options) {
if ($back_office_options['title_displayed'] == 'yes') {
echo '<h2 style="float: left;">'.$back_office_options['title'].'</h2>'; } }


function membership_manager_pages_top($back_office_options) {
membership_manager_pages_title($back_office_options);
membership_manager_pages_links($back_office_options);
echo '<div class="clear"></div>'; }


function membership_manager_action_links($links, $file) {
if ($file == 'membership-manager/membership-manager.php') {
return array_merge($links, array(
'<a href="admin.php?page=membership-manager&amp;action=uninstall">'.__('Uninstall', 'membership-manager').'</a>',
'<a href="admin.php?page=membership-manager">'.__('Options', 'membership-manager').'</a>')); }
return $links; }

add_filter('plugin_action_links', 'membership_manager_action_links', 10, 2);


function membership_manager_row_meta($links, $file) {
if ($file == 'membership-manager/membership-manager.php') {
return array_merge($links, array(
'<a href="http://www.kleor-editions.com/membership-manager/documentation">'.__('Documentation', 'membership-manager').'</a>')); }
return $links; }

add_filter('plugin_row_meta', 'membership_manager_row_meta', 10, 2);


function install_membership_manager() {
global $wpdb;
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
if (is_array($value)) {
$options = get_option('membership_manager'.$_key);
foreach ($value as $option => $initial_value) {
if (($option == 'version') || ($options[$option] == '')) { $options[$option] = $initial_value; } }
update_option('membership_manager'.$_key, $options); }
else { add_option('membership_manager'.$_key, $value); } }

include_once ABSPATH.'wp-admin/includes/upgrade.php';
if (!empty($wpdb->charset)) { $charset_collate = 'DEFAULT CHARACTER SET '.$wpdb->charset; }
if (!empty($wpdb->collate)) { $charset_collate .= ' COLLATE '.$wpdb->collate; }
include 'tables.php';
foreach ($tables as $table_slug => $table) {
unset($list); foreach ($table as $key => $value) { $list .= "
".$key." ".$value['type']." ".($key == "id" ? "auto_increment" : "NOT NULL").","; }
$sql = "CREATE TABLE ".$wpdb->prefix."membership_manager_".$table_slug." (".$list."
PRIMARY KEY  (id)) $charset_collate;"; dbDelta($sql);
foreach ($table as $key => $value) { if ($value['default'] != '') {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_".$table_slug." SET ".$key." = '".$value['default']."' WHERE ".$key." = ''"); } } } }

register_activation_hook('membership-manager/membership-manager.php', 'install_membership_manager');


function uninstall_membership_manager() {
global $wpdb;
include 'initial-options.php';
foreach ($initial_options as $key => $value) {
$_key = ($key == '' ? '' : '_'.$key);
delete_option('membership_manager'.$_key, $options); }
include 'tables.php';
foreach ($tables as $table_slug => $table) {
$results = $wpdb->query("DROP TABLE ".$wpdb->prefix.'membership_manager_'.$table_slug); } }


if (($_GET['action'] != 'delete') && (
($_GET['page'] == 'membership-manager-member')
 || ($_GET['page'] == 'membership-manager-member-area')
 || ($_GET['page'] == 'membership-manager-member-area-category')
 || ($_GET['page'] == 'membership-manager-member-category')
 || ($_GET['page'] == 'membership-manager-members')
 || ($_GET['page'] == 'membership-manager-members-areas')
 || ($_GET['page'] == 'membership-manager-members-areas-categories')
 || ($_GET['page'] == 'membership-manager-members-categories')
 || ($_GET['page'] == 'membership-manager-statistics'))) {
add_action('admin_head', 'membership_date_picker_css'); }