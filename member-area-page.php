<?php global $wpdb;
$back_office_options = get_option('membership_manager_back_office');
$is_category = (strstr($_GET['page'], 'category'));
if ($is_category) { $admin_page = 'member_area_category'; $table_slug = 'members_areas_categories'; $attribute = 'category'; }
else { $admin_page = 'member_area'; $table_slug = 'members_areas'; $attribute = 'id'; }

if ((isset($_GET['id'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if ($is_category) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."membership_manager_members_areas_categories WHERE id = '".$_GET['id']."'", OBJECT);
foreach (array('members_areas', 'members_areas_categories') as $table) {
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_".$table." SET category_id = ".$category->category_id." WHERE category_id = '".$_GET['id']."'"); } }
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."membership_manager_".$table_slug." WHERE id = '".$_GET['id']."'"); } ?>
<div class="wrap">
<div id="poststuff">
<?php membership_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.($is_category ? __('Category deleted.', 'membership-manager') : __('Member area deleted.', 'membership-manager')).'</strong></p></div>'; } ?>
<?php membership_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php echo ($is_category ? __('Do you really want to permanently delete this category?', 'membership-manager') : __('Do you really want to permanently delete this member_area?', 'membership-manager')); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'membership-manager'); ?>" />
</div>
<div class="clear"></div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
add_action('admin_footer', 'membership_date_picker_js');
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('html_entity_decode', $_POST);
if ($_POST[$admin_page.'_page_summary_displayed'] != 'yes') { $_POST[$admin_page.'_page_summary_displayed'] = 'no'; }
$back_office_options[$admin_page.'_page_summary_displayed'] = $_POST[$admin_page.'_page_summary_displayed'];
$back_office_options[$admin_page.'_page_undisplayed_modules'] = array();
foreach ($modules[$admin_page] as $key => $value) {
if (($_POST[$admin_page.'_page_'.str_replace('-', '_', $key).'_module_displayed'] != 'yes') && ($value['required'] != 'yes')) { $back_office_options[$admin_page.'_page_undisplayed_modules'][] = $key; }
if (is_array($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
if (($_POST[$admin_page.'_page_'.str_replace('-', '_', $module_key).'_module_displayed'] != 'yes') && ($module_value['required'] != 'yes')) { $back_office_options[$admin_page.'_page_undisplayed_modules'][] = $module_key; } } } }
update_option('membership_manager_back_office', $back_office_options);

if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$keywords = explode(',', $_POST['keywords']);
for ($i = 0; $i < count($keywords); $i++) { $keywords[$i] = strtolower(trim($keywords[$i])); }
sort($keywords);
foreach ($keywords as $keyword) { $keywords_list .= $keyword.', '; }
$_POST['keywords'] = substr($keywords_list, 0, -2);
$members_areas = array_unique(preg_split('#[^0-9]#', $_POST['members_areas']));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['members_areas'] = substr($members_areas_list, 0, -2);
if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s'); }
else {
$d = preg_split('#[^0-9]#', $_POST['date']);
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }

if (!isset($_GET['id'])) {
if ($_POST['name'] == '') { $error .= ' '.__('Please fill out the required fields.', 'membership-manager'); }
elseif ($is_category) {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."membership_manager_members_areas_categories WHERE name = '".$_POST['name']."'", OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'membership-manager'); } }
if ($error == '') {
if ($is_category) { $result = false; }
else { $result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."membership_manager_members_areas WHERE name = '".$_POST['name']."' AND date = '".$_POST['date']."'", OBJECT); }
if (!$result) {
$updated = true;
include 'tables.php';
foreach ($tables[$table_slug] as $key => $value) { $keys_list .= $key.","; $values_list .= "'".$_POST[$key]."',"; }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."membership_manager_".$table_slug." (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")"); } } }

