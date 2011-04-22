<?php $content_switcher_default_options = array(
'analytics_tracking_admin' => 'no',
'analytics_tracking_author' => 'yes',
'analytics_tracking_contributor' => 'yes',
'analytics_tracking_editor' => 'yes',
'analytics_tracking_id' => 'UA-XXXXXXXX-X',
'analytics_tracking_subscriber' => 'yes',
'analytics_tracking_visitor' => 'yes',
'javascript_enabled' => 'no',
'optimizer_tracking_id' => 'UA-XXXXXXXX-X');

$content_switcher_options = get_option('content_switcher');
foreach ($content_switcher_default_options as $key => $value) {
if ($content_switcher_options[$key] == '') { $content_switcher_options[$key] = $content_switcher_default_options[$key]; } }
update_option('content_switcher', $content_switcher_options);