<?php global $wpdb;
$back_office_options = get_option('membership_manager_back_office');

if ($_GET['action'] == 'uninstall') {
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) { uninstall_membership_manager(); } ?>
<div class="wrap">
<div id="poststuff">
<?php membership_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Options and tables deleted.', 'membership-manager').'</strong></p></div>'; } ?>
<?php membership_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<?php if (!isset($_POST['submit'])) { ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<div class="alignleft actions">
<?php _e('Do you really want to permanently delete the options and tables of Membership Manager?', 'membership-manager'); ?> 
<input type="submit" class="button-secondary" name="submit" id="submit" value="<?php _e('Yes', 'membership-manager'); ?>" />
</div>
</form><?php } ?>
</div>
</div><?php }

else {
include 'admin-pages.php';
if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
include 'initial-options.php';
$_POST = array_map('html_entity_decode', $_POST);
$_POST = array_map('stripslashes', $_POST);
if ($_POST['options_page_summary_displayed'] != 'yes') { $_POST['options_page_summary_displayed'] = 'no'; }
$back_office_options['options_page_summary_displayed'] = $_POST['options_page_summary_displayed'];
$back_office_options['options_page_undisplayed_modules'] = array();
foreach ($modules['options'] as $key => $value) {
if (($_POST['options_page_'.str_replace('-', '_', $key).'_module_displayed'] != 'yes') && ($value['required'] != 'yes')) { $back_office_options['options_page_undisplayed_modules'][] = $key; }
if (is_array($value['modules'])) { foreach ($value['modules'] as $module_key => $module_value) {
if (($_POST['options_page_'.str_replace('-', '_', $module_key).'_module_displayed'] != 'yes') && ($module_value['required'] != 'yes')) { $back_office_options['options_page_undisplayed_modules'][] = $module_key; } } } }
update_option('membership_manager_back_office', $back_office_options);

foreach (array(
'member_subscribed_to_autoresponder',
'registration_confirmation_email_sent',
'registration_custom_instructions_executed',
'registration_notification_email_sent') as $field) { if ($_POST[$field] != 'yes') { $_POST[$field] = 'no'; } }
foreach (array(
'maximum_login_length',
'maximum_password_length',
'minimum_login_length',
'minimum_password_length') as $field) { $_POST[$field] = (int) $_POST[$field]; }
if ($_POST['maximum_login_length'] < 2) { $_POST['maximum_login_length'] = $initial_options['']['maximum_login_length']; }
if ($_POST['maximum_password_length'] < 8) { $_POST['maximum_password_length'] = $initial_options['']['maximum_password_length']; }
if ($_POST['minimum_login_length'] < 1) { $_POST['minimum_login_length'] = $initial_options['']['minimum_login_length']; }
if ($_POST['minimum_login_length'] > $_POST['maximum_login_length']) { $_POST['minimum_login_length'] = $_POST['maximum_login_length']; }
if ($_POST['minimum_password_length'] < 1) { $_POST['minimum_password_length'] = $initial_options['']['minimum_password_length']; }
if ($_POST['minimum_password_length'] > $_POST['maximum_password_length']) { $_POST['minimum_password_length'] = $_POST['maximum_password_length']; }
foreach ($initial_options[''] as $key => $value) {
if ($_POST[$key] != '') { $options[$key] = $_POST[$key]; }
else { $options[$key] = $value; } }
update_option('membership_manager', $options);
foreach (array(
'activation_notification_email_body',
'deactivation_notification_email_body',
'password_reset_email_body',
'removal_notification_email_body',
'registration_confirmation_email_body',
'registration_custom_instructions',
'registration_notification_email_body') as $field) {
if ($_POST[$field] == '') { $_POST[$field] = $initial_options[$field]; }
update_option('membership_manager_'.$field, $_POST[$field]); } }
else { $options = (array) get_option('membership_manager'); }

$options = array_map('htmlspecialchars', $options);
$undisplayed_modules = (array) $back_office_options['options_page_undisplayed_modules']; ?>

