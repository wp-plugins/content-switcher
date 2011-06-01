<?php include_once 'initial-options.php';
$options = get_option('content_switcher');
foreach ($initial_options as $key => $value) {
if ($options[$key] == '') { $options[$key] = $initial_options[$key]; } }
update_option('content_switcher', $options);