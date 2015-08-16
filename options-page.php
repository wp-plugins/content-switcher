<?php if ((isset($_GET['action'])) && (($_GET['action'] == 'reset') || ($_GET['action'] == 'uninstall'))) {
if (!current_user_can('activate_plugins')) {
if (!headers_sent()) { header('Location: options-general.php?page=content-switcher'); exit(); }
else { echo '<script type="text/javascript">window.location = "options-general.php?page=content-switcher";</script>'; } }
else {
$for = (((isset($_GET['for'])) && (is_multisite()) && (current_user_can('manage_network'))) ? $_GET['for'] : 'single');
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if ($_GET['action'] == 'reset') { reset_content_switcher(); } else { uninstall_content_switcher($for); } } ?>
<div class="wrap">
<h2>Content Switcher</h2>
<ul class="subsubsub"><li><a href="http://www.kleor.com/content-switcher/"><?php _e('Documentation', 'content-switcher'); ?></a></li></ul>
<div class="clear"></div>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($_GET['action'] == 'reset' ? __('Options reset.', 'content-switcher') : __('Options deleted.', 'content-switcher')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "'.($_GET['action'] == 'reset' ? 'options-general.php?page=content-switcher' : ($for == 'network' ? 'network/' : '').'plugins.php').'"\', 2000);</script>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" name="<?php echo $_GET['page']; ?>" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<p><strong style="color: #c00000;"><?php if ($_GET['action'] == 'reset') { _e('Do you really want to reset the options of Content Switcher?', 'content-switcher'); }
elseif ($for == 'network') { _e('Do you really want to permanently delete the options of Content Switcher for all sites in this network?', 'content-switcher'); }
else { _e('Do you really want to permanently delete the options of Content Switcher?', 'content-switcher'); } ?></strong> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'content-switcher'); ?>" />
<span class="description"><?php _e('This action is irreversible.', 'content-switcher'); ?></span></p>
</div>
</form><?php } ?>
</div><?php } }

else {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
include CONTENT_SWITCHER_PATH.'initial-options.php';
foreach ($initial_options as $key => $value) { if (!isset($_POST[$key])) { $_POST[$key] = ''; } }
foreach (array(
'administrator_tracked',
'author_tracked',
'back_office_tracked',
'contributor_tracked',
'editor_tracked',
'front_office_tracked',
'javascript_enabled',
'subscriber_tracked',
'visitor_tracked') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach ($initial_options as $key => $value) { if ($_POST[$key] == '') { $_POST[$key] = $value; } $options[$key] = $_POST[$key]; }
update_option('content_switcher', $options); }
else { $options = (array) get_option('content_switcher'); }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } } ?>

<div class="wrap">
<h2>Content Switcher</h2>
<ul class="subsubsub"><li><a href="http://www.kleor.com/content-switcher/"><?php _e('Documentation', 'content-switcher'); ?></a></li></ul>
<div class="clear"></div>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.', 'content-switcher').'</strong></p></div>'; } ?>
<h3><?php _e('Options', 'content-switcher'); ?></h3>
<form method="post" name="<?php echo $_GET['page']; ?>" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<p><?php foreach (array(
'analytics' => __('Google Analytics Account Tracking ID:', 'content-switcher'),
'optimizer' => __('Google Optimizer Account Tracking ID:', 'content-switcher')) as $key => $value) {
echo '<label>'.$value.' <input type="text" name="'.$key.'_tracking_id" id="'.$key.'_tracking_id" value="'.$options[$key.'_tracking_id'].'" size="16" /></label>
<span class="description"><a href="http://www.kleor.com/content-switcher/#'.($key == 'analytics' ? 'google-analytics' : 'tracking-id').'">'.__('More informations', 'content-switcher').'</a></span><br />'; } ?></p>
<p><?php _e('Track with Google Analytics the:', 'content-switcher'); ?> <span class="description"><a href="http://www.kleor.com/content-switcher/#google-analytics"><?php _e('More informations', 'content-switcher'); ?></a></span></p>
<p style="margin: 1.5em;"><?php foreach (array(
'administrator' => __('Administrators', 'content-switcher'),
'editor' => __('Editors', 'content-switcher'),
'author' => __('Authors', 'content-switcher'),
'contributor' => __('Contributors', 'content-switcher'),
'subscriber' => __('Subscribers', 'content-switcher'),
'visitor' => __('Visitors without any role', 'content-switcher')) as $key => $value) {
echo '<label><input type="checkbox" name="'.$key.'_tracked" id="'.$key.'_tracked" value="yes"'.($options[$key.'_tracked'] == 'yes' ? ' checked="checked"' : '').' /> '.$value.'<br /></label>'; } ?>
<span class="description">(<?php _e('you can check several boxes', 'content-switcher'); ?>)</span></p>
<p><?php _e('Track with Google Analytics the:', 'content-switcher'); ?> <span class="description"><a href="http://www.kleor.com/content-switcher/#google-analytics"><?php _e('More informations', 'content-switcher'); ?></a></span></p>
<p style="margin: 1.5em;"><?php foreach (array(
'front_office' => __('Front office pages', 'content-switcher'),
'back_office' => __('Back office pages', 'content-switcher')) as $key => $value) {
echo '<label><input type="checkbox" name="'.$key.'_tracked" id="'.$key.'_tracked" value="yes"'.($options[$key.'_tracked'] == 'yes' ? ' checked="checked"' : '').' /> '.$value.'<br /></label>'; } ?>
<span class="description">(<?php _e('you can check several boxes', 'content-switcher'); ?>)</span></p>
<p><label><input type="checkbox" name="javascript_enabled" id="javascript_enabled" value="yes"<?php if ($options['javascript_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Add JavaScript code', 'content-switcher'); ?><br /></label>
<span class="description"><?php _e('If you uncheck this box, Content Switcher will never add any JavaScript code to the pages of your website, but Google Analytics and Google Optimizer will not work.', 'content-switcher'); ?></span></p>
<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes', 'content-switcher'); ?>" /></p>
</form>
</div>
<?php }