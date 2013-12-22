<?php global $wpdb; $error = '';
$back_office_options = (array) get_option('installations_manager_back_office');
extract(installations_manager_pages_links_markups($back_office_options));
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$current_time = time();

if ((isset($_GET['id'])) && (isset($_GET['action'])) && ($_GET['action'] == 'delete')) {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!installations_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'installations-manager'); }
else {
$results = $wpdb->query("DELETE FROM ".$wpdb->prefix."installations_manager_websites WHERE id = ".$_GET['id']);
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."installations_manager_websites ORDER BY id DESC LIMIT 1", OBJECT);
if (!$result) { $results = $wpdb->query("ALTER TABLE ".$wpdb->prefix."installations_manager_websites AUTO_INCREMENT = 1"); }
elseif ($result->id < $_GET['id']) {
$results = $wpdb->query("ALTER TABLE ".$wpdb->prefix."installations_manager_websites AUTO_INCREMENT = ".($result->id + 1)); } } } ?>
<div class="wrap">
<div id="poststuff" style="padding-top: 0;">
<?php installations_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.__('Website deleted.', 'installations-manager').'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=installations-manager-websites"\', 2000);</script>'; } ?>
<?php installations_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete this website?', 'installations-manager'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'installations-manager'); ?>" />
</div>
<div class="clear"></div>
</form><?php } ?>
</div>
</div><?php }

else {
$plugins = array(
'Commerce Manager',
'Affiliation Manager',
'Membership Manager',
'Optin Manager',
'Contact Manager');
include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php'; include INSTALLATIONS_MANAGER_PATH.'/tables.php';
foreach ($tables['websites'] as $key => $value) { if (!isset($_POST[$key])) { $_POST[$key] = ''; } }
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!installations_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'installations-manager'); }
else {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_installations_manager_back_office($back_office_options, 'website');

if ($_POST['date'] == '') {
$_POST['date'] = date('Y-m-d H:i:s', $current_time + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s', $current_time); }
else {
$d = preg_split('#[^0-9]#', $_POST['date'], 0, PREG_SPLIT_NO_EMPTY);
for ($i = 0; $i < 6; $i++) { $d[$i] = (int) (isset($d[$i]) ? $d[$i] : ($i < 3 ? 1 : 0)); }
$time = mktime($d[3], $d[4], $d[5], $d[1], $d[2], $d[0]);
$_POST['date'] = date('Y-m-d H:i:s', $time);
$_POST['date_utc'] = date('Y-m-d H:i:s', $time - 3600*UTC_OFFSET); }
$plugins_list = '';
foreach ($plugins as $plugin) {
$field = str_replace('-', '_', format_nice_name($plugin)).'_installations_dates';
$array = explode(',', $_POST[$field]);
$dates = array();
foreach ($array as $string) {
$string = explode('(', trim(str_replace(')', '', $string)));
if (count($string) == 2) { $key = trim($string[0]); $dates[$key] = trim($string[1]); } }
$_POST[$field] = serialize($dates);
$plugins_list .= (((strstr($_POST['plugins'], $plugin)) || ($dates != array())) ? $plugin.', ' : ''); }
$_POST['plugins'] = substr($plugins_list, 0, -2);

if (!isset($_GET['id'])) {
if ($_POST['url'] == '') { $error .= ' '.__('Please fill out the required fields.', 'installations-manager'); }
else {
$result = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix."installations_manager_websites WHERE url = '".$_POST['url']."'", OBJECT);
if ($result) { $error .= ' '.__('This URL is not available.', 'installations-manager'); } }
if ($error == '') {
$updated = true;
$sql = installations_sql_array($tables['websites'], $_POST);
$keys_list = ''; $values_list = '';
foreach ($tables['websites'] as $key => $value) { if ($key != 'id') { $keys_list .= $key.","; $values_list .= $sql[$key].","; } }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."installations_manager_websites (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")"); } }

if (isset($_GET['id'])) {
$updated = true;
if ($_POST['url'] != '') {
$result = $wpdb->get_results("SELECT url FROM ".$wpdb->prefix."installations_manager_websites WHERE url = '".$_POST['url']."' AND id != ".$_GET['id'], OBJECT);
if ($result) { $error .= ' '.__('This URL is not available.', 'installations-manager'); }
else { $results = $wpdb->query("UPDATE ".$wpdb->prefix."installations_manager_websites SET url = '".$_POST['url']."' WHERE id = ".$_GET['id']); } }
$sql = installations_sql_array($tables['websites'], $_POST);
$list = '';
foreach ($tables['websites'] as $key => $value) { switch ($key) {
case 'id': case 'url': break;
default: $list .= $key." = ".$sql[$key].","; } }
$results = $wpdb->query("UPDATE ".$wpdb->prefix."installations_manager_websites SET ".substr($list, 0, -1)." WHERE id = ".$_GET['id']); } } }

if (isset($_GET['id'])) {
$website_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."installations_manager_websites WHERE id = ".$_GET['id'], OBJECT);
if ($website_data) {
$GLOBALS['website_data'] = (array) $website_data;
foreach ($website_data as $key => $value) { $_POST[$key] = $value; } }
elseif (!headers_sent()) { header('Location: admin.php?page=installations-manager-websites'); exit(); }
else { echo '<script type="text/javascript">window.location = "admin.php?page=installations-manager-websites";</script>'; } }

