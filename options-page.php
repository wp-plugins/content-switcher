<?php if ($_GET['action'] == 'uninstall') {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) { delete_option('content_switcher'); } ?>
<div class="wrap">
<h2>Content Switcher</h2>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Options deleted.', 'content-switcher').'</strong></p></div>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete the options of Content Switcher?', 'content-switcher'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'content-switcher'); ?>" />
</div>
</form><?php } ?>
</div><?php }

else {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
include 'initial-options.php';
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
foreach (array(
'admin_tracked',
'author_tracked',
'contributor_tracked',
'editor_tracked',
'javascript_enabled',
'subscriber_tracked',
'visitor_tracked') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach ($initial_options as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('content_switcher', $options); }
else { $options = (array) get_option('content_switcher'); }

$options = array_map('htmlspecialchars', $options); ?>

<div class="wrap">
<h2>Content Switcher</h2>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<p style="margin: 1.5em"><a href="http://www.kleor-editions.com/content-switcher"><?php _e('Documentation', 'content-switcher'); ?></a></p>
<h3><?php _e('Options', 'content-switcher'); ?></h3>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<p><?php foreach (array(
'analytics' => __('Google Analytics Account Tracking ID', 'content-switcher'),
'optimizer' => __('Google Optimizer Account Tracking ID', 'content-switcher')) as $key => $value) {
echo '<label>'.$value.': <input type="text" name="'.$key.'_tracking_id" id="'.$key.'_tracking_id" value="'.$options[$key.'_tracking_id'].'" size="16" /></label>
<a href="http://www.kleor-editions.com/content-switcher/#tracking-id">'.__('More informations', 'content-switcher').'</a><br />'; } ?></p>
<p><?php _e('Track with Google Analytics the', 'content-switcher'); ?>:</p>
<p style="margin: 1.5em;"><?php foreach (array(
'admin' => __('Administrators', 'content-switcher'),
'editor' => __('Editors', 'content-switcher'),
'author' => __('Authors', 'content-switcher'),
'contributor' => __('Contributors', 'content-switcher'),
'subscriber' => __('Subscribers', 'content-switcher'),
'visitor' => __('Visitors without any role', 'content-switcher')) as $key => $value) {
echo '<label><input type="checkbox" name="'.$key.'_tracked" id="'.$key.'_tracked" value="yes"'.($options[$key.'_tracked'] == 'yes' ? ' checked="checked"' : '').' /> '.$value.'<br /></label>'; } ?>
<span class="description">(<?php _e('you can check several boxes', 'content-switcher'); ?>)</span></p>
<p><label><input type="checkbox" name="javascript_enabled" id="javascript_enabled" value="yes"<?php if ($options['javascript_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Add JavaScript code', 'content-switcher'); ?><br /></label>
<span class="description"><?php _e('If you uncheck this box, Content Switcher will never add any JavaScript code to the pages of your website, but Google Analytics and Google Optimizer will not work.', 'content-switcher'); ?></span></p>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>
<?php }