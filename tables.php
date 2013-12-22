<?php $tables['websites'] = array(
'id' => array('type' => 'int', 'modules' => array('general-informations'), 'name' => __('ID', 'installations-manager'), 'width' => 5),
'name' => array('type' => 'text', 'modules' => array('general-informations'), 'name' => __('Name', 'installations-manager'), 'width' => 15, 'searchby' => __('the name', 'installations-manager')),
'url' => array('type' => 'text', 'constraint' => 'UNIQUE', 'modules' => array('general-informations'), 'name' => __('URL', 'installations-manager'), 'width' => 18, 'searchby' => __('the URL', 'installations-manager')),
'language_code' => array('type' => 'text', 'modules' => array('general-informations'), 'name' => __('Language', 'installations-manager'), 'width' => 12),
'date' => array('type' => 'datetime', 'modules' => array('general-informations'), 'name' => __('Date', 'installations-manager'), 'width' => 18),
'date_utc' => array('type' => 'datetime', 'modules' => array('general-informations'), 'name' => __('Date (UTC)', 'installations-manager'), 'width' => 18),
'plugins' => array('type' => 'text', 'modules' => array('installations'), 'name' => __('Plugins', 'installations-manager'), 'width' => 18, 'searchby' => __('the plugins', 'installations-manager')),
'commerce_manager_installations_dates' => array('type' => 'text', 'modules' => array('installations')),
'affiliation_manager_installations_dates' => array('type' => 'text', 'modules' => array('installations')),
'membership_manager_installations_dates' => array('type' => 'text', 'modules' => array('installations')),
'optin_manager_installations_dates' => array('type' => 'text', 'modules' => array('installations')),
'contact_manager_installations_dates' => array('type' => 'text', 'modules' => array('installations')));

foreach (array(
'affiliation_manager_websites',
'commerce_manager_websites',
'contact_manager_websites',
'membership_manager_websites',
'optin_manager_websites') as $key) { $tables[$key] = $tables['websites']; }