foreach ($_POST as $key => $value) {
if (is_string($value)) {
$_POST[$key] = str_replace(array('&amp;amp;', '&amp;apos;', '&amp;quot;'), array('&amp;', '&apos;', '&quot;'), htmlspecialchars(stripslashes($value)));
if (($value == '0000-00-00 00:00:00') && ((substr($key, -4) == 'date') || (substr($key, -8) == 'date_utc'))) { $_POST[$key] = ''; } } }
$undisplayed_modules = (array) $back_office_options['website_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff" style="padding-top: 0;">
<?php installations_manager_pages_top($back_office_options); ?>
<?php if ((isset($updated)) && ($updated)) {
echo '<div class="updated"><p><strong>'.(isset($_GET['id']) ? __('Website updated.', 'installations-manager') : __('Website saved.', 'installations-manager')).'</strong></p></div>
'.(isset($_GET['id']) ? '' : '<script type="text/javascript">setTimeout(\'window.location = "admin.php?page=installations-manager-websites"\', 2000);</script>'); } ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php installations_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('Only fields marked with * are required.', 'installations-manager'); ?> 
<?php installations_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="general-informations-module"<?php if (in_array('general-informations', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="general-informations"><strong><?php echo $modules['website']['general-informations']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<?php if (isset($_GET['id'])) { echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="id">'.__('ID', 'installations-manager').'</label></strong></th>
<td><input type="text" name="id" id="id" size="10" value="'.$_GET['id'].'" disabled="disabled" /> <span class="description">'.__('The ID can not be changed.', 'installations-manager').'</span><br />
<a style="text-decoration: none;" '.$ids_fields_links_markup.' href="admin.php?page=installations-manager-website&amp;id='.$_GET['id'].'&amp;action=delete" class="delete">'.__('Delete', 'installations-manager').'</a></td></tr>'; } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="name"><?php _e('Name', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="name" id="name" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/60)))+'em';" onblur="this.style.height = '1.75em';" cols="50"><?php echo $_POST['name']; ?></textarea> 
<?php $url = htmlspecialchars(website_data(array(0 => 'url', 'part' => 1, 'id' => (isset($_GET['id']) ? $_GET['id'] : 0)))); if ($url != '') { ?><a style="vertical-align: 25%;" <?php echo $urls_fields_links_markup; ?> href="<?php echo $url; ?>"><?php _e('Link', 'installations-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;<?php if ((!isset($_GET['id'])) && (isset($_POST['submit'])) && ($_POST['url'] == '')) { echo ' color: #c00000;'; } ?>"><strong><label for="url"><?php _e('URL', 'installations-manager'); ?></label></strong> *</th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="url" id="url" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $_POST['url']; ?></textarea> 
<?php $url = htmlspecialchars(website_data(array(0 => 'url', 'part' => 1, 'id' => (isset($_GET['id']) ? $_GET['id'] : 0)))); if ($url != '') { ?><a style="vertical-align: 25%;" <?php echo $urls_fields_links_markup; ?> href="<?php echo $url; ?>"><?php _e('Link', 'installations-manager'); ?></a><?php } ?></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="language_code"><?php _e('Language code', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="language_code" id="language_code" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/60)))+'em';" onblur="this.style.height = '1.75em';" cols="50"><?php echo $_POST['language_code']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="date"><?php _e('Date', 'installations-manager'); ?></label></strong></th>
<td><input class="date-pick" type="text" name="date" id="date" size="20" value="<?php echo ($_POST['date'] != '' ? $_POST['date'] : date('Y-m-d H:i:s', $current_time + 3600*UTC_OFFSET)); ?>" /></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th><td><input type="submit" class="button-secondary" name="submit" value="<?php echo (isset($_GET['id']) ? __('Update', 'installations-manager') : __('Save', 'installations-manager')); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="installations-module"<?php if (in_array('installations', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="installations"><strong><?php echo $modules['website']['installations']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="plugins"><?php _e('Plugins', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="plugins" id="plugins" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $_POST['plugins']; ?></textarea><br />
<span class="description"><?php _e('Separate the plugins with commas.', 'installations-manager'); ?></span></td></tr>
<?php foreach ($plugins as $plugin) {
$field = str_replace('-', '_', format_nice_name($plugin)).'_installations_dates';
$_POST[$field] = '';
$dates = (array) unserialize(htmlspecialchars_decode($_POST[$field]));
foreach ($dates as $key => $date) { $_POST[$field] .= $key." (".$date."),\n"; }
echo '<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="'.$field.'">'.$plugin.'</label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 50%;" name="'.$field.'" id="'.$field.'" rows="2" cols="50">'.$_POST[$field].'</textarea>
<span class="description">'.__('Installations dates (UTC) for each version of this plugin', 'installations-manager').'</span></td></tr>'; } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th><td><input type="submit" class="button-secondary" name="submit" value="<?php echo (isset($_GET['id']) ? __('Update', 'installations-manager') : __('Save', 'installations-manager')); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php (isset($_GET['id']) ? _e('Save Changes', 'installations-manager') : _e('Save Website', 'installations-manager')); ?>" /></p>
<?php installations_manager_pages_module($back_office_options, 'website-page', $undisplayed_modules); ?>
</form>
</div>
</div>

<script type="text/javascript">
var anchor = window.location.hash;
<?php foreach ($modules['website'] as $key => $value) {
echo "if (anchor == '#".$key."') { document.getElementById('".$key."-module').style.display = 'block'; }\n";
if (isset($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
echo "if (anchor == '#".$module_key."') {
document.getElementById('".$key."-module').style.display = 'block';
document.getElementById('".$module_key."-module').style.display = 'block'; }\n"; } } } ?>
</script>
<?php }