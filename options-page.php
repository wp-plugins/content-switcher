<?php global $wpdb; $error = '';
$back_office_options = (array) get_option('installations_manager_back_office');
extract(installations_manager_pages_links_markups($back_office_options));

if ((isset($_GET['action'])) && (($_GET['action'] == 'reset') || ($_GET['action'] == 'uninstall'))) {
$for = (((isset($_GET['for'])) && (is_multisite()) && (current_user_can('manage_network'))) ? $_GET['for'] : 'single');
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!installations_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'installations-manager'); }
else { if ($_GET['action'] == 'reset') { reset_installations_manager(); } else { uninstall_installations_manager($for); } } } ?>
<div class="wrap">
<div id="poststuff" style="padding-top: 0;">
<?php installations_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) {
echo '<div class="updated"><p><strong>'.($_GET['action'] == 'reset' ? __('Options reset.', 'installations-manager') : __('Options and tables deleted.', 'installations-manager')).'</strong></p></div>
<script type="text/javascript">setTimeout(\'window.location = "'.($_GET['action'] == 'reset' ? 'admin.php?page=installations-manager' : ($for == 'network' ? 'network/' : '').'plugins.php').'"\', 2000);</script>'; } ?>
<?php installations_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php if ($_GET['action'] == 'reset') { _e('Do you really want to reset the options of Installations Manager?', 'installations-manager'); }
elseif ($for == 'network') { _e('Do you really want to permanently delete the options and tables of Installations Manager for all sites in this network?', 'installations-manager'); }
else { _e('Do you really want to permanently delete the options and tables of Installations Manager?', 'installations-manager'); } ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'installations-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
if (!installations_manager_user_can($back_office_options, 'manage')) { $_POST = array(); $error = __('You don\'t have sufficient permissions.', 'installations-manager'); }
else {
include INSTALLATIONS_MANAGER_PATH.'/initial-options.php';
foreach ($_POST as $key => $value) {
if (is_string($value)) { $_POST[$key] = stripslashes(html_entity_decode(str_replace('&nbsp;', ' ', $value))); } }
$back_office_options = update_installations_manager_back_office($back_office_options, 'options');

foreach (array(
'installation_notification_email_sent',
'upgrades_email_sent',
'upgrades_notification_email_sent',
'website_notification_email_sent') as $field) { if (!isset($_POST[$field])) { $_POST[$field] = 'no'; } }
foreach ($initial_options[''] as $key => $value) {
if ((isset($_POST[$key])) && ($_POST[$key] != '')) { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('installations_manager', $options);
foreach (array(
'installation_notification_email_body',
'upgrades_email_body',
'upgrades_form_code',
'upgrades_notification_email_body',
'website_notification_email_body') as $field) {
if ((!isset($_POST[$field])) || ($_POST[$field] == '')) { $_POST[$field] = $initial_options[$field]; }
update_option(substr('installations_manager_'.$field, 0, 64), $_POST[$field]); } } }
if (!isset($options)) { $options = (array) get_option('installations_manager'); }

foreach ($options as $key => $value) {
if (is_string($value)) { $options[$key] = htmlspecialchars($value); } }
$undisplayed_modules = (array) $back_office_options['options_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff" style="padding-top: 0;">
<?php installations_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.', 'installations-manager').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php installations_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if ($error != '') { echo '<p style="color: #c00000;">'.$error.'</p>'; } ?>
<p class="description"><?php _e('You can reset an option by leaving the corresponding field blank.', 'installations-manager'); ?></p>
<?php installations_manager_pages_summary($back_office_options); ?>

<div class="postbox" id="website-notification-email-module"<?php if (in_array('website-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="website-notification-email"><strong><?php echo $modules['options']['website-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="website_notification_email_sent" id="website_notification_email_sent" value="yes"<?php if ($options['website_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a website notification email', 'installations-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_notification_email_sender"><?php _e('Sender', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="website_notification_email_sender" id="website_notification_email_sender" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['website_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_notification_email_receiver"><?php _e('Receiver', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="website_notification_email_receiver" id="website_notification_email_receiver" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['website_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('You can enter several email addresses. Separate them with commas.', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_notification_email_subject"><?php _e('Subject', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="website_notification_email_subject" id="website_notification_email_subject" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['website_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="website_notification_email_body"><?php _e('Body', 'installations-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="website_notification_email_body" id="website_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('installations_manager_website_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields.', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="installation-notification-email-module"<?php if (in_array('installation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="installation-notification-email"><strong><?php echo $modules['options']['installation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="installation_notification_email_sent" id="installation_notification_email_sent" value="yes"<?php if ($options['installation_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an installation notification email', 'installations-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="installation_notification_email_sender"><?php _e('Sender', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="installation_notification_email_sender" id="installation_notification_email_sender" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['installation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="installation_notification_email_receiver"><?php _e('Receiver', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="installation_notification_email_receiver" id="installation_notification_email_receiver" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['installation_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('You can enter several email addresses. Separate them with commas.', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="installation_notification_email_subject"><?php _e('Subject', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="installation_notification_email_subject" id="installation_notification_email_subject" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['installation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="installation_notification_email_body"><?php _e('Body', 'installations-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="installation_notification_email_body" id="installation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('installations_manager_installation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields.', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="upgrades-email-module"<?php if (in_array('upgrades-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="upgrades-email"><strong><?php echo $modules['options']['upgrades-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="upgrades_email_sent" id="upgrades_email_sent" value="yes"<?php if ($options['upgrades_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an upgrades email', 'installations-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="upgrades_email_sender"><?php _e('Sender', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="upgrades_email_sender" id="upgrades_email_sender" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['upgrades_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="upgrades_email_receiver"><?php _e('Receiver', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="upgrades_email_receiver" id="upgrades_email_receiver" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['upgrades_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('You can enter several email addresses. Separate them with commas.', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="upgrades_email_subject"><?php _e('Subject', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="upgrades_email_subject" id="upgrades_email_subject" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['upgrades_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="upgrades_email_body"><?php _e('Body', 'installations-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="upgrades_email_body" id="upgrades_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('installations_manager_upgrades_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields.', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="upgrades-notification-email-module"<?php if (in_array('upgrades-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="upgrades-notification-email"><strong><?php echo $modules['options']['upgrades-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="upgrades_notification_email_sent" id="upgrades_notification_email_sent" value="yes"<?php if ($options['upgrades_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send an upgrades notification email', 'installations-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="upgrades_notification_email_sender"><?php _e('Sender', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="upgrades_notification_email_sender" id="upgrades_notification_email_sender" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['upgrades_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="upgrades_notification_email_receiver"><?php _e('Receiver', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="upgrades_notification_email_receiver" id="upgrades_notification_email_receiver" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['upgrades_notification_email_receiver']; ?></textarea><br />
<span class="description"><?php _e('You can enter several email addresses. Separate them with commas.', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="upgrades_notification_email_subject"><?php _e('Subject', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="upgrades_notification_email_subject" id="upgrades_notification_email_subject" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['upgrades_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="upgrades_notification_email_body"><?php _e('Body', 'installations-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="upgrades_notification_email_body" id="upgrades_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('installations_manager_upgrades_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields.', 'installations-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox" id="upgrades-form-module"<?php if (in_array('upgrades-form', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 style="font-size: 1.25em;" id="upgrades-form"><strong><?php echo $modules['options']['upgrades-form']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="upgrades_form_code"><?php _e('Code', 'installations-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="upgrades_form_code" id="upgrades_form_code" rows="15" cols="75"><?php echo htmlspecialchars(get_option('installations_manager_upgrades_form_code')); ?></textarea>
<p class="description">
<a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#input"><?php _e('Display a form field', 'installations-manager'); ?></a><br />
<a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#textarea"><?php _e('Display a text area', 'installations-manager'); ?></a><br />
<a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#select"><?php _e('Display a dropdown list', 'installations-manager'); ?></a><br />
<a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#error"><?php _e('Display an error message', 'installations-manager'); ?></a><br />
<a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#button"><?php _e('Display a submit button', 'installations-manager'); ?></a></p></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
<div id="captcha-module"<?php if (in_array('captcha', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="captcha"><strong><?php echo $modules['options']['upgrades-form']['modules']['captcha']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#captcha"><?php _e('How to display a CAPTCHA?', 'installations-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_captcha_type"><?php _e('Default type', 'installations-manager'); ?></label></strong></th>
<td><select name="default_captcha_type" id="default_captcha_type">
<?php include INSTALLATIONS_MANAGER_PATH.'/libraries/captchas.php';
$captcha_type = do_shortcode($options['default_captcha_type']);
asort($captchas_types);
foreach ($captchas_types as $key => $value) {
echo '<option value="'.$key.'"'.($captcha_type == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select>
<span class="description"><a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#captcha"><?php _e('More informations', 'installations-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="default_recaptcha_theme"><?php _e('Default reCAPTCHA theme', 'installations-manager'); ?></label></strong></th>
<td><select name="default_recaptcha_theme" id="default_recaptcha_theme">
<?php include INSTALLATIONS_MANAGER_PATH.'/libraries/captchas.php';
$recaptcha_theme = do_shortcode($options['default_recaptcha_theme']);
asort($recaptcha_themes);
foreach ($recaptcha_themes as $key => $value) {
echo '<option value="'.$key.'"'.($recaptcha_theme == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select>
<span class="description"><a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#recaptcha"><?php _e('More informations', 'installations-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recaptcha_public_key"><?php _e('reCAPTCHA public key', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recaptcha_public_key" id="recaptcha_public_key" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/60)))+'em';" onblur="this.style.height = '1.75em';" cols="50"><?php echo $options['recaptcha_public_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#recaptcha"><?php _e('More informations', 'installations-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'installations-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="recaptcha_private_key"><?php _e('reCAPTCHA private key', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="recaptcha_private_key" id="recaptcha_private_key" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/60)))+'em';" onblur="this.style.height = '1.75em';" cols="50"><?php echo $options['recaptcha_private_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#recaptcha"><?php _e('More informations', 'installations-manager'); ?></a>
<?php if (function_exists('commerce_manager_admin_menu')) { echo '<br />'.__('Leave this field blank to apply the Commerce Manager\'s option.', 'installations-manager'); } ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div>
<div id="error-messages-module"<?php if (in_array('error-messages', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="error-messages"><strong><?php echo $modules['options']['upgrades-form']['modules']['error-messages']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a <?php echo $documentations_links_markup; ?> href="http://www.kleor.com/contact-manager/#error"><?php _e('How to display an error message?', 'installations-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_fields_message"><?php _e('Unfilled required fields', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_fields_message" id="unfilled_fields_message" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['unfilled_fields_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="unfilled_field_message"><?php _e('Unfilled required field', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="unfilled_field_message" id="unfilled_field_message" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['unfilled_field_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_email_address_message"><?php _e('Invalid email address', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_email_address_message" id="invalid_email_address_message" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['invalid_email_address_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="invalid_captcha_message"><?php _e('Invalid CAPTCHA', 'installations-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="invalid_captcha_message" id="invalid_captcha_message" rows="1" onfocus="this.style.height = (1.75*Math.min(5, 1 + Math.floor(this.value.length/90)))+'em';" onblur="this.style.height = '1.75em';" cols="75"><?php echo $options['invalid_captcha_message']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update', 'installations-manager'); ?>" /></td></tr>
</tbody></table>
</div>
</div></div>

<p class="submit"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes', 'installations-manager'); ?>" /></p>
<?php installations_manager_pages_module($back_office_options, 'options-page', $undisplayed_modules); ?>
</form>
</div>
</div>

<script type="text/javascript">
var anchor = window.location.hash;
<?php foreach ($modules['options'] as $key => $value) {
echo "if (anchor == '#".$key."') { document.getElementById('".$key."-module').style.display = 'block'; }\n";
if (isset($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
echo "if (anchor == '#".$module_key."') {
document.getElementById('".$key."-module').style.display = 'block';
document.getElementById('".$module_key."-module').style.display = 'block'; }\n"; } } } ?>
</script>
<?php }