<?php include_once 'initial-options.php';

$content_switcher_options = get_option('content_switcher');
foreach ($content_switcher_initial_options as $key => $value) {
if ($content_switcher_options[$key] == '') { $content_switcher_options[$key] = $content_switcher_initial_options[$key]; } }
update_option('content_switcher', $content_switcher_options);