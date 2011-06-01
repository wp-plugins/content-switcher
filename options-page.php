<?php if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
include_once 'initial-options.php';
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
if ($_POST['admin_tracked'] != 'yes') { $_POST['admin_tracked'] = 'no'; }
if ($_POST['author_tracked'] != 'yes') { $_POST['author_tracked'] = 'no'; }
if ($_POST['contributor_tracked'] != 'yes') { $_POST['contributor_tracked'] = 'no'; }
if ($_POST['editor_tracked'] != 'yes') { $_POST['editor_tracked'] = 'no'; }
if ($_POST['javascript_enabled'] != 'yes') { $_POST['javascript_enabled'] = 'no'; }
if ($_POST['subscriber_tracked'] != 'yes') { $_POST['subscriber_tracked'] = 'no'; }
if ($_POST['visitor_tracked'] != 'yes') { $_POST['visitor_tracked'] = 'no'; }
foreach ($initial_options as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $initial_options[$key]; } }
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
<p><label for="analytics_tracking_id"><?php _e('Google Analytics Account Tracking ID', 'content-switcher'); ?>:</label> <input type="text" name="analytics_tracking_id" id="analytics_tracking_id" value="<?php echo $options['analytics_tracking_id']; ?>" size="16" />
<a href="http://www.kleor-editions.com/content-switcher/#tracking-id"><?php _e('More informations', 'content-switcher'); ?></a><br />
<label for="optimizer_tracking_id"><?php _e('Google Optimizer Account Tracking ID', 'content-switcher'); ?>:</label> <input type="text" name="optimizer_tracking_id" id="optimizer_tracking_id" value="<?php echo $options['optimizer_tracking_id']; ?>" size="16" />
<a href="http://www.kleor-editions.com/content-switcher/#tracking-id"><?php _e('More informations', 'content-switcher'); ?></a></p>
<p><?php _e('Track with Google Analytics the', 'content-switcher'); ?>:</p>
<p style="margin: 1.5em;"><input type="checkbox" name="admin_tracked" id="admin_tracked" value="yes"<?php if ($options['admin_tracked'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="admin_tracked"><?php _e('Administrators', 'content-switcher'); ?></label><br />
<input type="checkbox" name="editor_tracked" id="editor_tracked" value="yes"<?php if ($options['editor_tracked'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="editor_tracked"><?php _e('Editors', 'content-switcher'); ?></label><br />
<input type="checkbox" name="author_tracked" id="author_tracked" value="yes"<?php if ($options['author_tracked'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="author_tracked"><?php _e('Authors', 'content-switcher'); ?></label><br />
<input type="checkbox" name="contributor_tracked" id="contributor_tracked" value="yes"<?php if ($options['contributor_tracked'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="contributor_tracked"><?php _e('Contributors', 'content-switcher'); ?></label><br />
<input type="checkbox" name="subscriber_tracked" id="subscriber_tracked" value="yes"<?php if ($options['subscriber_tracked'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="subscriber_tracked"><?php _e('Subscribers', 'content-switcher'); ?></label><br />
<input type="checkbox" name="visitor_tracked" id="visitor_tracked" value="yes"<?php if ($options['visitor_tracked'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="visitor_tracked"><?php _e('Visitors without any role', 'content-switcher'); ?></label><br />
<span class="description">(<?php _e('you can check several boxes', 'content-switcher'); ?>)</span></p>
<p><input type="checkbox" name="javascript_enabled" id="javascript_enabled" value="yes"<?php if ($options['javascript_enabled'] == 'yes') { echo ' checked="checked"'; } ?> /> <label for="javascript_enabled"><?php _e('Add JavaScript code', 'content-switcher'); ?></label><br />
<span class="description"><?php _e('If you uncheck this box, Content Switcher will never add any JavaScript code to the pages of your website, but Google Analytics and Google Optimizer will not work.', 'content-switcher'); ?></span></p>
<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
</form>
</div>