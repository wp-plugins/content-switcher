<?php $form_id = $GLOBALS['installations_form_id'];
$prefix = $GLOBALS['installations_form_prefix'];
$atts = installations_shortcode_atts(array(0 => 'country', 'required' => 'no'), $atts);
$markup = '';
$name = str_replace('-', '_', format_nice_name($atts[0]));
$GLOBALS['installations_field_name'] = $name;
$GLOBALS[$prefix.'fields'][] = $name;
if ((in_array($name, $GLOBALS[$prefix.'required_fields'])) && ($atts['required'] != 'required')) { $atts['required'] = 'yes'; }
foreach (array($name, str_replace('_', '-', $name)) as $key) {
if (((!isset($_POST[$prefix.$name])) || ($_POST[$prefix.$name] == '')) && (isset($_GET[$key]))) { $_POST[$prefix.$name] = htmlspecialchars($_GET[$key]); } }
if ((!isset($_POST[$prefix.'submit'])) && ((!isset($_POST[$prefix.$name])) || ($_POST[$prefix.$name] == ''))) {
include INSTALLATIONS_MANAGER_PATH.'/libraries/personal-informations.php';
if (in_array($name, $personal_informations)) {
if ((function_exists('affiliation_session')) && (affiliation_session())) { $_POST[$prefix.$name] = affiliate_data($name); }
elseif ((function_exists('commerce_session')) && (commerce_session())) { $_POST[$prefix.$name] = client_data($name); }
elseif ((function_exists('membership_session')) && (membership_session())) { $_POST[$prefix.$name] = member_data($name); }
elseif ((function_exists('is_user_logged_in')) && (is_user_logged_in()) && (function_exists('current_user_can')) && (!current_user_can('edit_pages')) && (!current_user_can('manage_options'))) { $_POST[$prefix.$name] = installations_user_data($name); } } }
if ((isset($_POST[$prefix.'submit'])) && (in_array($atts['required'], array('required', 'yes'))) && ((!isset($_POST[$prefix.$name])) || ($_POST[$prefix.$name] == ''))) { $GLOBALS[$prefix.$name.'_error'] = $GLOBALS[$prefix.'unfilled_field_message']; }
if (((!isset($GLOBALS['form_focus'])) || ($GLOBALS['form_focus'] == '')) && ((!isset($_POST[$prefix.$name])) || ($_POST[$prefix.$name] == ''))) { $GLOBALS['form_focus'] = 'document.getElementById("'.$prefix.$name.'").focus();'; }
foreach ($atts as $key => $value) {
switch ($key) {
case 'required': if (in_array($value, array('required', 'yes'))) {
$GLOBALS[$prefix.'required_fields'][] = $name; if ($value == $key) { $markup .= ' '.$key.'="'.$key.'"'; } } break;
default: if ((!in_array($key, array('id', 'name'))) && (is_string($key)) && ($value != '')) { $c = (strstr($value, '"') ? "'" : '"'); $markup .= ' '.$key.'='.$c.$value.$c; } } }
if (isset($GLOBALS[$prefix.$name.'_error'])) { $GLOBALS['form_error'] = 'yes'; }
$content = '<select name="'.$prefix.$name.'" id="'.$prefix.$name.'"'.$markup.'>'.do_shortcode($content).'</select>';