<div class="wrap">
<div id="poststuff">
<?php membership_manager_pages_top($back_office_options); ?>
<?php if (isset($_POST['submit'])) { echo '<div class="updated"><p><strong>'.__('Settings saved.').'</strong></p></div>'; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php membership_manager_pages_menu($back_office_options); ?>
<div class="clear"></div>
<p class="description"><?php _e('You can reset an option by leaving the corresponding field blank.', 'membership-manager'); ?></p>
<?php membership_manager_pages_summary($back_office_options); ?>

<div class="postbox"<?php if (in_array('registration', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration"><strong><?php echo $modules['options']['registration']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="minimum_login_length"><?php _e('Minimum login length', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_login_length" id="minimum_login_length" rows="1" cols="25"><?php echo $options['minimum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_login_length"><?php _e('Maximum login length', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_login_length" id="maximum_login_length" rows="1" cols="25"><?php echo $options['maximum_login_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="minimum_password_length"><?php _e('Minimum password length', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="minimum_password_length" id="minimum_password_length" rows="1" cols="25"><?php echo $options['minimum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="maximum_password_length"><?php _e('Maximum password length', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="maximum_password_length" id="maximum_password_length" rows="1" cols="25"><?php echo $options['maximum_password_length']; ?></textarea> <span style="vertical-align: 25%;"><?php _e('characters', 'membership-manager'); ?></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_url"><?php _e('Registration confirmation URL', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_url" id="registration_confirmation_url" rows="1" cols="75"><?php echo $options['registration_confirmation_url']; ?></textarea> <a style="vertical-align: 25%;" href="<?php echo htmlspecialchars(membership_format_url($options['registration_confirmation_url'])); ?>"><?php _e('Link', 'membership-manager'); ?></a></td></tr>
<?php $categories = $wpdb->get_results("SELECT id, name FROM ".$wpdb->prefix."membership_manager_members_categories ORDER BY name ASC", OBJECT);
if ($categories) { ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="members_initial_category_id"><?php _e('Members initial category', 'membership-manager'); ?></label></strong></th>
<td><select name="members_initial_category_id" id="members_initial_category_id">
<option value="0"<?php if ($options['members_initial_category_id'] == 0) { echo ' selected="selected"'; } ?>><?php _e('None ', 'membership-manager'); ?></option>
<?php foreach ($categories as $category) {
echo '<option value="'.$category->id.'"'.($options['members_initial_category_id'] == $category->id ? ' selected="selected"' : '').'>'.$category->name.'</option>'."\n"; } ?>
</select>
<span class="description"><?php _e('Category assigned to members upon their registration', 'membership-manager'); ?></span>
<?php if ($options['members_initial_category_id'] > 0) { echo '<br />
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$options['members_initial_category_id'].'">'.__('Edit').'</a> | 
<a style="text-decoration: none;" href="admin.php?page=membership-manager-member-category&amp;id='.$options['members_initial_category_id'].'&amp;action=delete">'.__('Delete').'</a>'; } ?></td></tr>
<?php } ?>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="members_initial_status"><?php _e('Members initial status', 'membership-manager'); ?></label></strong></th>
<td><select name="members_initial_status" id="members_initial_status">
<option value="active"<?php if ($options['members_initial_status'] == 'active') { echo ' selected="selected"'; } ?>><?php _e('Active', 'membership-manager'); ?></option>
<option value="inactive"<?php if ($options['members_initial_status'] == 'inactive') { echo ' selected="selected"'; } ?>><?php _e('Inactive', 'membership-manager'); ?></option>
</select>
<span class="description"><?php _e('Status assigned to members upon their registration', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#member-status"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('registration-confirmation-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-confirmation-email"><strong><?php echo $modules['options']['registration-confirmation-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_confirmation_email_sent" id="registration_confirmation_email_sent" value="yes"<?php if ($options['registration_confirmation_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration confirmation email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_sender" id="registration_confirmation_email_sender" rows="1" cols="75"><?php echo $options['registration_confirmation_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_receiver" id="registration_confirmation_email_receiver" rows="1" cols="75"><?php echo $options['registration_confirmation_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_confirmation_email_subject" id="registration_confirmation_email_subject" rows="1" cols="75"><?php echo $options['registration_confirmation_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_confirmation_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_confirmation_email_body" id="registration_confirmation_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_registration_confirmation_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('registration-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="registration-notification-email"><strong><?php echo $modules['options']['registration-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_notification_email_sent" id="registration_notification_email_sent" value="yes"<?php if ($options['registration_notification_email_sent'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Send a registration notification email', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_sender" id="registration_notification_email_sender" rows="1" cols="75"><?php echo $options['registration_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_receiver" id="registration_notification_email_receiver" rows="1" cols="75"><?php echo $options['registration_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="registration_notification_email_subject" id="registration_notification_email_subject" rows="1" cols="75"><?php echo $options['registration_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_notification_email_body" id="registration_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_registration_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('autoresponders', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders"><strong><?php echo $modules['options']['autoresponders']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#autoresponders"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="member_subscribed_to_autoresponder" id="member_subscribed_to_autoresponder" value="yes"<?php if ($options['member_subscribed_to_autoresponder'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Subscribe the member to an autoresponder list', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_autoresponder"><?php _e('Autoresponder', 'membership-manager'); ?></label></strong></th>
<td><select name="member_autoresponder" id="member_autoresponder">
<?php include 'autoresponders.php';
$autoresponder = do_shortcode($options['member_autoresponder']);
foreach ($autoresponders as $value) {
echo '<option value="'.$value.'"'.($autoresponder == $value ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; } ?>
</select></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="member_autoresponder_list"><?php _e('List', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="member_autoresponder_list" id="member_autoresponder_list" rows="1" cols="50"><?php echo $options['member_autoresponder_list']; ?></textarea><br />
<span class="description"><?php _e('For some autoresponders, you must enter the list ID.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#autoresponders"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('autoresponders-integration', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="autoresponders-integration"><strong><?php echo $modules['options']['autoresponders-integration']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You must make some adjustments so that the subscription works with some autoresponders.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#autoresponders"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
<div<?php if (in_array('aweber', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="aweber"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['aweber']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><a href="http://www.kleor-editions.com/membership-manager/documentation/#aweber"><?php _e('Click here to read the instructions for integration.', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div<?php if (in_array('cybermailing', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="cybermailing"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['cybermailing']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><span class="description"><?php _e('You have no adjustment to make so that the subscription works with CyberMailing.', 'membership-manager'); ?></span></td></tr>
</tbody></table>
</div>
<div<?php if (in_array('getresponse', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="getresponse"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['getresponse']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="getresponse_api_key"><?php _e('API key', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="getresponse_api_key" id="getresponse_api_key" rows="1" cols="50"><?php echo $options['getresponse_api_key']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#getresponse"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<div<?php if (in_array('sg-autorepondeur', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h4 id="sg-autorepondeur"><strong><?php echo $modules['options']['autoresponders-integration']['modules']['sg-autorepondeur']['name']; ?></strong></h4>
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sg_autorepondeur_account_id"><?php _e('Account ID', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 25%;" name="sg_autorepondeur_account_id" id="sg_autorepondeur_account_id" rows="1" cols="25"><?php echo $options['sg_autorepondeur_account_id']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#sg-autorepondeur"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="sg_autorepondeur_activation_code"><?php _e('Activation code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 50%;" name="sg_autorepondeur_activation_code" id="sg_autorepondeur_activation_code" rows="1" cols="50"><?php echo $options['sg_autorepondeur_activation_code']; ?></textarea> 
<span class="description" style="vertical-align: 25%;"><a href="http://www.kleor-editions.com/membership-manager/documentation/#sg-autorepondeur"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
</tbody></table>
</div>
<table class="form-table"><tbody><tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('custom-instructions', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="custom-instructions"><strong><?php echo $modules['options']['custom-instructions']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><label><input type="checkbox" name="registration_custom_instructions_executed" id="registration_custom_instructions_executed" value="yes"<?php if ($options['registration_custom_instructions_executed'] == 'yes') { echo ' checked="checked"'; } ?> /> <?php _e('Execute custom instructions', 'membership-manager'); ?></label></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="registration_custom_instructions"><?php _e('PHP code', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="registration_custom_instructions" id="registration_custom_instructions" rows="10" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_registration_custom_instructions')); ?></textarea>
<span class="description"><?php _e('You can add custom instructions that will be executed just after the registration of the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#custom-instructions"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('password-reset-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="password-reset-email"><strong><?php echo $modules['options']['password-reset-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_sender" id="password_reset_email_sender" rows="1" cols="75"><?php echo $options['password_reset_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_receiver" id="password_reset_email_receiver" rows="1" cols="75"><?php echo $options['password_reset_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="password_reset_email_subject" id="password_reset_email_subject" rows="1" cols="75"><?php echo $options['password_reset_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="password_reset_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="password_reset_email_body" id="password_reset_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_password_reset_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('activation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="activation-notification-email"><strong><?php echo $modules['options']['activation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_sender" id="activation_notification_email_sender" rows="1" cols="75"><?php echo $options['activation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_receiver" id="activation_notification_email_receiver" rows="1" cols="75"><?php echo $options['activation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="activation_notification_email_subject" id="activation_notification_email_subject" rows="1" cols="75"><?php echo $options['activation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="activation_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="activation_notification_email_body" id="activation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_activation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('deactivation-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="deactivation-notification-email"><strong><?php echo $modules['options']['deactivation-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_sender" id="deactivation_notification_email_sender" rows="1" cols="75"><?php echo $options['deactivation_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_receiver" id="deactivation_notification_email_receiver" rows="1" cols="75"><?php echo $options['deactivation_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="deactivation_notification_email_subject" id="deactivation_notification_email_subject" rows="1" cols="75"><?php echo $options['deactivation_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="deactivation_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="deactivation_notification_email_body" id="deactivation_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_deactivation_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<div class="postbox"<?php if (in_array('removal-notification-email', $undisplayed_modules)) { echo ' style="display: none;"'; } ?>>
<h3 id="removal-notification-email"><strong><?php echo $modules['options']['removal-notification-email']['name']; ?></strong></h3>
<div class="inside">
<table class="form-table"><tbody>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_sender"><?php _e('Sender', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_sender" id="removal_notification_email_sender" rows="1" cols="75"><?php echo $options['removal_notification_email_sender']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_receiver"><?php _e('Receiver', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_receiver" id="removal_notification_email_receiver" rows="1" cols="75"><?php echo $options['removal_notification_email_receiver']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_subject"><?php _e('Subject', 'membership-manager'); ?></label></strong></th>
<td><textarea style="padding: 0 0.25em; height: 1.75em; width: 75%;" name="removal_notification_email_subject" id="removal_notification_email_subject" rows="1" cols="75"><?php echo $options['removal_notification_email_subject']; ?></textarea></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"><strong><label for="removal_notification_email_body"><?php _e('Body', 'membership-manager'); ?></label></strong></th>
<td><textarea style="float: left; margin-right: 1em; width: 75%;" name="removal_notification_email_body" id="removal_notification_email_body" rows="15" cols="75"><?php echo htmlspecialchars(get_option('membership_manager_removal_notification_email_body')); ?></textarea>
<span class="description"><?php _e('You can insert shortcodes into <em>Sender</em>, <em>Receiver</em>, <em>Subject</em> and <em>Body</em> fields to display informations about the member.', 'membership-manager'); ?> <a href="http://www.kleor-editions.com/membership-manager/documentation/#email-shortcodes"><?php _e('More informations', 'membership-manager'); ?></a></span></td></tr>
<tr style="vertical-align: top;"><th scope="row" style="width: 20%;"></th>
<td><input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" /></td></tr>
</tbody></table>
</div></div>

<p class="submit" style="margin: 0 20%;"><input type="submit" class="button-primary" name="submit" id="submit" value="<?php _e('Save Changes'); ?>" /></p>
<?php membership_manager_pages_module($back_office_options, 'options-page', $undisplayed_modules); ?>
</form>
</div>
</div>
<?php }