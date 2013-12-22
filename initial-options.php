<?php $lang = strtolower(substr(WPLANG, 0, 2)); if ($lang == '') { $lang = 'en'; }
foreach (array('admin_email', 'blogname', 'siteurl') as $key) { $$key = get_option($key); }


$initial_options[''] = array(
'default_captcha_type' => 'recaptcha',
'default_recaptcha_theme' => 'red',
'installation_notification_email_receiver' => $admin_email,
'installation_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'installation_notification_email_sent' => 'no',
'installation_notification_email_subject' => __('Installation Of [plugin]', 'installations-manager'),
'invalid_captcha_message' => __('The code you entered for the CAPTCHA is incorrect.', 'installations-manager'),
'invalid_email_address_message' => __('This email address appears to be invalid.', 'installations-manager'),
'recaptcha_private_key' => '',
'recaptcha_public_key' => '',
'unfilled_field_message' => __('This field is required.', 'installations-manager'),
'unfilled_fields_message' => __('Please fill out the required fields.', 'installations-manager'),
'upgrades_email_receiver' => '[webmaster email-address]',
'upgrades_email_sender' => $blogname.' <'.$admin_email.'>',
'upgrades_email_sent' => 'yes',
'upgrades_email_subject' => __('Latest Versions Of The Plugins', 'installations-manager'),
'upgrades_notification_email_receiver' => $admin_email,
'upgrades_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'upgrades_notification_email_sent' => 'no',
'upgrades_notification_email_subject' => __('Upgrades Notification', 'installations-manager').' [website name]',
'version' => INSTALLATIONS_MANAGER_VERSION,
'website_notification_email_receiver' => $admin_email,
'website_notification_email_sender' => $blogname.' <'.$admin_email.'>',
'website_notification_email_sent' => 'yes',
'website_notification_email_subject' => __('New Website', 'installations-manager'));


$initial_options['installation_notification_email_body'] =
__('Name:', 'installations-manager').' [website name]
'.__('URL:', 'installations-manager').' [website url]

'.__('More informations about this website:', 'installations-manager').'

'.$siteurl.'/wp-admin/admin.php?page=installations-manager-website&id=[website id]';


$initial_options['upgrades_email_body'] =
__('Hi', 'installations-manager').'



--
'.$blogname.'
'.HOME_URL;


$initial_options['upgrades_form_code'] =
'';


$initial_options['upgrades_notification_email_body'] = $initial_options['installation_notification_email_body'];


$initial_options['website_notification_email_body'] = $initial_options['installation_notification_email_body'];


if (isset($variables)) { $original['variables'] = $variables; }
$variables = array(
'displayed_columns',
'displayed_links',
'first_columns',
'id',
'last_columns',
'links',
'menu_displayed_items',
'menu_items',
'pages_titles',
'table',
'table_slug',
'tables');
foreach ($variables as $variable) { if (isset($$variable)) { $original[$variable] = $$variable; unset($$variable); } }


include INSTALLATIONS_MANAGER_PATH.'/tables.php';
foreach ($tables as $table_slug => $table) {
$first_columns = array(
'id',
'name',
'language_code',
'date',
'plugins');

$last_columns = array();
foreach ($table as $key => $value) {
if ((!in_array($key, $first_columns)) && (isset($value['name'])) && ($value['name'] != '')) { $last_columns[] = $key; } }
$displayed_columns = array();
for ($i = 0; $i < count($first_columns); $i++) { $displayed_columns[] = $i; }

$initial_options[$table_slug] = array(
'columns' => array_merge($first_columns, $last_columns),
'columns_list_displayed' => 'yes',
'displayed_columns' => $displayed_columns,
'limit' => 10,
'order' => 'desc',
'orderby' => 'id',
'searchby' => '',
'start_date' => '2000-01-01 00:00:00'); }


include INSTALLATIONS_MANAGER_PATH.'/admin-pages.php';
$links = array();
foreach ($admin_links as $key => $value) { $links[] = $key; }
$displayed_links = array();
for ($i = 0; $i < count($links); $i++) { $displayed_links[] = $i; }
$menu_items = array();
$pages_titles = array();
foreach ($admin_pages as $key => $value) {
$menu_items[] = $key;
if (isset($_GET['id'])) { $id = $_GET['id']; unset($_GET['id']); }
$pages_titles[$key] = $value['menu_title'];
if (isset($id)) { $_GET['id'] = $id; unset($id); } }
$menu_displayed_items = array();
foreach ($menu_items as $key => $value) { $menu_displayed_items[] = $key; }

$initial_options['back_office'] = array(
'back_office_page_summary_displayed' => 'yes',
'back_office_page_undisplayed_modules' => array(),
'custom_icon_url' => INSTALLATIONS_MANAGER_URL.'images/icon.png',
'custom_icon_used' => 'yes',
'default_options_links_target' => '_blank',
'displayed_links' => $displayed_links,
'documentations_links_target' => '_blank',
'ids_fields_links_target' => '_blank',
'links' => $links,
'links_displayed' => 'yes',
'menu_displayed' => 'yes',
'menu_displayed_items' => $menu_displayed_items,
'menu_items' => $menu_items,
'menu_title_'.$lang => __('Installations', 'installations-manager'),
'minimum_roles' => array(
	'manage' => 'administrator',
	'view' => 'administrator'),
'options_page_summary_displayed' => 'yes',
'options_page_undisplayed_modules' => array(),
'pages_modules_links_target' => '_blank',
'pages_titles_'.$lang => $pages_titles,
'title' => 'Installations Manager',
'title_displayed' => 'yes',
'urls_fields_links_target' => '_blank',
'website_page_summary_displayed' => 'yes',
'website_page_undisplayed_modules' => array());


foreach ($variables as $variable) { if (isset($original[$variable])) { $$variable = $original[$variable]; } }
if (isset($original['variables'])) { $variables = $original['variables']; }