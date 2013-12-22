<?php global $installations_manager_options;
if (empty($installations_manager_options)) { $installations_manager_options = (array) get_option('installations_manager'); }
if (is_string($atts)) { $field = $atts; $decimals = ''; $default = ''; $filter = ''; $part = 0; }
else {
$atts = array_map('installations_do_shortcode', (array) $atts);
$field = (isset($atts[0]) ? $atts[0] : '');
foreach (array('decimals', 'default', 'filter') as $key) { $$key = (isset($atts[$key]) ? $atts[$key] : ''); }
$part = (int) (isset($atts['part']) ? $atts['part'] : 0); }
$field = str_replace('-', '_', format_nice_name($field));
if (($field == 'code') || (substr($field, -10) == 'email_body') || (substr($field, -9) == 'form_code')) {
$data = get_option(substr('installations_manager_'.$field, 0, 64)); }
else { $data = (isset($installations_manager_options[$field]) ? $data = $installations_manager_options[$field] : ''); }
if ($part > 0) { $data = explode(',', $data); $data = (isset($data[$part - 1]) ? trim($data[$part - 1]) : ''); }
$data = (string) do_shortcode($data);
if ($data === '') { $data = $default; }
$data = installations_format_data($field, $data);
if ($data === '') { $data = $default; }
$data = installations_filter_data($filter, $data);
$data = installations_decimals_data($decimals, $data);