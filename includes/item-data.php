<?php global $wpdb;
switch ($type) {
case 'website': $table = 'websites'; $default_field = 'name'; }
$GLOBALS[$type.'_data'] = (array) (isset($GLOBALS[$type.'_data']) ? $GLOBALS[$type.'_data'] : array());
if ((isset($GLOBALS[$type.'_id'])) && ((!isset($GLOBALS[$type.'_data']['id'])) || ($GLOBALS[$type.'_data']['id'] != $GLOBALS[$type.'_id']))) {
$n = $GLOBALS[$type.'_id']; $GLOBALS[$type.$n.'_data'] = (array) (isset($GLOBALS[$type.$n.'_data']) ? $GLOBALS[$type.$n.'_data'] : array());
if ((isset($GLOBALS[$type.$n.'_data']['id'])) && ($GLOBALS[$type.$n.'_data']['id'] == $GLOBALS[$type.'_id'])) { $GLOBALS[$type.'_data'] = $GLOBALS[$type.$n.'_data']; }
elseif ($GLOBALS[$type.'_id'] > 0) { $GLOBALS[$type.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."installations_manager_".$table." WHERE id = ".$GLOBALS[$type.'_id'], OBJECT); } }
$item_data = $GLOBALS[$type.'_data'];
if (is_string($atts)) { $is_array = false; $field = $atts; $decimals = ''; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$is_array = true;
$atts = array_map('installations_do_shortcode', (array) $atts);
$field = (isset($atts[0]) ? $atts[0] : '');
foreach (array('decimals', 'default', 'filter') as $key) {
$$key = (isset($atts[$key]) ? $atts[$key] : '');
if (isset($atts[$key])) { unset($atts[$key]); } }
$id = (int) (isset($atts['id']) ? $atts['id'] : 0);
$part = (int) (isset($atts['part']) ? $atts['part'] : 0); }
$field = str_replace('-', '_', format_nice_name($field));
if ($field == '') { $field = $default_field; }
if (($id > 0) && ((!isset($item_data['id'])) || ($id != $item_data['id']))) {
foreach (array($type.'_id', $type.'_data') as $key) {
if (isset($GLOBALS[$key])) { $original[$key] = $GLOBALS[$key]; } }
if ((!isset($GLOBALS[$type.$id.'_data'])) || (!isset($GLOBALS[$type.$id.'_data']['id'])) || ($GLOBALS[$type.$id.'_data']['id'] != $id)) {
$GLOBALS[$type.$id.'_data'] = (array) $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."installations_manager_".$table." WHERE id = $id", OBJECT); }
$item_data = $GLOBALS[$type.$id.'_data'];
$GLOBALS[$type.'_id'] = $id; $GLOBALS[$type.'_data'] = $item_data; }
$data = (isset($item_data[$field]) ? $item_data[$field] : '');
if ($part > 0) { $data = explode(',', $data); $data = (isset($data[$part - 1]) ? trim($data[$part - 1]) : ''); }
$data = (string) do_shortcode($data);
if ($data === '') { $data = $default; }
$data = installations_format_data($field, $data);
if ($data === '') { $data = $default; }
$data = installations_filter_data($filter, $data);
$data = installations_decimals_data($decimals, $data);
foreach (array($type.'_id', $type.'_data') as $key) {
if (isset($original[$key])) { $GLOBALS[$key] = $original[$key]; } }