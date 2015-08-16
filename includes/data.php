<?php global $content_switcher_options;
if (empty($content_switcher_options)) { $content_switcher_options = (array) get_option('content_switcher'); }
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $formatting = 'yes'; }
else {
$atts = array_map('kleor_do_shortcode_in_attribute', (array) $atts);
$field = (isset($atts[0]) ? $atts[0] : '');
foreach (array('default', 'filter') as $key) { $$key = (isset($atts[$key]) ? $atts[$key] : ''); }
$formatting = (((isset($atts['formatting'])) && ($atts['formatting'] == 'no')) ? 'no' : 'yes'); }
$field = str_replace('-', '_', content_switcher_format_nice_name($field));
$data = (isset($content_switcher_options[$field]) ? $content_switcher_options[$field] : '');
$data = (string) ($formatting == 'yes' ? kleor_do_shortcode($data) : $data);
if ($data === '') { $data = $default; }
$data = content_switcher_filter_data($filter, $data);