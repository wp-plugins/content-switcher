<?php $admin_pages = array(
'' => array('page_title' => 'Membership Manager ('.__('Options', 'membership-manager').')', 'menu_title' => __('Options', 'membership-manager'), 'function' => 'membership_manager_options_page'),
'member_area' => array('page_title' => 'Membership Manager ('.__('Member Area', 'membership-manager').')', 'menu_title' => ((($_GET['page'] == 'membership-manager-member-area') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Member Area', 'membership-manager') : __('Edit Member Area', 'membership-manager')) : __('Add Member Area', 'membership-manager')), 'function' => 'membership_manager_member_area_page'),
'members_areas' => array('page_title' => 'Membership Manager ('.__('Members Areas', 'membership-manager').')', 'menu_title' => __('Members Areas', 'membership-manager'), 'function' => 'membership_manager_table_page'),
'member_area_category' => array('page_title' => 'Membership Manager ('.__('Member Area Category', 'membership-manager').')', 'menu_title' => ((($_GET['page'] == 'membership-manager-member-area-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Member Area Category', 'membership-manager') : __('Edit Member Area Category', 'membership-manager')) : __('Add Member Area Category', 'membership-manager')), 'function' => 'membership_manager_member_area_page'),
'members_areas_categories' => array('page_title' => 'Membership Manager ('.__('Members Areas Categories', 'membership-manager').')', 'menu_title' => __('Members Areas Categories', 'membership-manager'), 'function' => 'membership_manager_table_page'),
'member' => array('page_title' => 'Membership Manager ('.__('Member', 'membership-manager').')', 'menu_title' => ((($_GET['page'] == 'membership-manager-member') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Member', 'membership-manager') : __('Edit Member', 'membership-manager')) : __('Add Member', 'membership-manager')), 'function' => 'membership_manager_member_page'),
'members' => array('page_title' => 'Membership Manager ('.__('Members', 'membership-manager').')', 'menu_title' => __('Members', 'membership-manager'), 'function' => 'membership_manager_table_page'),
'member_category' => array('page_title' => 'Membership Manager ('.__('Member Category', 'membership-manager').')', 'menu_title' => ((($_GET['page'] == 'membership-manager-member-category') && (isset($_GET['id']))) ? ($_GET['action'] == 'delete' ? __('Delete Member Category', 'membership-manager') : __('Edit Member Category', 'membership-manager')) : __('Add Member Category', 'membership-manager')), 'function' => 'membership_manager_member_page'),
'members_categories' => array('page_title' => 'Membership Manager ('.__('Members Categories', 'membership-manager').')', 'menu_title' => __('Members Categories', 'membership-manager'), 'function' => 'membership_manager_table_page'),
'statistics' => array('page_title' => 'Membership Manager ('.__('Statistics', 'membership-manager').')', 'menu_title' => __('Statistics', 'membership-manager'), 'function' => 'membership_manager_statistics_page'),
'back_office' => array('page_title' => 'Membership Manager ('.__('Back Office', 'membership-manager').')', 'menu_title' => __('Back Office', 'membership-manager'), 'function' => 'membership_manager_back_office_page'));

$modules['back_office'] = array(
'top' => array('name' => __('Top', 'membership-manager')),
'menu' => array('name' => __('Menu', 'membership-manager')),
'options-page' => array('name' => __('<em>Options</em> page', 'membership-manager')),
'member-area-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member Area</em> page', 'membership-manager') : __('<em>Add Member Area</em> page', 'membership-manager'))),
'member-area-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member Area Category</em> page', 'membership-manager') : __('<em>Add Member Area Category</em> page', 'membership-manager'))),
'member-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member</em> page', 'membership-manager') : __('<em>Add Member</em> page', 'membership-manager'))),
'statistics-page' => array('name' => __('<em>Statistics</em> page', 'membership-manager')),
'back-office-page' => array('name' => __('<em>Back Office</em> page', 'membership-manager'), 'required' => 'yes'));

$modules['member'] = array(
'personal-informations' => array('name' => __('Personal informations', 'membership-manager'), 'required' => 'yes'),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'membership-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'membership-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'membership-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'membership-manager')),
'member-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member</em> page', 'membership-manager') : __('<em>Add Member</em> page', 'membership-manager'))));

$modules['member_area'] = array(
'general-informations' => array('name' => __('General informations', 'membership-manager'), 'required' => 'yes'),
'registration' => array('name' => __('Registration', 'membership-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'membership-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'membership-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'membership-manager')),
'custom-instructions' => array('name' => __('Custom instructions', 'membership-manager')),
'member-area-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member Area</em> page', 'membership-manager') : __('<em>Add Member Area</em> page', 'membership-manager'))));

$modules['member_area_category'] = $modules['member_area'];
unset($modules['member_area_category']['member-area-page']);
$modules['member_area_category'] = array_merge($modules['member_area_category'], array(
'member-area-category-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Member Area Category</em> page', 'membership-manager') : __('<em>Add Member Area Category</em> page', 'membership-manager')))));

$modules['member_category'] = array(
'general-informations' => array('name' => __('General informations', 'membership-manager'), 'required' => 'yes'));

$modules['options'] = array(
'registration' => array('name' => __('Registration', 'membership-manager')),
'registration-confirmation-email' => array('name' => __('Registration confirmation email', 'membership-manager')),
'registration-notification-email' => array('name' => __('Registration notification email', 'membership-manager')),
'autoresponders' => array('name' => __('Autoresponders', 'membership-manager')),
'autoresponders-integration' => array('name' => __('Autoresponders integration', 'membership-manager'), 'modules' => array(
	'aweber' => array('name' => 'AWeber'),
	'cybermailing' => array('name' => 'CyberMailing'),
	'getresponse' => array('name' => 'GetResponse'),
	'sg-autorepondeur' => array('name' => 'SG Autorépondeur'))),
'custom-instructions' => array('name' => __('Custom instructions', 'membership-manager')),
'password-reset-email' => array('name' => __('Password reset email', 'membership-manager')),
'activation-notification-email' => array('name' => __('Activation notification email', 'membership-manager')),
'deactivation-notification-email' => array('name' => __('Deactivation notification email', 'membership-manager')),
'removal-notification-email' => array('name' => __('Removal notification email', 'membership-manager')),
'options-page' => array('name' => __('<em>Options</em> page', 'membership-manager')));

$statistics_columns = array(
'data' => array('name' => __('Data', 'membership-manager'), 'required' => 'yes'),
'quantity' => array('name' => __('Quantity', 'membership-manager')),
'members_percentage' => array('name' => __('Percentage of members', 'membership-manager')));

$statistics_rows = array(
'members' => array('name' => __('Members', 'membership-manager')),
'active_members' => array('name' => __('Active members', 'membership-manager')),
'inactive_members' => array('name' => __('Inactive members', 'membership-manager')),
'members_categories' => array('name' => __('Members categories', 'membership-manager')),
'members_areas' => array('name' => __('Members areas', 'membership-manager')),
'members_areas_categories' => array('name' => __('Members areas categories', 'membership-manager')));