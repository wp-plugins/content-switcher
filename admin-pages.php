<?php $admin_links = array(
'Commerce Manager' => array('name' => __('Commerce', 'installations-manager')),
'Affiliation Manager' => array('name' => __('Affiliation', 'installations-manager')),
'Membership Manager' => array('name' => __('Membership', 'installations-manager')),
'Optin Manager' => array('name' => __('Optin', 'installations-manager')),
'Contact Manager' => array('name' => __('Contact', 'installations-manager')));

$admin_pages = array(
'' => array('page_title' => 'Installations Manager ('.__('Options', 'installations-manager').')', 'menu_title' => __('Options', 'installations-manager'), 'file' => 'options-page.php'),
'website' => array('page_title' => 'Installations Manager ('.__('Website', 'installations-manager').')', 'menu_title' => (((isset($_GET['page'])) && ($_GET['page'] == 'installations-manager-website') && (isset($_GET['id']))) ? (((isset($_GET['action'])) && ($_GET['action'] == 'delete')) ? __('Delete Website', 'installations-manager') : __('Edit Website', 'installations-manager')) : __('Add Website', 'installations-manager')), 'file' => 'website-page.php'),
'websites' => array('page_title' => 'Installations Manager ('.__('Websites', 'installations-manager').')', 'menu_title' => __('Websites', 'installations-manager'), 'file' => 'table-page.php'),
'commerce-manager-websites' => array('page_title' => 'Installations Manager (Commerce Manager)', 'menu_title' => 'Commerce Manager', 'file' => 'table-page.php'),
'affiliation-manager-websites' => array('page_title' => 'Installations Manager (Affiliation Manager)', 'menu_title' => 'Affiliation Manager', 'file' => 'table-page.php'),
'membership-manager-websites' => array('page_title' => 'Installations Manager (Membership Manager)', 'menu_title' => 'Membership Manager', 'file' => 'table-page.php'),
'optin-manager-websites' => array('page_title' => 'Installations Manager (Optin Manager)', 'menu_title' => 'Optin Manager', 'file' => 'table-page.php'),
'contact-manager-websites' => array('page_title' => 'Installations Manager (Contact Manager)', 'menu_title' => 'Contact Manager', 'file' => 'table-page.php'),
'back_office' => array('page_title' => 'Installations Manager ('.__('Back Office', 'installations-manager').')', 'menu_title' => __('Back Office', 'installations-manager'), 'file' => 'back-office-page.php'));

$modules['back_office'] = array(
'capabilities' => array('name' => __('Capabilities', 'installations-manager')),
'icon' => array('name' => __('Icon', 'installations-manager')),
'top' => array('name' => __('Top', 'installations-manager')),
'menu' => array('name' => __('Menu', 'installations-manager')),
'links' => array('name' => __('Links', 'installations-manager')),
'options-page' => array('name' => __('<em>Options</em> page', 'installations-manager')),
'website-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Website</em> page', 'installations-manager') : __('<em>Add Website</em> page', 'installations-manager'))),
'back-office-page' => array('name' => __('<em>Back Office</em> page', 'installations-manager'), 'required' => 'yes'));

$modules['options'] = array(
'website-notification-email' => array('name' => __('Website notification email', 'installations-manager')),
'installation-notification-email' => array('name' => __('Installation notification email', 'installations-manager')),
'upgrades-email' => array('name' => __('Upgrades email', 'installations-manager')),
'upgrades-notification-email' => array('name' => __('Upgrades notification email', 'installations-manager')),
'upgrades-form' => array('name' => __('Upgrades form', 'installations-manager'), 'modules' => array(
	'captcha' => array('name' => __('CAPTCHA', 'installations-manager')),
	'error-messages' => array('name' => __('Error messages', 'installations-manager')))),
'options-page' => array('name' => __('<em>Options</em> page', 'installations-manager')));

$modules['website'] = array(
'general-informations' => array('name' => __('General informations', 'installations-manager'), 'required' => 'yes'),
'installations' => array('name' => __('Installations', 'installations-manager')),
'website-page' => array('name' => (isset($_GET['id']) ? __('<em>Edit Website</em> page', 'installations-manager') : __('<em>Add Website</em> page', 'installations-manager'))));

$roles = array(
'administrator' => array('name' => __('Administrator', 'installations-manager'), 'capability' => 'manage_options'),
'editor' => array('name' => __('Editor', 'installations-manager'), 'capability' => 'edit_pages'),
'author' => array('name' => __('Author', 'installations-manager'), 'capability' => 'publish_posts'),
'contributor' => array('name' => __('Contributor', 'installations-manager'), 'capability' => 'edit_posts'),
'subscriber' => array('name' => __('Subscriber', 'installations-manager'), 'capability' => 'read'));