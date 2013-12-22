<?php load_plugin_textdomain('installations-manager', false, 'installations-manager/languages');
add_action('admin_enqueue_scripts', create_function('', 'wp_enqueue_script("dashboard");'));
foreach ((array) $_GET as $key => $value) { if (is_string($value)) { $_GET[$key] = quotes_entities($_GET[$key]); } }
if (isset($_GET['id'])) { $_GET['id'] = (int) $_GET['id']; if ($_GET['id'] < 1) { unset($_GET['id']); } }
foreach ($_GET as $key => $value) { $GLOBALS[$key] = $value; }


function installations_manager_pages_css() { ?>
<style type="text/css">
.wrap { margin-top: 0; }
.wrap .delete:hover { color: #ff0000; }
.wrap .dp-choose-date { vertical-align: 6%; }
.wrap .postbox { background-color: #f8f8f8; }
.wrap .postbox .description { font-size: 13px; }
.wrap .postbox h3 { background-color: #f2f2f2; color: #000000; }
.wrap .postbox h4 { color: #000000; font-family: Tahoma, Geneva, sans-serif; font-size: 1.125em; }
.wrap .postbox input.button-secondary { background-color: #ffffff; }
.wrap h2 { float: left; }
.wrap input.button-secondary, .wrap select { vertical-align: 0; }
.wrap input.date-pick { margin-right: 0.5em; width: 10.5em; }
.wrap p.submit { margin: 0 20%; }
</style>
<?php }

add_action('admin_head', 'installations_manager_pages_css');


function installations_manager_pages_links($back_office_options) {
$links = (array) $back_office_options['links'];
$displayed_links = (array) $back_office_options['displayed_links'];
if (($back_office_options['links_displayed'] == 'yes') && (count($displayed_links) > 0)) {
include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php';
if ($back_office_options['title_displayed'] == 'yes') { $left_margin = '6em'; } else { $left_margin = '0'; }
echo '<ul class="subsubsub" style="margin: 1.75em 0 1.5em '.$left_margin.'; float: left; white-space: normal;">';
$links_markup = array(
'Commerce Manager' => (function_exists('commerce_manager_admin_menu') ? '<a href="admin.php?page=commerce-manager>'.$admin_links['Commerce Manager']['name'].'</a>' : '<a target="'.$back_office_options['documentations_links_target'].'" href="http://www.kleor.com/commerce-manager">'.$admin_links['Commerce Manager']['name'].'</a>'),
'Affiliation Manager' => (function_exists('affiliation_manager_admin_menu') ? '<a href="admin.php?page=affiliation-manager>'.$admin_links['Affiliation Manager']['name'].'</a>' : '<a target="'.$back_office_options['documentations_links_target'].'" href="http://www.kleor.com/affiliation-manager">'.$admin_links['Affiliation Manager']['name'].'</a>'),
'Membership Manager' => (function_exists('membership_manager_admin_menu') ? '<a href="admin.php?page=membership-manager>'.$admin_links['Membership Manager']['name'].'</a>' : '<a target="'.$back_office_options['documentations_links_target'].'" href="http://www.kleor.com/membership-manager">'.$admin_links['Membership Manager']['name'].'</a>'),
'Optin Manager' => (function_exists('optin_manager_admin_menu') ? '<a href="admin.php?page=optin-manager>'.$admin_links['Optin Manager']['name'].'</a>' : '<a target="'.$back_office_options['documentations_links_target'].'" href="http://www.kleor.com/optin-manager">'.$admin_links['Optin Manager']['name'].'</a>'),
'Contact Manager' => (function_exists('contact_manager_admin_menu') ? '<a href="admin.php?page=contact-manager>'.$admin_links['Contact Manager']['name'].'</a>' : '<a target="'.$back_office_options['documentations_links_target'].'" href="http://www.kleor.com/contact-manager">'.$admin_links['Contact Manager']['name'].'</a>'));
$first = true; $links_displayed = array();
for ($i = 0; $i < count($admin_links); $i++) {
$link = (isset($links[$i]) ? $links[$i] : '');
if ((in_array($i, $displayed_links)) && (isset($links_markup[$link])) && (!in_array($link, $links_displayed))) {
echo '<li>'.($first ? '' : '&nbsp;| ').$links_markup[$link].'</li>'; $first = false; $links_displayed[] = $link; } }
echo '</ul>'; } }


function installations_manager_pages_links_markups($back_office_options) {
foreach (array('default_options', 'documentations', 'ids_fields', 'pages_modules', 'urls_fields') as $string) {
$markups[$string.'_links_markup'] = 'target="'.$back_office_options[$string.'_links_target'].'"'; }
return $markups; }


function installations_manager_pages_menu($back_office_options) {
$menu_items = (array) $back_office_options['menu_items'];
$menu_displayed_items = (array) $back_office_options['menu_displayed_items'];
if (($back_office_options['menu_displayed'] == 'yes') && (count($menu_displayed_items) > 0)) {
include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php';
echo '<ul class="subsubsub" style="margin: 0 0 1em; float: left; white-space: normal;">';
$first = true; $items_displayed = array();
for ($i = 0; $i < count($admin_pages); $i++) {
$item = (isset($menu_items[$i]) ? $menu_items[$i] : '');
if ((isset($admin_pages[$item])) && (in_array($i, $menu_displayed_items)) && (!in_array($item, $items_displayed))) {
$slug = 'installations-manager'.($item == '' ? '' : '-'.str_replace('_', '-', $item));
echo '<li>'.($first ? '' : '&nbsp;| ').'<a href="admin.php?page='.$slug.'"'.($_GET['page'] == $slug ? ' class="current"' : '').'>'.$admin_pages[$item]['menu_title'].'</a></li>';
$first = false; $items_displayed[] = $item; } }
echo '</ul>'; } }


function installations_manager_pages_module($back_office_options, $module, $undisplayed_modules) {
include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php';
$page_slug = str_replace('-', '_', str_replace('-page', '', $module));
$page_undisplayed_modules = (array) $back_office_options[$page_slug.'_page_undisplayed_modules']; ?>
<div class="postbox" id="<?php echo $module.'-module'; ?>"<?php if (in_array($module, $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="<?php echo $module; ?>"><strong><?php echo $modules['back_office'][$module]['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if ((strstr($_GET['page'], 'back-office')) && ($page_slug != 'back_office')) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a target="'.$back_office_options['pages_modules_links_target'].'" href="admin.php?page=installations-manager'.($page_slug == 'options' ? '' : '-'.str_replace('_', '-', $page_slug)).'">'.__('Click here to open this page.', 'installations-manager').'</a></span></td></tr>'; } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="<?php echo $page_slug; ?>_page_summary_displayed" id="<?php echo $page_slug; ?>_page_summary_displayed" value="yes"<?php if ($back_office_options[$page_slug.'_page_summary_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the summary', 'installations-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Modules displayed', 'installations-manager'); ?></strong></th>
<td><?php foreach ($modules[$page_slug] as $key => $value) {
$name = $page_slug.'_page_'.str_replace('-', '_', $key).'_module_displayed';
if (strstr($_GET['page'], 'back-office')) { $onmouseover = ""; }
else { $onmouseover = " onmouseover=\"document.getElementById('".$key."-submodules').style.display = 'block';\""; }
if ((isset($value['required'])) && ($value['required'] == 'yes')) { echo '<label'.$onmouseover.'><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes" checked="checked" disabled="disabled" /> '.$value['name'].'<br /></label>'; }
else { echo '<label'.$onmouseover.'><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes"'.(in_array($key, $page_undisplayed_modules) ? '' : ' checked="checked"').' /> '.$value['name'].'<br /></label>'; }
if (!strstr($_GET['page'], 'back-office')) { echo '<div style="display: none;" id="'.$key.'-submodules">'; }
if (isset($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
$module_name = $page_slug.'_page_'.str_replace('-', '_', $module_key).'_module_displayed';
if ((isset($module_value['required'])) && ($module_value['required'] == 'yes')) { echo '<label><input style="margin-left: 2em;" type="checkbox" name="'.$module_name.'" id="'.$module_name.'" value="yes" checked="checked" disabled="disabled" /> '.$module_value['name'].'<br /></label>'; }
else { echo '<label><input style="margin-left: 2em;" type="checkbox" name="'.$module_name.'" id="'.$module_name.'" value="yes"'.(in_array($module_key, $page_undisplayed_modules) ? '' : ' checked="checked"').' /> '.$module_value['name'].'<br /></label>'; } } }
if (!strstr($_GET['page'], 'back-office')) { echo '</div>'; } } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div></div>
<?php }


function installations_manager_pages_search_field($type, $searchby, $searchby_options) { ?>
<p class="search-box" style="float: right;"><label><?php _e(ucfirst($type).' by', 'installations-manager'); ?> <select name="<?php echo $type; ?>by" id="<?php echo $type; ?>by">
<?php if ($type == 'search') { echo '<option value=""'.($searchby == '' ? ' selected="selected"' : '').'>'.__('all fields', 'installations-manager').'</option>'; } ?>
<?php foreach ($searchby_options as $key => $value) {
echo '<option value="'.$key.'"'.($searchby == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></label><br />
<input type="text" name="s" id="s" size="40" value="<?php if (isset($_GET['s'])) { echo $_GET['s']; } ?>" />
<input type="submit" class="button" name="submit" id="<?php echo $type; ?>-submit" value="<?php _e(ucfirst($type), 'installations-manager'); ?>" /></p>
<?php }


function installations_manager_pages_summary($back_office_options) {
if ($_GET['page'] == 'installations-manager') { $page_slug = 'options'; }
else { $page_slug = str_replace('-', '_', str_replace('installations-manager-', '', $_GET['page'])); }
if ($back_office_options[$page_slug.'_page_summary_displayed'] == 'yes') {
include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php';
$modules = $modules[$page_slug];
$undisplayed_modules = (array) $back_office_options[$page_slug.'_page_undisplayed_modules'];
$list = ''; foreach ($modules as $key => $value) {
if (!in_array($key, $undisplayed_modules)) { $list .= '<li>&nbsp;| <a href="#'.$key.'">'.$value['name'].'</a></li>'; } }
if ($list != '') { echo '<ul class="subsubsub" style="float: none; margin-bottom: 1em; white-space: normal;"><li>'.substr($list, 12).'</ul>'; } } }


function installations_manager_pages_title($back_office_options) {
if ($back_office_options['title_displayed'] == 'yes') {
echo '<h2 style="font-size: 1.75em;">'.$back_office_options['title'].'</h2>'; } }


function installations_manager_pages_top($back_office_options) {
installations_manager_pages_title($back_office_options);
installations_manager_pages_links($back_office_options);
echo '<div class="clear"></div>'; }


function installations_manager_users_roles() {
$wp_roles = new WP_Roles();
$roles = $wp_roles->get_names();
foreach ($roles as $role => $name) { $roles[$role] = translate_user_role($name); }
return $roles; }


function update_installations_manager_back_office($back_office_options, $page) {
include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php';
if ((!isset($_POST[$page.'_page_summary_displayed'])) || ($_POST[$page.'_page_summary_displayed'] != 'yes')) { $_POST[$page.'_page_summary_displayed'] = 'no'; }
$_POST[$page.'_page_undisplayed_modules'] = array();
foreach ($modules[$page] as $key => $value) {
if (((!isset($_POST[$page.'_page_'.str_replace('-', '_', $key).'_module_displayed'])) || ($_POST[$page.'_page_'.str_replace('-', '_', $key).'_module_displayed'] != 'yes'))
 && ((!isset($value['required'])) || ($value['required'] != 'yes'))) { $_POST[$page.'_page_undisplayed_modules'][] = $key; }
if (isset($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
if (((!isset($_POST[$page.'_page_'.str_replace('-', '_', $module_key).'_module_displayed'])) || ($_POST[$page.'_page_'.str_replace('-', '_', $module_key).'_module_displayed'] != 'yes'))
 && ((!isset($module_value['required'])) || ($module_value['required'] != 'yes'))) { $_POST[$page.'_page_undisplayed_modules'][] = $module_key; } } } }
if (!strstr($_GET['page'], 'back-office')) {
foreach (array('summary_displayed', 'undisplayed_modules') as $option) {
if (isset($_POST[$page.'_page_'.$option])) { $back_office_options[$page.'_page_'.$option] = $_POST[$page.'_page_'.$option]; } }
update_option('installations_manager_back_office', $back_office_options);
return $back_office_options; } }


function installations_manager_pages_date_picker($start_date, $end_date) {
echo '<p style="margin: 0 0 1em 0; float: left;"><label><strong>'.__('Start', 'installations-manager').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="start_date" id="start_date" size="20" value="'.$start_date.'" /></label>
<label style="margin-left: 3em;"><strong>'.__('End', 'installations-manager').'</strong>
<input class="date-pick" style="margin: 0.5em;" type="text" name="end_date" id="end_date" size="20" value="'.$end_date.'" /></label>
<input style="margin-left: 3em; vertical-align: middle;" type="submit" class="button-secondary" name="submit" value="'.__('Display', 'installations-manager').'" /></p>
<div class="clear"></div>'; }


function installations_manager_date_picker_css() { ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo INSTALLATIONS_MANAGER_URL; ?>libraries/date-picker.css" />
<?php }


function installations_manager_date_picker_js() { ?>
<script type="text/javascript" src="<?php echo INSTALLATIONS_MANAGER_URL; ?>libraries/date-picker.js"></script>
<script type="text/javascript">
Date.dayNames = ['<?php _e('Sunday', 'installations-manager'); ?>', '<?php _e('Monday', 'installations-manager'); ?>', '<?php _e('Tuesday', 'installations-manager'); ?>', '<?php _e('Wednesday', 'installations-manager'); ?>', '<?php _e('Thursday', 'installations-manager'); ?>', '<?php _e('Friday', 'installations-manager'); ?>', '<?php _e('Saturday', 'installations-manager'); ?>'];
Date.abbrDayNames = ['<?php _e('Sun', 'installations-manager'); ?>', '<?php _e('Mon', 'installations-manager'); ?>', '<?php _e('Tue', 'installations-manager'); ?>', '<?php _e('Wed', 'installations-manager'); ?>', '<?php _e('Thu', 'installations-manager'); ?>', '<?php _e('Fri', 'installations-manager'); ?>', '<?php _e('Sat', 'installations-manager'); ?>'];
Date.monthNames = ['<?php _e('January', 'installations-manager'); ?>', '<?php _e('February', 'installations-manager'); ?>', '<?php _e('March', 'installations-manager'); ?>', '<?php _e('April', 'installations-manager'); ?>', '<?php _e('May', 'installations-manager'); ?>', '<?php _e('June', 'installations-manager'); ?>', '<?php _e('July', 'installations-manager'); ?>', '<?php _e('August', 'installations-manager'); ?>', '<?php _e('September', 'installations-manager'); ?>', '<?php _e('October', 'installations-manager'); ?>', '<?php _e('November', 'installations-manager'); ?>', '<?php _e('December', 'installations-manager'); ?>'];
Date.abbrMonthNames = ['<?php _e('Jan', 'installations-manager'); ?>', '<?php _e('Feb', 'installations-manager'); ?>', '<?php _e('Mar', 'installations-manager'); ?>', '<?php _e('Apr', 'installations-manager'); ?>', '<?php _e('May', 'installations-manager'); ?>', '<?php _e('Jun', 'installations-manager'); ?>', '<?php _e('Jul', 'installations-manager'); ?>', '<?php _e('Aug', 'installations-manager'); ?>', '<?php _e('Sep', 'installations-manager'); ?>', '<?php _e('Oct', 'installations-manager'); ?>', '<?php _e('Nov', 'installations-manager'); ?>', '<?php _e('Dec', 'installations-manager'); ?>'];
$.dpText = {
TEXT_PREV_YEAR : '<?php _e('Previous year', 'installations-manager'); ?>',
TEXT_PREV_MONTH : '<?php _e('Previous month', 'installations-manager'); ?>',
TEXT_NEXT_YEAR : '<?php _e('Next year', 'installations-manager'); ?>',
TEXT_NEXT_MONTH : '<?php _e('Next month', 'installations-manager'); ?>',
TEXT_CLOSE : '<?php _e('Close', 'installations-manager'); ?>',
TEXT_CHOOSE_DATE : '<?php _e('Choose a date', 'installations-manager'); ?>',
DATE_PICKER_ALT : '<?php _e('Date', 'installations-manager'); ?>',
DATE_PICKER_URL : '<?php echo INSTALLATIONS_MANAGER_URL; ?>images/date-picker.png',
HEADER_FORMAT : 'mmmm yyyy'
}; $(function(){ $('.date-pick').datePicker({startDate:'2000-01-01'}); });
</script>
<?php }


if (((!isset($_GET['action'])) || ($_GET['action'] != 'delete'))
 && ($_GET['page'] != 'installations-manager')
 && ($_GET['page'] != 'installations-manager-back-office')) {
add_action('admin_head', 'installations_manager_date_picker_css');
add_action('admin_footer', 'installations_jquery_js');
add_action('admin_footer', 'installations_manager_date_picker_js'); }