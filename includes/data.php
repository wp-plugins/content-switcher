<?php global $content_switcher_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; }
else {
$field = (isset($atts[0]) ? $atts[0] : '');
$default = (isset($atts['default']) ? $atts['default'] : '');
$filter = (isset($atts['filter']) ? $atts['filter'] : ''); }
$field = str_replace('-', '_', content_switcher_format_nice_name($field));
$data = (isset($content_switcher_options[$field]) ? $content_switcher_options[$field] : '');
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = content_switcher_filter_data($filter, $data);