if (isset($_GET['id'])) {
$updated = true;
if ($_POST['name'] != '') {
if (!$is_category) { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_".$table_slug." SET name = '".$_POST['name']."' WHERE id = '".$_GET['id']."'"); }
else {
$result = $wpdb->get_results("SELECT name FROM ".$wpdb->prefix."membership_manager_members_areas_categories WHERE name = '".$_POST['name']."' AND id != '".$_GET['id']."'", OBJECT);
if ($result) { $error .= ' '.__('This name is not available.', 'membership-manager'); }
else { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members_areas_categories SET name = '".$_POST['name']."' WHERE id = '".$_GET['id']."'"); } } }
include 'tables.php';
foreach ($tables[$table_slug] as $key => $value) { switch ($key) {
case 'id': case 'name': break;
default: $list .= $key." = '".$_POST[$key]."',"; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_".$table_slug." SET ".substr($list, 0, -1)." WHERE id = '".$_GET['id']."'"); } }

if (isset($_GET['id'])) {
$item_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_".$table_slug." WHERE id = '".$_GET['id']."'", OBJECT);
if ($item_data) { foreach ($item_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page='.$_GET['page']); exit(); } }

$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('htmlspecialchars', $_POST);
foreach ($_POST as $key => $value) {
$_POST[$key] = str_replace('&amp;amp;', '&amp;', $value);
if ($value == '0000-00-00 00:00:00') { $_POST[$key] = ''; } }
$undisplayed_modules = (array) $back_office_options[$admin_page.'_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff">
<?php membership_manager_pages_top($back_office_options); ?>
<?php if ($updated) { echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? ($is_category ? __('Category updated.', 'membership-manager') : __('Member area updated.', 'membership-manager')) : ($is_category ? __('Category saved.', 'membership-manager') : __('Member area saved.', 'membership-manager'))).'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php membership_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Fields marked with * are required.', 'membership-manager'); ?> 
<?php if ($_POST['category_id'] > 0) { _e('You can apply the default option of the category by leaving the corresponding field blank.', 'membership-manager'); } ?></p>
<?php membership_manager_pages_summary($back_office_options); ?>

<div class="postbox"<?php if (in_array('general-informations', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="general-informations"><strong><?php echo $modules[$admin_page]['general-informations']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if ($_POST['category_id'] > 0) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager-member-area-category&amp;id=<?php echo $_POST['category_id']; ?>#general-informations">
<?php ($is_category ? _e('Click here to configure the default options of the parent category.', 'membership-manager') : _e('Click here to configure the default options of the category.', 'membership-manager')); ?></a></span></td></tr>
<?php } ?>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'membership-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'membership-manager').'</span></td></tr>'; } ?>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_areas_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="category_id"><?php echo ($is_category ? __('Parent category', 'membership-manager') : __('Category', 'membership-manager')); ?></label></strong></th>
<td><select name="category_id" id="category_id">
<option value="0"<?php if ($_POST['category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'membership-manager'); ?></option>
<?php foreach ($categories as $category) {
if ((!$is_category) || (!in_array($_GET['id'], members_areas_categories_list($category->id)))) {
echo '<option value="'.$category->id.'"'.($_POST['category_id'] == $category->id ? ' selected="selected"' : '').'>'.$category->name.'</option>'."\n"; } } ?>
</select>
<span class="description"><?php ($is_category ? _e('The options of this category will apply by default to the category.', 'membership-manager') : _e('The options of this category will apply by default to the member area.', 'membership-manager')); ?></span>
<?php if ($_POST['category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area-category&amp;id='.$_POST['category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-area-category&amp;id='.$_POST['category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['name'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="name"><?php _e('Name', 'membership-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="name" id="name" rows="1" cols="50"><?php echo $_POST['name']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="description"><?php _e('Description', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="description" id="description" rows="1" cols="75"><?php echo $_POST['description']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="keywords"><?php _e('Keywords', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="keywords" id="keywords" rows="1" cols="75"><?php echo $_POST['keywords']; ?></textarea><br />
<span class="description"><?php _e('Separate the keywords with commas.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="url"><?php _e('URL', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="url" id="url" rows="1" cols="75"><?php echo $_POST['url']; ?></textarea> 
<?php $url = htmlspecialchars(member_area_data(array(0 => 'url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'membership-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="instructions"><?php _e('Instructions to the member', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="instructions" id="instructions" rows="9" cols="75"><?php echo $_POST['instructions']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes to display informations about the member and the member area.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="members_areas"><?php _e('Included members areas', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="members_areas" id="members_areas" rows="1" cols="75"><?php echo $_POST['members_areas']; ?></textarea>
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#included-members-areas"><?php _e('More informations', 'membership-manager'); ?></a><br />
<?php _e('Separate the IDs of the members areas with commas.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Creation date', 'membership-manager'); ?></label></strong></th>
<td><input class="date-pick" style="margin-right: 0.5em;" type="text" name="date" id="date" size="20" value="<?php echo (isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET)); ?>" /></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('registration', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration"><strong><?php echo $modules[$admin_page]['registration']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager<?php echo ($_POST['category_id'] == 0 ? '' : '-member-area-category&amp;id='.$_POST['category_id'].'#registration'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'membership-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'membership-manager') : _e('Click here to configure the default options of the category.', 'membership-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_url"><?php _e('Registration confirmation URL', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_url" id="registration_confirmation_url" rows="1" cols="75"><?php echo $_POST['registration_confirmation_url']; ?></textarea> 
<?php $url = htmlspecialchars(member_area_data(array(0 => 'registration_confirmation_url', 'part' => 1, $attribute => $_GET['id']))); if ($url != '') { ?><a style="vertical-align: 25%;" href="<?php echo $url; ?>"><?php _e('Link', 'membership-manager'); ?></a><?php } ?><br />
<span class="description" style="vertical-align: 25%;"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="members_initial_category_id"><?php _e('Members initial category', 'membership-manager'); ?></label></strong></th>
<td><select name="members_initial_category_id" id="members_initial_category_id">
<option value=""<?php if ($_POST['members_initial_category_id'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'membership-manager'); ?></option>
<option value="0"<?php if ($_POST['members_initial_category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'membership-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($_POST['members_initial_category_id'] == $category->id ? ' selected="selected"' : '').'>'.$category->name.'</option>'."\n"; } ?>
</select>
<span class="description"><?php _e('Category assigned to members upon their registration', 'membership-manager'); ?></span>
<?php if ($_POST['members_initial_category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['members_initial_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$_POST['members_initial_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="members_initial_status"><?php _e('Members initial status', 'membership-manager'); ?></label></strong></th>
<td><select name="members_initial_status" id="members_initial_status">
<option value=""<?php if ($_POST['members_initial_status'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'membership-manager'); ?></option>
<option value="active"<?php if ($_POST['members_initial_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'membership-manager'); ?></option>
<option value="inactive"<?php if ($_POST['members_initial_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'membership-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to members upon their registration', 'membership-manager'); ?><br />
<?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('registration-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-confirmation-email"><strong><?php echo $modules[$admin_page]['registration-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager<?php echo ($_POST['category_id'] == 0 ? '#registration-confirmation-email' : '-member-area-category&amp;id='.$_POST['category_id'].'#registration-confirmation-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'membership-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'membership-manager') : _e('Click here to configure the default options of the category.', 'membership-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sent"><?php _e('Send a registration confirmation email', 'membership-manager'); ?></label></strong></th>
<td><select name="registration_confirmation_email_sent" id="registration_confirmation_email_sent">
<option value=""<?php if ($_POST['registration_confirmation_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'membership-manager'); ?></option>
<option value="yes"<?php if ($_POST['registration_confirmation_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'membership-manager'); ?></option>
<option value="no"<?php if ($_POST['registration_confirmation_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'membership-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_sender" id="registration_confirmation_email_sender" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_receiver" id="registration_confirmation_email_receiver" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_subject" id="registration_confirmation_email_subject" rows="1" cols="75"><?php echo $_POST['registration_confirmation_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_confirmation_email_body" id="registration_confirmation_email_body" rows="15" cols="75"><?php echo $_POST['registration_confirmation_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member and the member area.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('registration-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-notification-email"><strong><?php echo $modules[$admin_page]['registration-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager<?php echo ($_POST['category_id'] == 0 ? '#registration-notification-email' : '-member-area-category&amp;id='.$_POST['category_id'].'#registration-notification-email'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'membership-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'membership-manager') : _e('Click here to configure the default options of the category.', 'membership-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sent"><?php _e('Send a registration notification email', 'membership-manager'); ?></label></strong></th>
<td><select name="registration_notification_email_sent" id="registration_notification_email_sent">
<option value=""<?php if ($_POST['registration_notification_email_sent'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'membership-manager'); ?></option>
<option value="yes"<?php if ($_POST['registration_notification_email_sent'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'membership-manager'); ?></option>
<option value="no"<?php if ($_POST['registration_notification_email_sent'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'membership-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_sender" id="registration_notification_email_sender" rows="1" cols="75"><?php echo $_POST['registration_notification_email_sender']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_receiver" id="registration_notification_email_receiver" rows="1" cols="75"><?php echo $_POST['registration_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_subject" id="registration_notification_email_subject" rows="1" cols="75"><?php echo $_POST['registration_notification_email_subject']; ?></textarea><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_notification_email_body" id="registration_notification_email_body" rows="15" cols="75"><?php echo $_POST['registration_notification_email_body']; ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member and the member area.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules[$admin_page]['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager<?php echo ($_POST['category_id'] == 0 ? '#autoresponders' : '-member-area-category&amp;id='.$_POST['category_id'].'#autoresponders'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'membership-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'membership-manager') : _e('Click here to configure the default options of the category.', 'membership-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_subscribed_to_autoresponder"><?php _e('Subscribe the member to an autoresponder list', 'membership-manager'); ?></label></strong></th>
<td><select name="member_subscribed_to_autoresponder" id="member_subscribed_to_autoresponder">
<option value=""<?php if ($_POST['member_subscribed_to_autoresponder'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'membership-manager'); ?></option>
<option value="yes"<?php if ($_POST['member_subscribed_to_autoresponder'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'membership-manager'); ?></option>
<option value="no"<?php if ($_POST['member_subscribed_to_autoresponder'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'membership-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_autoresponder"><?php _e('Autoresponder', 'membership-manager'); ?></label></strong></th>
<td><select name="member_autoresponder" id="member_autoresponder">
<?php include 'autoresponders.php';
$autoresponder = do_shortcode($_POST['member_autoresponder']);
echo '<option value=""'.($autoresponder == '' ? ' selected="selected"' : '').'>'.__('Default option', 'membership-manager').'</option>'."\n";
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_autoresponder_list"><?php _e('List', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="member_autoresponder_list" id="member_autoresponder_list" rows="1" cols="50"><?php echo $_POST['member_autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#autoresponders"><?php _e('More informations', 'membership-manager'); ?></a><br />
<?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="custom-instructions"><strong><?php echo $modules[$admin_page]['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="admin.php?page=membership-manager<?php echo ($_POST['category_id'] == 0 ? '#custom-instructions' : '-member-area-category&amp;id='.$_POST['category_id'].'#custom-instructions'); ?>">
<?php ($_POST['category_id'] == 0 ? _e('Click here to configure the default options.', 'membership-manager') : ($is_category ? _e('Click here to configure the default options of the parent category.', 'membership-manager') : _e('Click here to configure the default options of the category.', 'membership-manager'))); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions_executed"><?php _e('Execute custom instructions', 'membership-manager'); ?></label></strong></th>
<td><select name="registration_custom_instructions_executed" id="registration_custom_instructions_executed">
<option value=""<?php if ($_POST['registration_custom_instructions_executed'] == '') { echo ' selected="selected"'; } ?>><?php _e('Default option', 'membership-manager'); ?></option>
<option value="yes"<?php if ($_POST['registration_custom_instructions_executed'] == 'yes') { echo ' selected="selected"'; } ?>><?php _e('Yes', 'membership-manager'); ?></option>
<option value="no"<?php if ($_POST['registration_custom_instructions_executed'] == 'no') { echo ' selected="selected"'; } ?>><?php _e('No', 'membership-manager'); ?></option>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_custom_instructions" id="registration_custom_instructions" rows="10" cols="75"><?php echo $_POST['registration_custom_instructions']; ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span><br />
<span class="description"><?php _e('Leave this field blank to apply the default option.', 'membership-manager'); ?></span></td></tr>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="'.__('Update').'" /></td></tr>'; } ?>
</tbody></table>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ?  _e('Save Changes', 'membership-manager') : ($is_category ? _e('Save Category', 'membership-manager') : _e('Save Member Area', 'membership-manager'))); ?>" /></p>
<?php if ($is_category) { $module = 'member-area-category-page'; } else { $module = 'member-area-page'; }
membership_manager_pages_module($back_office_options, $module, $undisplayed_modules); ?>
</form>
</div>
</div>
<?php }