<?php global $wpdb; $error = '';
$options = (array) get_option('installations_manager_back_office');
extract(installations_manager_pages_links_markups($options));
include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php';
$max_links = count($admin_links);
$max_menu_items = count($admin_pages);

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!installations_manager_user_can($options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'installations-manager'); }
else {
include INSTALLATIONS_MANAGER_PATH.'/initial-options.php';
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
foreach (array(
'custom_icon_used',
'links_displayed',
'menu_displayed',
'title_displayed') as $field) { if (!isset($_POST[$field])) { $_POST[$field] = 'no'; } }
foreach (array(
'back_office',
'options',
'website') as $page) { update_installations_manager_back_office($options, $page); }
$_POST['minimum_roles'] = array();
foreach (array('manage', 'view') as $key) { $_POST['minimum_roles'][$key] = $_POST[$key.'_minimum_role']; }
if (isset($_POST['reset_links'])) {
foreach (array('links', 'displayed_links') as $field) { $_POST[$field] = $initial_options['back_office'][$field]; } }
else {
$_POST['displayed_links'] = array();
for ($i = 0; $i < $max_links; $i++) {
$_POST['links'][$i] = $_POST['link'.$i];
if (isset($_POST['link'.$i.'_displayed'])) { $_POST['displayed_links'][] = $i; } } }
if (isset($_POST['reset_menu_items'])) {
foreach (array('menu_items', 'menu_displayed_items') as $field) { $_POST[$field] = $initial_options['back_office'][$field]; } }
else {
$_POST['menu_displayed_items'] = array();
for ($i = 0; $i < $max_menu_items; $i++) {
$_POST['menu_items'][$i] = $_POST['menu_item'.$i];
if (isset($_POST['menu_item'.$i.'_displayed'])) { $_POST['menu_displayed_items'][] = $i; } } }
foreach (array('default_options', 'documentations', 'ids_fields', 'pages_modules', 'urls_fields') as $string) {
$_POST[$string.'_links_target'] = (isset($_POST[$string.'_links_targets_opened_in_new_tab']) ? '_blank' : '_self'); }
foreach ($initial_options['back_office'] as $key => $value) {
if ((isset($_POST[$key])) && ($_POST[$key] != '')) { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('installations_manager_back_office', $options); } }

$undisplayed_modules = (array) $options['back_office_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff" style="padding-top: 0;">
<?php installations_manager_pages_top($options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.', 'installations-manager').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php installations_manager_pages_menu($options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php installations_manager_pages_summary($options); ?>

<div class="postbox" id="capabilities-module"<?php if (in_array('capabilities', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="capabilities"><strong><?php echo $modules['back_office']['capabilities']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="view_minimum_role"><?php _e('Access', 'installations-manager'); ?></label></strong></th>
<td><select name="view_minimum_role" id="view_minimum_role">
<?php foreach ($roles as $key => $value) {
echo '<option value="'.$key.'"'.($options['minimum_roles']['view'] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; } ?>
</select> <span class="description"><?php _e('Minimum role to access the interface of Installations Manager', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="manage_minimum_role"><?php _e('Management', 'installations-manager'); ?></label></strong></th>
<td><select name="manage_minimum_role" id="manage_minimum_role">
<?php foreach ($roles as $key => $value) {
echo '<option value="'.$key.'"'.($options['minimum_roles']['manage'] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; } ?>
</select> <span class="description"><?php _e('Minimum role to change options and add, edit or delete items of Installations Manager', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="icon-module"<?php if (in_array('icon', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="icon"><strong><?php echo $modules['back_office']['icon']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="custom_icon_used" id="custom_icon_used" value="yes"<?php if ($options['custom_icon_used'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Use a custom icon', 'installations-manager'); ?></label>
 <span class="description" style="vertical-align: -5%;"><?php _e('Icon displayed in the admin menu of WordPress', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="custom_icon_url"><?php _e('Icon URL', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="custom_icon_url" id="custom_icon_url" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['custom_icon_url']; ?></textarea> 
<span style="vertical-align: 25%;"><a target="<?php echo $options['urls_fields_links_target']; ?>" href="<?php echo htmlspecialchars(format_url(do_shortcode($options['custom_icon_url']))); ?>"><?php _e('Link', 'installations-manager'); ?></a>
<?php if (current_user_can('upload_files')) { echo ' | <a target="'.$options['urls_fields_links_target'].'" href="media-new.php">'.__('Upload an image', 'installations-manager').'</a>'; } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="top-module"<?php if (in_array('top', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="top"><strong><?php echo $modules['back_office']['top']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="title_displayed" id="title_displayed" value="yes"<?php if ($options['title_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the title', 'installations-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="title"><?php _e('Title', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="title" id="title" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/30)))+'em';" onblur="this.style.height = '1.75em';" cols="25"><?php echo $options['title']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="links_displayed" id="links_displayed" value="yes"<?php if ($options['links_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the links', 'installations-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Links', 'installations-manager'); ?></strong></th>
<td><input type="hidden" name="submit" value="true" /><input type="submit" class="button-secondary" name="reset_links" value="<?php _e('Reset the links', 'installations-manager'); ?>" /><br />
<?php $displayed_links = (array) $options['displayed_links'];
for ($i = 0; $i < $max_links; $i++) {
echo '<label>'.__('Link', 'installations-manager').' '.($i + 1).($i < 9 ? '&nbsp;&nbsp;': '').' <select name="link'.$i.'" id="link'.$i.'">';
foreach ($admin_links as $key => $value) { echo '<option value="'.$key.'"'.($options['links'][$i] == $key ? ' selected="selected"' : '').'>'.$value['name'].'</option>'."\n"; }
echo '</select></label>
<label><input type="checkbox" name="link'.$i.'_displayed" id="link'.$i.'_displayed" value="yes"'.(!in_array($i, $displayed_links) ? '' : ' checked="checked"').' /> '.__('Display', 'installations-manager').'</label><br />'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="menu-module"<?php if (in_array('menu', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="menu"><strong><?php echo $modules['back_office']['menu']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="menu_displayed" id="menu_displayed" value="yes"<?php if ($options['menu_displayed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Display the menu', 'installations-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Pages', 'installations-manager'); ?></strong></th>
<td><input type="hidden" name="submit" value="true" /><input type="submit" class="button-secondary" name="reset_menu_items" value="<?php _e('Reset the pages', 'installations-manager'); ?>" /><br />
<?php $menu_displayed_items = (array) $options['menu_displayed_items'];
for ($i = 0; $i < $max_menu_items; $i++) {
echo '<label>'.__('Page', 'installations-manager').' '.($i + 1).($i < 9 ? '&nbsp;&nbsp;': '').' <select name="menu_item'.$i.'" id="menu_item'.$i.'">';
foreach ($admin_pages as $key => $value) { echo '<option value="'.$key.'"'.($options['menu_items'][$i] == $key ? ' selected="selected"' : '').'>'.$value['menu_title'].'</option>'."\n"; }
echo '</select></label>
<label><input type="checkbox" name="menu_item'.$i.'_displayed" id="menu_item'.$i.'_displayed" value="yes"'.(!in_array($i, $menu_displayed_items) ? '' : ' checked="checked"').' /> '.__('Display', 'installations-manager').'</label><br />'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="links-module"<?php if (in_array('links', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="links"><strong><?php echo $modules['back_office']['links']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><?php _e('Open in a new tab the targets of the links', 'installations-manager'); ?></strong></th>
<td><?php foreach (array(
'documentations' => __('pointing to the documentation', 'installations-manager'),
'default_options' => __('allowing to configure the default options', 'installations-manager'),
'ids_fields' => __('below the fields allowing to enter an ID', 'installations-manager'),
'urls_fields' => __('next to the fields allowing to enter a URL', 'installations-manager'),
'pages_modules' => __('at the top of the modules of this page', 'installations-manager')) as $key => $value) {
$name = $key.'_links_targets_opened_in_new_tab';
echo '<label><input type="checkbox" name="'.$name.'" id="'.$name.'" value="yes"'.($options[$key.'_links_target'] != '_blank' ? '' : ' checked="checked"').' /> '.$value.'</label><br />'; } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div></div>

<?php foreach (array(
'options-page',
'website-page',
'back-office-page') as $module) { installations_manager_pages_module($options, $module, $undisplayed_modules); } ?>

<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes', 'installations-manager'); ?>" /></p>
</form>
</div>
</div>

<script type="text/javascript">
var anchor = window.location.hash;
<?php foreach ($modules['back_office'] as $key => $value) {
echo "if (anchor == '#".$key."') { document.getElementById('".$key."-module').style.display = 'block'; }\n";
if (isset($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
echo "if (anchor == '#".$module_key."') {
document.getElementById('".$key."-module').style.display = 'block';
document.getElementById('".$module_key."-module').style.display = 'block'; }\n"; } } } ?>
</script>