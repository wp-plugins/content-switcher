<?php function installations_counter_tag($atts) {
$atts = array_map('installations_do_shortcode', (array) $atts);
extract(shortcode_atts(array('data' => '', 'decimals' => '0/2', 'filter' => ''), $atts));
$string = $GLOBALS['installations_'.str_replace('-', '_', format_nice_name($data))];
$string = installations_filter_data($filter, $string);
$string = installations_decimals_data($decimals, $string);
return $string; }


function installations_counter($atts, $content) {
$type = '';
include INSTALLATIONS_MANAGER_PATH.'/includes/counter.php';
return $content; }