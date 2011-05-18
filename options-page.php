<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

$content_switcher_options = get_option('content_switcher');

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
if ($_POST['analytics_tracking_admin'] != 'yes') { $_POST['analytics_tracking_admin'] = 'no'; }
if ($_POST['analytics_tracking_author'] != 'yes') { $_POST['analytics_tracking_author'] = 'no'; }
if ($_POST['analytics_tracking_contributor'] != 'yes') { $_POST['analytics_tracking_contributor'] = 'no'; }
if ($_POST['analytics_tracking_editor'] != 'yes') { $_POST['analytics_tracking_editor'] = 'no'; }
if ($_POST['analytics_tracking_subscriber'] != 'yes') { $_POST['analytics_tracking_subscriber'] = 'no'; }
if ($_POST['analytics_tracking_visitor'] != 'yes') { $_POST['analytics_tracking_visitor'] = 'no'; }
if ($_POST['javascript_enabled'] != 'yes') { $_POST['javascript_enabled'] = 'no'; }
foreach ($content_switcher_options as $key => $value) { $content_switcher_options[$key] = $_POST[$key]; }
update_option('content_switcher', $content_switcher_options); }

$content_switcher_options = array_map('htmlspecialchars', $content_switcher_options); ?>

<div class="wrap">
<h2>Content Switcher</h2>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<p style="margin: 1.5em"><a href="http://www.kleor-editions.com/content-switcher"><?php _e('Documentation', 'content-switcher'); ?></a></p>
<h3><?php _e('Options', 'content-switcher'); ?></h3>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<p><label for="analytics_tracking_id"><?php _e('Google Analytics Account Tracking ID', 'content-switcher'); ?>:</label> <input type="text" name="analytics_tracking_id" id="analytics_tracking_id" value="<?php echo $content_switcher_options['analytics_tracking_id']; ?>" size="16" />
<a href="http://www.kleor-editions.com/content-switcher/#tracking-id"><?php _e('More informations', 'content-switcher'); ?></a><br />
<label for="optimizer_tracking_id"><?php _e('Google Optimizer Account Tracking ID', 'content-switcher'); ?>:</label> <input type="text" name="optimizer_tracking_id" id="optimizer_tracking_id" value="<?php echo $content_switcher_options['optimizer_tracking_id']; ?>" size="16" />
<a href="http://www.kleor-editions.com/content-switcher/#tracking-id"><?php _e('More informations', 'content-switcher'); ?></a></p>
<p><?php _e('Track with Google Analytics the', 'content-switcher'); ?>:</p>
<p style="margin: 1.5em;"><input type="checkbox" name="analytics_tracking_admin" id="analytics_tracking_admin" value="yes"<?php if ($content_switcher_options['analytics_tracking_admin'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics_tracking_admin"><?php _e('Administrators', 'content-switcher'); ?></label><br />
<input type="checkbox" name="analytics_tracking_editor" id="analytics_tracking_editor" value="yes"<?php if ($content_switcher_options['analytics_tracking_editor'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics_tracking_editor"><?php _e('Editors', 'content-switcher'); ?></label><br />
<input type="checkbox" name="analytics_tracking_author" id="analytics_tracking_author" value="yes"<?php if ($content_switcher_options['analytics_tracking_author'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics_tracking_author"><?php _e('Authors', 'content-switcher'); ?></label><br />
<input type="checkbox" name="analytics_tracking_contributor" id="analytics_tracking_contributor" value="yes"<?php if ($content_switcher_options['analytics_tracking_contributor'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics_tracking_contributor"><?php _e('Contributors', 'content-switcher'); ?></label><br />
<input type="checkbox" name="analytics_tracking_subscriber" id="analytics_tracking_subscriber" value="yes"<?php if ($content_switcher_options['analytics_tracking_subscriber'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics_tracking_subscriber"><?php _e('Subscribers', 'content-switcher'); ?></label><br />
<input type="checkbox" name="analytics_tracking_visitor" id="analytics_tracking_visitor" value="yes"<?php if ($content_switcher_options['analytics_tracking_visitor'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics_tracking_visitor"><?php _e('Visitors without any role', 'content-switcher'); ?></label><br />
<span class="description">(<?php _e('you can check several boxes', 'content-switcher'); ?>)</span></p>
<p><input type="checkbox" name="javascript_enabled" id="javascript_enabled" value="yes"<?php if ($content_switcher_options['javascript_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="javascript_enabled"><?php _e('Add JavaScript code', 'content-switcher'); ?></label><br />
<span class="description"><?php _e('If you uncheck this box, Content Switcher will never add any JavaScript code to the pages of your website, but Google Analytics and Google Optimizer will not work.', 'content-switcher'); ?></span></p>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>