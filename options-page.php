<?php if (!current_user_can('manage_options')) { wp_die(__('You do not have sufficient permissions to access this page.')); }

if ($_POST['submit'] ==  __('Save Changes')) {
$analytics_tracking_id = mysql_real_escape_string($_POST['analytics-tracking-id']);
$optimizer_tracking_id = mysql_real_escape_string($_POST['optimizer-tracking-id']);
if ($_POST['analytics-tracking-admin'] == 'yes') { $analytics_tracking_admin = 'yes'; } else { $analytics_tracking_admin = 'no'; }
if ($_POST['analytics-tracking-editor'] == 'yes') { $analytics_tracking_editor = 'yes'; } else { $analytics_tracking_editor = 'no'; }
if ($_POST['analytics-tracking-author'] == 'yes') { $analytics_tracking_author = 'yes'; } else { $analytics_tracking_author = 'no'; }
if ($_POST['analytics-tracking-contributor'] == 'yes') { $analytics_tracking_contributor = 'yes'; } else { $analytics_tracking_contributor = 'no'; }
if ($_POST['analytics-tracking-subscriber'] == 'yes') { $analytics_tracking_subscriber = 'yes'; } else { $analytics_tracking_subscriber = 'no'; }
if ($_POST['analytics-tracking-visitor'] == 'yes') { $analytics_tracking_visitor = 'yes'; } else { $analytics_tracking_visitor = 'no'; }
if ($_POST['javascript-enabled'] == 'yes') { $javascript_enabled = 'yes'; } else { $javascript_enabled = 'no'; }

$content_switcher_options = array(
'analytics_tracking_id' => $analytics_tracking_id,
'optimizer_tracking_id' => $optimizer_tracking_id,
'analytics_tracking_admin' => $analytics_tracking_admin,
'analytics_tracking_editor' => $analytics_tracking_editor,
'analytics_tracking_author' => $analytics_tracking_author,
'analytics_tracking_contributor' => $analytics_tracking_contributor,
'analytics_tracking_subscriber' => $analytics_tracking_subscriber,
'analytics_tracking_visitor' => $analytics_tracking_visitor,
'javascript_enabled' => $javascript_enabled);
update_option('content_switcher', $content_switcher_options); }

$content_switcher_options = get_option('content_switcher'); ?>

<div class="wrap">
<h2>Content Switcher</h2>
<?php if ($_POST['submit'] ==  __('Save Changes')) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<p><?php _e('Complete Documentation', 'content-switcher'); ?>:</p>
<ul style="margin: 1.5em">
<li><a href="http://www.kleor-editions.com/content-switcher/en"><?php _e('in English', 'content-switcher'); ?></a></li>
<li><a href="http://www.kleor-editions.com/content-switcher"><?php _e('in French', 'content-switcher'); ?></a></li>
</ul>
<h3><?php _e('Options', 'content-switcher'); ?></h3>
<form method="post" action="">
<p><label for="analytics-tracking-id"><?php _e('Google Analytics Account Tracking ID', 'content-switcher'); ?>:</label> <input type="text" name="analytics-tracking-id" id="analytics-tracking-id" value="<?php echo $content_switcher_options['analytics_tracking_id']; ?>" size="16" />
<?php _e('<a href="http://www.kleor-editions.com/content-switcher/en/#part5.2">More informations</a>', 'content-switcher'); ?><br />
<label for="optimizer-tracking-id"><?php _e('Google Optimizer Account Tracking ID', 'content-switcher'); ?>:</label> <input type="text" name="optimizer-tracking-id" id="optimizer-tracking-id" value="<?php echo $content_switcher_options['optimizer_tracking_id']; ?>" size="16" />
<?php _e('<a href="http://www.kleor-editions.com/content-switcher/en/#part5.2">More informations</a>', 'content-switcher'); ?></p>
<p><?php _e('Track with Google Analytics the', 'content-switcher'); ?>:</p>
<p><input type="checkbox" name="analytics-tracking-admin" id="analytics-tracking-admin" value="yes"<?php if ($content_switcher_options['analytics_tracking_admin'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics-tracking-admin"><?php _e('Administrators', 'content-switcher'); ?></label><br />
<input type="checkbox" name="analytics-tracking-editor" id="analytics-tracking-editor" value="yes"<?php if ($content_switcher_options['analytics_tracking_editor'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics-tracking-editor"><?php _e('Editors', 'content-switcher'); ?></label><br />
<input type="checkbox" name="analytics-tracking-author" id="analytics-tracking-author" value="yes"<?php if ($content_switcher_options['analytics_tracking_author'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics-tracking-author"><?php _e('Authors', 'content-switcher'); ?></label><br />
<input type="checkbox" name="analytics-tracking-contributor" id="analytics-tracking-contributor" value="yes"<?php if ($content_switcher_options['analytics_tracking_contributor'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics-tracking-contributor"><?php _e('Contributors', 'content-switcher'); ?></label><br />
<input type="checkbox" name="analytics-tracking-subscriber" id="analytics-tracking-subscriber" value="yes"<?php if ($content_switcher_options['analytics_tracking_subscriber'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics-tracking-subscriber"><?php _e('Subscribers', 'content-switcher'); ?></label><br />
<input type="checkbox" name="analytics-tracking-visitor" id="analytics-tracking-visitor" value="yes"<?php if ($content_switcher_options['analytics_tracking_visitor'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="analytics-tracking-visitor"><?php _e('Visitors without any role', 'content-switcher'); ?></label><br />
<em>(<?php _e('you can check several boxes', 'content-switcher'); ?>)</em></p>
<p><input type="checkbox" name="javascript-enabled" id="javascript-enabled" value="yes"<?php if ($content_switcher_options['javascript_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="javascript-enabled"><?php _e('Add JavaScript code', 'content-switcher'); ?></label><br />
<em><?php _e('If you uncheck this box, Content Switcher will never add any JavaScript code to the pages of your website, but Google Analytics and Google Optimizer will not work.', 'content-switcher'); ?> <?php _e('<a href="http://www.kleor-editions.com/content-switcher/en/#part6.1">More informations</a>', 'content-switcher'); ?></em></p>
<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>