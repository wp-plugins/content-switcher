<?php load_plugin_textdomain('content-switcher', false, 'content-switcher/languages');
include CONTENT_SWITCHER_PATH.'/initial-options.php';
$options = (array) get_option('content_switcher');
$current_options = $options;
if ((isset($options[0])) && ($options[0] === false)) { unset($options[0]); }
foreach ($initial_options as $key => $value) {
if (($key == 'version') || (!isset($options[$key])) || ($options[$key] == '')) { $options[$key] = $value; } }
if ($options != $current_options) { update_option('content_switcher', $options); }