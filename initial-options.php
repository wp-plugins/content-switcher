<?php include 'tables.php';
$admin_email = get_option('admin_email');
$blogname = get_option('blogname');
$siteurl = get_option('siteurl');


$initial_options[''] = array(
'activation_notification_email_receiver' => '[membership-user email-address]',
'activation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'activation_notification_email_subject' => __('Activation Of Your Member Account', 'membership-manager'),
'deactivation_notification_email_receiver' => '[membership-user email-address]',
'deactivation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'deactivation_notification_email_subject' => __('Deactivation Of Your Member Account', 'membership-manager'),
'getresponse_api_key' => '',
'maximum_login_length' => 32,
'maximum_password_length' => 32,
'member_autoresponder' => '',
'member_autoresponder_list' => '',
'member_subscribed_to_autoresponder' => 'no',
'members_initial_category_id' => 0,
'members_initial_status' => 'active',
'minimum_login_length' => 1,
'minimum_password_length' => 5,
'password_reset_email_receiver' => '[membership-user email-address]',
'password_reset_email_sender' => $blogname.' <'.$admin_email.'>',
'password_reset_email_subject' => __('Your New Password', 'membership-manager'),
'removal_notification_email_receiver' => '[membership-user email-address]',
'removal_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'removal_notification_email_subject' => __('Removal Of Your Member Account', 'membership-manager'),
'registration_confirmation_email_receiver' => '[membership-user email-address]',
'registration_confirmation_email_sender' => $blogname.' <'.$admin_email.'>',
'registration_confirmation_email_sent' => 'yes',
'registration_confirmation_email_subject' => __('Your Registration To Our Member Area', 'membership-manager'),
'registration_confirmation_url' => HOME_URL,
'registration_custom_instructions_executed' => 'no',
'registration_notification_email_receiver' => $admin_email,
'registration_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'registration_notification_email_sent' => 'yes',
'registration_notification_email_subject' => __('Registration Of A Member', 'membership-manager').' ([membership-user login])',
'sg_autorepondeur_account_id' => '',
'sg_autorepondeur_activation_code' => '',
'version' => MEMBERSHIP_MANAGER_VERSION);


$initial_options['activation_notification_email_body'] =
__('Hi', 'membership-manager').', [membership-user first-name].

'.__('Your member account has been activated.', 'membership-manager').' '.__('You can login from this page:', 'membership-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


include 'admin-pages.php';
$menu_items = array();
foreach ($admin_pages as $key => $value) { $menu_items[] = $key; }

$initial_options['back_office'] = array(
'back_office_page_summary_displayed' => 'yes',
'back_office_page_undisplayed_modules' => array(),
'links' => array('Documentation', 'Commerce Manager'),
'links_displayed' => 'yes',
'member_area_category_page_summary_displayed' => 'yes',
'member_area_category_page_undisplayed_modules' => array(),
'member_area_page_summary_displayed' => 'yes',
'member_area_page_undisplayed_modules' => array(),
'member_category_page_summary_displayed' => 'no',
'member_category_page_undisplayed_modules' => array(),
'member_page_summary_displayed' => 'yes',
'member_page_undisplayed_modules' => array(),
'menu_displayed' => 'yes',
'menu_items' => $menu_items,
'menu_items_number' => count($menu_items),
'options_page_summary_displayed' => 'yes',
'options_page_undisplayed_modules' => array(),
'statistics_page_undisplayed_columns' => array(),
'statistics_page_undisplayed_rows' => array(),
'title' => 'Membership Manager',
'title_displayed' => 'yes');


$initial_options['deactivation_notification_email_body'] =
__('Hi', 'membership-manager').', [membership-user first-name].



--
'.$blogname.'
'.HOME_URL;


$first_columns = array(
'id',
'login',
'first_name',
'last_name',
'email_address',
'website_name',
'members_areas',
'status',
'date');
$last_columns = array();
foreach ($tables['members'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }

$initial_options['members'] = array(
'columns' => array_merge($first_columns, $last_columns),
'columns_number' => 9,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_column' => 0,
'start_date' => '2011-01-01');


$first_columns = array(
'id',
'name',
'description',
'keywords',
'members_areas',
'url',
'date');
$last_columns = array();
foreach ($tables['members_areas'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }

$initial_options['members_areas'] = array(
'columns' => array_merge($first_columns, $last_columns),
'columns_number' => 7,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_column' => 0,
'start_date' => '2011-01-01');


$initial_options['members_areas_categories'] = $initial_options['members_areas'];


$first_columns = array(
'id',
'name',
'description',
'keywords',
'members_areas',
'date',
'date_utc',
'category_id');
$last_columns = array();
foreach ($tables['members_categories'] as $key => $value) {
if ((!in_array($key, $first_columns)) && ($value['name'] != '')) { $last_columns[] = $key; } }

$initial_options['members_categories'] = array(
'columns' => array_merge($first_columns, $last_columns),
'columns_number' => 8,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_column' => 0,
'start_date' => '2011-01-01');


$initial_options['password_reset_email_body'] =
__('Hi', 'membership-manager').', [membership-user first-name].

'.__('Here are your new login informations:', 'membership-manager').'

'.__('Your login:', 'membership-manager').' [membership-user login]
'.__('Your password:', 'membership-manager').' [membership-user password]

'.__('You can login from this page:', 'membership-manager').'

'.HOME_URL.'

--
'.$blogname.'
'.HOME_URL;


$initial_options['removal_notification_email_body'] = $initial_options['deactivation_notification_email_body'];


$initial_options['registration_confirmation_email_body'] =
__('Thank you for your registration to our member area', 'membership-manager').', [membership-user first-name].
'.__('You can login from this page:', 'membership-manager').'

'.HOME_URL.'

'.__('Your login name:', 'membership-manager').' [membership-user login]
'.__('Your password:', 'membership-manager').' [membership-user password]

--
'.$blogname.'
'.HOME_URL;


$initial_options['registration_custom_instructions'] = '';


$initial_options['registration_notification_email_body'] =
'[membership-user first-name] [membership-user last-name]

'.__('Login name:', 'membership-manager').' [membership-user login]
'.__('Email address:', 'membership-manager').' [membership-user email-address]
'.__('Website name:', 'membership-manager').' [membership-user website-name]
'.__('Website URL:', 'membership-manager').' [membership-user website-url]
'.__('Member area:', 'membership-manager').' [member-area name]

'.__('More informations about this member:', 'membership-manager').'

'.$siteurl.'/wp-admin/admin.php?page=membership-manager-member&id=[membership-user id]';


$initial_options['statistics'] = array(
'filterby' => 'user_agent',
'start_date' => '2011-01-01',
'start_table' => 0,
'tables' => array('members', 'members_categories', 'members_areas', 'members_areas_categories'),
'tables_number' => 0);