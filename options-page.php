<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ($_POST['submit'] ==  __('Save Changes')) {
if ($_POST['analytics_tracking_admin'] == 'yes') { $analytics_tracking_admin = 'yes'; } else { $analytics_tracking_admin = 'no'; }
if ($_POST['analytics_tracking_author'] == 'yes') { $analytics_tracking_author = 'yes'; } else { $analytics_tracking_author = 'no'; }
if ($_POST['analytics_tracking_contributor'] == 'yes') { $analytics_tracking_contributor = 'yes'; } else { $analytics_tracking_contributor = 'no'; }
if ($_POST['analytics_tracking_editor'] == 'yes') { $analytics_tracking_editor = 'yes'; } else { $analytics_tracking_editor = 'no'; }
$analytics_tracking_id = mysql_real_escape_string($_POST['analytics_tracking_id']);
if ($_POST['analytics_tracking_subscriber'] == 'yes') { $analytics_tracking_subscriber = 'yes'; } else { $analytics_tracking_subscriber = 'no'; }
if ($_POST['analytics_tracking_visitor'] == 'yes') { $analytics_tracking_visitor = 'yes'; } else { $analytics_tracking_visitor = 'no'; }
if ($_POST['javascript_enabled'] == 'yes') { $javascript_enabled = 'yes'; } else { $javascript_enabled = 'no'; }
$optimizer_tracking_id = mysql_real_escape_string($_POST['optimizer_tracking_id']);

$content_switcher_options = array(
'analytics_tracking_admin' => $analytics_tracking_admin,
'analytics_tracking_author' => $analytics_tracking_author,
'analytics_tracking_contributor' => $analytics_tracking_contributor,
'analytics_tracking_editor' => $analytics_tracking_editor,
'analytics_tracking_id' => $analytics_tracking_id,
'analytics_tracking_subscriber' => $analytics_tracking_subscriber,
'analytics_tracking_visitor' => $analytics_tracking_visitor,
'javascript_enabled' => $javascript_enabled,
'optimizer_tracking_id' => $optimizer_tracking_id);
update_option('content_switcher', $content_switcher_options); }

$content_switcher_options = get_option('content_switcher'); ?>

<div class="wrap">
<h2>Content Switcher</h2>
<?php if ($_POST['submit'] ==  __('Save Changes')) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<ul style="margin: 1.5em">
<li><?php _e('<a href="http://www.kleor-editions.com/content-switcher/en">Documentation</a>', 'content-switcher'); ?></li>
</ul>
<h3><?php _e('Options', 'content-switcher'); ?></h3>
<form method="post" action="">
<p><label for="analytics_tracking_id"><?php _e('Google Analytics Account Tracking ID', 'content-switcher'); ?>:</label> <input type="text" name="analytics_tracking_id" id="analytics_tracking_id" value="<?php echo $content_switcher_options['analytics_tracking_id']; ?>" size="16" />
<?php _e('<a href="http://www.kleor-editions.com/content-switcher/en/#part5.2">More informations</a>', 'content-switcher'); ?><br />
<label for="optimizer_tracking_id"><?php _e('Google Optimizer Account Tracking ID', 'content-switcher'); ?>:</label> <input type="text" name="optimizer_tracking_id" id="optimizer_tracking_id" value="<?php echo $content_switcher_options['optimizer_tracking_id']; ?>" size="16" />
<?php _e('<a href="http://www.kleor-editions.com/content-switcher/en/#part5.2">More informations</a>', 'content-switcher'); ?></p>
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
<p class="submit" style="margin: 0 20em;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>