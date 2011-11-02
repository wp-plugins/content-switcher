<?php
/*
Plugin Name: Membership Manager
Plugin URI: http://www.kleor-editions.com/membership-manager
Description: Allows you to create and manage your members areas.
Version: 0.9
Author: Kleor
Author URI: http://www.kleor-editions.com
Text Domain: membership-manager
*/


load_plugin_textdomain('membership-manager', false, 'membership-manager/languages');

if (!defined('HOME_URL')) { define('HOME_URL', get_option('home')); }
if (!defined('UTC_OFFSET')) { define('UTC_OFFSET', get_option('gmt_offset')); }
define('MEMBERSHIP_MANAGER_URL', plugin_dir_url(__FILE__));
$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
define('MEMBERSHIP_MANAGER_VERSION', $plugin_data['Version']);

if (is_admin()) { include_once dirname(__FILE__).'/admin.php'; }

global $wpdb;
$membership_manager_options = get_option('membership_manager');
if (($membership_manager_options) && ($membership_manager_options['version'] != MEMBERSHIP_MANAGER_VERSION)) {
include_once dirname(__FILE__).'/admin.php';
install_membership_manager(); }


membership_fix_url();
membership_session('');
if (!is_admin()) { membership_instructions(); }


function add_member($member) {
global $wpdb;
include 'tables.php';
foreach ($tables['members'] as $key => $value) { $keys_list .= $key.","; $values_list .= "'".($key == 'password' ? hash('sha256', $member[$key]) : $member[$key])."',"; }
$results = $wpdb->query("INSERT INTO ".$wpdb->prefix."membership_manager_members (".substr($keys_list, 0, -1).") VALUES(".substr($values_list, 0, -1).")");
$_GET['member_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$member['login']."'", OBJECT);
$_GET['member_data']->password = $member['password'];
$members_areas = array_unique(preg_split('#[^0-9]#', $member['members_areas']));
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
foreach (add_member_fields() as $field) {
if (is_admin()) { $member[$field] = stripslashes(do_shortcode($member[$field])); }
else { $member[$field] = member_area_data($field); } }

if ($member['registration_confirmation_email_sent'] == 'yes') {
$sender = $member['registration_confirmation_email_sender'];
$receiver = $member['registration_confirmation_email_receiver'];
$subject = $member['registration_confirmation_email_subject'];
$body = $member['registration_confirmation_email_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }
if ($member['registration_notification_email_sent'] == 'yes') {
$sender = $member['registration_notification_email_sender'];
$receiver = $member['registration_notification_email_receiver'];
$subject = $member['registration_notification_email_subject'];
$body = $member['registration_notification_email_body'];
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers); }

include 'autoresponders.php';
include_once 'autoresponders-functions.php';
$_GET['autoresponder_subscription'] = '';
if ($member['member_subscribed_to_autoresponder'] == 'yes') {
subscribe_to_autoresponder($member['member_autoresponder'], $member['member_autoresponder_list'], $member); }

if ($member['registration_custom_instructions_executed'] == 'yes') {
eval(membership_format_instructions($member['registration_custom_instructions'])); } }


function add_member_fields() {
return array(
'registration_confirmation_email_sent',
'registration_confirmation_email_sender',
'registration_confirmation_email_receiver',
'registration_confirmation_email_subject',
'registration_confirmation_email_body',
'registration_notification_email_sent',
'registration_notification_email_sender',
'registration_notification_email_receiver',
'registration_notification_email_subject',
'registration_notification_email_body',
'member_subscribed_to_autoresponder',
'member_autoresponder',
'member_autoresponder_list',
'registration_custom_instructions_executed',
'registration_custom_instructions'); }


function member_area_category_data($atts) {
global $wpdb;
if ((isset($_GET['member_area_category_id'])) && ($_GET['member_area_category_data']->id != $_GET['member_area_category_id'])) {
$_GET['member_area_category_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members_areas_categories WHERE id = '".$_GET['member_area_category_id']."'", OBJECT); }
$member_area_category_data = $_GET['member_area_category_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['category']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', membership_format_nice_name($field));
if ($field == '') { $field = 'name'; }
if (($id == 0) || ($id == $member_area_category_data->id)) { $data = $member_area_category_data->$field; }
else {
if ($_GET['member_area_category'.$id.'_data']->id != $id) {
$_GET['member_area_category'.$id.'_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members_areas_categories WHERE id = '$id'", OBJECT); }
$member_area_category_data = $_GET['member_area_category'.$id.'_data'];
$data =  $member_area_category_data->$field; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) $data;
if ($data != '') { $data = membership_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($member_area_category_data->category_id > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $member_area_category_data->category_id;
$data = member_area_category_data($atts); }
elseif ($data == '') { $data = membership_data($atts); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = membership_format_data($field, $data);
$data = membership_filter_data($filter, $data);
return $data; }


function member_area_data($atts) {
if ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return member_area_category_data($atts); }
else {
global $wpdb;
if ((isset($_GET['member_area_id'])) && ($_GET['member_area_data']->id != $_GET['member_area_id'])) {
$_GET['member_area_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members_areas WHERE id = '".$_GET['member_area_id']."'", OBJECT); }
$member_area_data = $_GET['member_area_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['id']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', membership_format_nice_name($field));
if ($field == '') { $field = 'name'; }
if (($id == 0) || ($id == $member_area_data->id)) { $data = $member_area_data->$field; }
else {
if (isset($_GET['member_area_id'])) { $original_member_area_id = $_GET['member_area_id']; }
if (isset($_GET['member_area_data'])) { $original_member_area_data = $_GET['member_area_data']; }
if ($_GET['member_area'.$id.'_data']->id != $id) {
$_GET['member_area'.$id.'_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members_areas WHERE id = '$id'", OBJECT); }
$member_area_data = $_GET['member_area'.$id.'_data'];
$_GET['member_area_id'] = $id; $_GET['member_area_data'] = $member_area_data;
$data =  $member_area_data->$field; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) $data;
if ($data != '') { $data = membership_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($member_area_data->category_id > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $member_area_data->category_id;
$data = member_area_category_data($atts); }
elseif ($data == '') { $data = membership_data($atts); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = membership_format_data($field, $data);
$data = membership_filter_data($filter, $data);
if (isset($original_member_area_id)) { $_GET['member_area_id'] = $original_member_area_id; }
if (isset($original_member_area_data)) { $_GET['member_area_data'] = $original_member_area_data; }
return $data; } }

add_shortcode('member-area', 'member_area_data');


function member_category_data($atts) {
global $wpdb;
if ((isset($_GET['member_category_id'])) && ($_GET['member_category_data']->id != $_GET['member_category_id'])) {
$_GET['member_category_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members_categories WHERE id = '".$_GET['member_category_id']."'", OBJECT); }
$member_category_data = $_GET['member_category_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['category']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', membership_format_nice_name($field));
if ($field == '') { $field = 'name'; }
if (($id == 0) || ($id == $member_category_data->id)) { $data = $member_category_data->$field; }
else {
if ($_GET['member_category'.$id.'_data']->id != $id) {
$_GET['member_category'.$id.'_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members_categories WHERE id = '$id'", OBJECT); }
$member_category_data = $_GET['member_category'.$id.'_data'];
$data = $member_category_data->$field; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) $data;
if ($data != '') { $data = membership_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($member_category_data->category_id > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $member_category_data->category_id;
$data = member_category_data($atts); }
elseif ($data == '') { $data = membership_data($atts); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = membership_format_data($field, $data);
$data = membership_filter_data($filter, $data);
return $data; }


function member_data($atts) {
if ((is_array($atts)) && (!isset($atts['id'])) && (isset($atts['category']))) { return member_category_data($atts); }
else {
global $wpdb;
if ((!is_admin()) && (isset($_SESSION['m_login'])) && ($_GET['member_data']->login != $_SESSION['m_login'])) {
$_GET['member_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_SESSION['m_login']."'", OBJECT); }
$member_data = $_GET['member_data'];
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $id = 0; $part = 0; }
else {
$field = $atts[0];
$default = $atts['default'];
unset($atts['default']);
$filter = $atts['filter'];
unset($atts['filter']);
$id = (int) do_shortcode(str_replace(array('(', ')'), array('[', ']'), $atts['id']));
$part = (int) $atts['part']; }
$field = str_replace('-', '_', membership_format_nice_name($field));
if ($field == '') { $field = 'login'; }
if (($id == 0) || ($id == $member_data->id)) { $data = $member_data->$field; }
else {
if ($_GET['member'.$id.'_data']->id != $id) {
$_GET['member'.$id.'_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE id = '$id'", OBJECT); }
$member_data = $_GET['member'.$id.'_data'];
$data = $member_data->$field; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) $data;
if ($data != '') { $data = membership_format_data($field, $data); }
$data = (string) $data;
if (($data == '') && ($member_data->category_id > 0)) {
if (is_string($atts)) { $atts = array($field); }
$atts['category'] = $member_data->category_id;
$data = member_category_data($atts); }
elseif ($data == '') { $data = membership_data($atts); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = membership_format_data($field, $data);
$data = membership_filter_data($filter, $data);
return $data; } }

add_shortcode('membership-user', 'member_data');


function members_areas_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."membership_manager_members_areas_categories WHERE id = '$id'", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


function members_areas_list($members_areas) {
global $wpdb;
$list = array();
return $list; }


function members_categories_list($id) {
global $wpdb;
$id = (int) $id;
$list = array($id);
while ($id > 0) {
$category = $wpdb->get_row("SELECT category_id FROM ".$wpdb->prefix."membership_manager_members_categories WHERE id = '$id'", OBJECT);
if ($category) { $id = $category->category_id; }
if ((!$category) || (in_array($id, $list))) { $id = 0; }
$list[] = $id; }
return $list; }


function membership_content($atts, $content) {
extract(shortcode_atts(array('id' => ''), $atts));
$content = explode('[other]', do_shortcode($content));
if (membership_session($id)) { $n = 0; } else { $n = 1; }
return $content[$n]; }

add_shortcode('membership-content', 'membership_content');


function membership_counter($atts, $content) {
global $wpdb;
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
extract(shortcode_atts(array('data' => '', 'limit' => '', 'range' => ''), $atts));

$data = str_replace('_', '-', membership_format_nice_name($data));
switch ($data) {
case 'members': $table = $wpdb->prefix.'membership_manager_members'; $field = ''; break;
case 'members-areas': $table = $wpdb->prefix.'membership_manager_members_areas'; $field = ''; break;
case 'members-areas-categories': $table = $wpdb->prefix.'membership_manager_members_areas_categories'; $field = ''; break;
case 'members-categories': $table = $wpdb->prefix.'membership_manager_members_categories'; $field = ''; break;
default: $table = $wpdb->prefix.'membership_manager_members'; $field = ''; }

$range = str_replace('_', '-', membership_format_nice_name($range));
if (is_numeric($range)) {
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$start_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET - 86400*$range);
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; }
else { switch ($range) {
case 'previous-month':
$Y = (int) date('Y', time() + 3600*UTC_OFFSET);
$M = (int) date('n', time() + 3600*UTC_OFFSET);
if ($M == 1) { $m = 12; $y = $Y - 1; }
else { $m = $M - 1; $y = $Y; }
if ($M < 10) { $M = '0'.$M; }
if ($m < 10) { $m = '0'.$m; }
$start_date = $y.'-'.$m.'-01 00:00:00';
$end_date = $Y.'-'.$M.'-01 00:00:00';
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; break;
case 'previous-year':
$Y = (int) date('Y', time() + 3600*UTC_OFFSET);
$y = $Y - 1;
$start_date = $y.'-01-01 00:00:00';
$end_date = $y.'-12-31 23:59:59';
$date_criteria = "AND (date BETWEEN '".$start_date."' AND '".$end_date."')"; break;
default: $date_criteria = ''; } }

if (is_string($table)) {
if ($field == '') {
$row = $wpdb->get_row("SELECT count(*) as total FROM $table WHERE id > 0 $date_criteria", OBJECT);
$data = (int) $row->total; }
else {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table WHERE id > 0 $date_criteria", OBJECT);
$data = round(100*$row->total)/100; } }

else {
$data = 0; foreach ($table as $table_name) {
$row = $wpdb->get_row("SELECT SUM($field) AS total FROM $table_name WHERE id > 0 $date_criteria", OBJECT);
$data = $data + round(100*$row->total)/100; } }

if ($limit == '') { $limit = '0'; }
else { $limit = '0/'.$limit; }
$limit = preg_split('#[^0-9]#', $limit);
$n = count($limit);

$i = 0; while (($i < $n) && ($limit[$i] <= $data)) { $k = $i; $i = $i + 1; }
if ($i < $n) { $remaining_number = $limit[$i] - $data; $total_remaining_number = $limit[$n - 1] - $data; }
else { $i = $n - 1; $remaining_number = 0; $total_remaining_number = 0; }

$content = explode('[after]', do_shortcode($content));
$content[$k] = str_ireplace('[limit]', $limit[$i], $content[$k]);
$content[$k] = str_ireplace('[total-limit]', $limit[$n - 1], $content[$k]);
$content[$k] = str_ireplace('[number]', $data - $limit[$k], $content[$k]);
$content[$k] = str_ireplace('[total-number]', $data, $content[$k]);
$content[$k] = str_ireplace('[remaining-number]', $remaining_number, $content[$k]);
$content[$k] = str_ireplace('[total-remaining-number]', $total_remaining_number, $content[$k]);

return $content[$k]; }

add_shortcode('membership-counter', 'membership_counter');
add_shortcode('membership-data-counter', 'membership_counter');


function membership_data($atts) {
global $membership_manager_options;
if (is_string($atts)) { $field = $atts; $default = ''; $filter = ''; $part = 0; }
else { $field = $atts[0]; $default = $atts['default']; $filter = $atts['filter']; $part = (int) $atts['part']; }
$field = str_replace('-', '_', membership_format_nice_name($field));
if ($field == '') { $field = 'version'; }
if ((strstr($field, 'email_body')) || ($field == 'registration_custom_instructions')) { $data = get_option('membership_manager_'.$field); }
else { $data = $membership_manager_options[$field]; }
if ($part > 0) { $data = explode(',', $data); $data = trim($data[$part - 1]); }
$data = (string) do_shortcode($data);
if ($data == '') { $data = $default; }
$data = membership_format_data($field, $data);
$data = membership_filter_data($filter, $data);
return $data; }

add_shortcode('membership-manager', 'membership_data');


function membership_date_picker_css() {
global $post;
if ((strstr($post->post_content, '[membership-statistics-form'))
 || ($_GET['page'] == 'membership-manager-member')
 || ($_GET['page'] == 'membership-manager-member-area')
 || ($_GET['page'] == 'membership-manager-member-area-category')
 || ($_GET['page'] == 'membership-manager-member-category')
 || ($_GET['page'] == 'membership-manager-members')
 || ($_GET['page'] == 'membership-manager-members-areas')
 || ($_GET['page'] == 'membership-manager-members-areas-categories')
 || ($_GET['page'] == 'membership-manager-members-categories')
 || ($_GET['page'] == 'membership-manager-statistics')) { ?>
<style type="text/css">
table.jCalendar {
  background: #c0c0c0;
  border: 1px solid #000000;
  border-collapse: separate;
  border-spacing: 2px;
}

table.jCalendar td {
  background: #e0e0e0;
  color: #000000;
  padding: 3px 5px;
  text-align: center;
}

table.jCalendar td.disabled, table.jCalendar td.unselectable, table.jCalendar td.unselectable:hover {
  background: #c0c0c0;
  color: #808080;
}

table.jCalendar td.dp-hover,
table.jCalendar td.selected,
table.jCalendar td.today,
table.jCalendar tr.activeWeekHover td,
table.jCalendar tr.selectedWeek td {
  background: #e0c040;
  color: #000000;
}

table.jCalendar td.other-month {
  background: #808080;
  color: #000000;
}

table.jCalendar th {
  background: #404040;
  color: #ffffff;
  font-weight: bold;
  padding: 3px 5px;
}

#dp-close {
  display: block;
  font-size: 12px;
  padding: 4px 0;
  text-align: center;
}

#dp-close:hover { text-decoration: underline; }

#dp-popup {
  position: absolute;
  z-index: 1000;
}

.dp-popup {
  background: #e0e0e0;
  font-family: Verdana, Geneva, Arial, Helvetica, Sans-Serif;
  font-size: 9px;
  line-height: 1.25em;
  padding: 2px;
  position: relative;
  width: 187px;
}

.dp-popup a {
  color: #000000;
  padding: 3px 2px 0;
  text-decoration: none;
}

.dp-popup a.disabled {
  color: #c0c0c0;
  cursor: default;
}

.dp-popup div.dp-nav-next {
  position: absolute;
  right: 4px;
  top: 2px;
  width: 100px;
}

.dp-popup div.dp-nav-next a { float: right; }

.dp-popup div.dp-nav-next a, .dp-popup div.dp-nav-prev a, .dp-popup td { cursor: pointer; }

.dp-popup div.dp-nav-next a.disabled, .dp-popup div.dp-nav-prev a.disabled, .dp-popup td.disabled { cursor: default; }

.dp-popup div.dp-nav-prev {
  left: 4px;
  position: absolute;
  top: 2px;
  width: 108px;
}

.dp-popup div.dp-nav-prev a { float: left; }

.dp-popup h2 {
  font-size: 12px;
  margin: 2px 0;
  padding: 0;
  text-align: center;
}
</style>
<?php } }

add_action('wp_head', 'membership_date_picker_css');


function membership_date_picker_js() { ?>
<script type="text/javascript" src="<?php echo MEMBERSHIP_MANAGER_URL; ?>libraries/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="<?php echo MEMBERSHIP_MANAGER_URL; ?>libraries/jquery-date-picker.js"></script>
<script type="text/javascript">
Date.dayNames = ['<?php _e('Sunday', 'membership-manager'); ?>', '<?php _e('Monday', 'membership-manager'); ?>', '<?php _e('Tuesday', 'membership-manager'); ?>', '<?php _e('Wednesday', 'membership-manager'); ?>', '<?php _e('Thursday', 'membership-manager'); ?>', '<?php _e('Friday', 'membership-manager'); ?>', '<?php _e('Saturday', 'membership-manager'); ?>'];
Date.abbrDayNames = ['<?php _e('Sun', 'membership-manager'); ?>', '<?php _e('Mon', 'membership-manager'); ?>', '<?php _e('Tue', 'membership-manager'); ?>', '<?php _e('Wed', 'membership-manager'); ?>', '<?php _e('Thu', 'membership-manager'); ?>', '<?php _e('Fri', 'membership-manager'); ?>', '<?php _e('Sat', 'membership-manager'); ?>'];
Date.monthNames = ['<?php _e('January', 'membership-manager'); ?>', '<?php _e('February', 'membership-manager'); ?>', '<?php _e('March', 'membership-manager'); ?>', '<?php _e('April', 'membership-manager'); ?>', '<?php _e('May', 'membership-manager'); ?>', '<?php _e('June', 'membership-manager'); ?>', '<?php _e('July', 'membership-manager'); ?>', '<?php _e('August', 'membership-manager'); ?>', '<?php _e('September', 'membership-manager'); ?>', '<?php _e('October', 'membership-manager'); ?>', '<?php _e('November', 'membership-manager'); ?>', '<?php _e('December', 'membership-manager'); ?>'];
Date.abbrMonthNames = ['<?php _e('Jan', 'membership-manager'); ?>', '<?php _e('Feb', 'membership-manager'); ?>', '<?php _e('Mar', 'membership-manager'); ?>', '<?php _e('Apr', 'membership-manager'); ?>', '<?php _e('May', 'membership-manager'); ?>', '<?php _e('Jun', 'membership-manager'); ?>', '<?php _e('Jul', 'membership-manager'); ?>', '<?php _e('Aug', 'membership-manager'); ?>', '<?php _e('Sep', 'membership-manager'); ?>', '<?php _e('Oct', 'membership-manager'); ?>', '<?php _e('Nov', 'membership-manager'); ?>', '<?php _e('Dec', 'membership-manager'); ?>'];
$.dpText = {
TEXT_PREV_YEAR : '<?php _e('Previous year', 'membership-manager'); ?>',
TEXT_PREV_MONTH : '<?php _e('Previous month', 'membership-manager'); ?>',
TEXT_NEXT_YEAR : '<?php _e('Next year', 'membership-manager'); ?>',
TEXT_NEXT_MONTH : '<?php _e('Next month', 'membership-manager'); ?>',
TEXT_CLOSE : '<?php _e('Close', 'membership-manager'); ?>',
TEXT_CHOOSE_DATE : '<?php _e('Choose a date', 'membership-manager'); ?>',
DATE_PICKER_ALT : '<?php _e('Date', 'membership-manager'); ?>',
DATE_PICKER_URL : '<?php echo MEMBERSHIP_MANAGER_URL; ?>images/date-picker.png',
HEADER_FORMAT : 'mmmm yyyy'
}; $(function(){ $('.date-pick').datePicker({startDate:'2011-01-01'}); });
</script>
<?php }


function membership_filter_data($filter, $data) {
if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', $filter)); }
if (is_array($filter)) { foreach ($filter as $function) { $data = membership_string_map($function, $data); } }
return $data; }


function membership_fix_url() {
$url = $_SERVER['REQUEST_URI'];
if (strstr($url, '&amp;')) { $url = str_replace('&amp;', '&', $url); $error = true; }
if ((strstr($url, '?')) && (!strstr($url, '/?')) && (!strstr($url, '.php?'))) { $url = str_replace('?', '/?', $url); $error = true; }
if (($error) && (!headers_sent())) { header('Location: '.$url); exit; } }


function membership_format_data($field, $data) {
$data = membership_quotes_entities_decode(do_shortcode($data));
if ((strstr($field, 'date')) && ($data == '0000-00-00 00:00:00')) { $data = ''; }
elseif (strstr($field, 'email_address')) { $data = membership_format_email_address($data); }
elseif (substr($field, -12) == 'instructions') { $data = membership_format_instructions($data); }
elseif (($field == 'url') || (strstr($field, '_url'))) { $data = membership_format_url($data); }
return $data; }


function membership_format_email_address($string) {
$string = strtolower(trim(strip_tags($string)));
$string = str_replace('à', '@', $string);
$string = str_replace(';', '.', $string);
$string = str_replace(' ', '-', $string);
$string = membership_strip_accents($string);
$string = preg_replace('/[^a-zA-Z0-9_@.-]/', '', $string);
return $string; }


function membership_format_email_address_js() { ?>
<script type="text/javascript">
function membership_format_email_address(string) {
string = string.toLowerCase();
string = string.replace(/[à]/gi, '@');
string = string.replace(/[;]/gi, '.');
string = string.replace(/[ ]/gi, '-');
string = membership_strip_accents(string);
string = string.replace(/[^a-zA-Z0-9_@.-]/gi, '');
return string; }
</script>
<?php }


function membership_format_instructions($string) {
$string = str_replace('<? ', '<?php ', trim($string));
if (substr($string, 0, 5) == '<?php') { $string = substr($string, 5); }
if (substr($string, -2) == '?>') { $string = substr($string, 0, -2); }
$string = trim($string);
return $string; }


function membership_format_login_name($string) {
return membership_format_email_address($string); }


function membership_format_login_name_js() { ?>
<script type="text/javascript">
function membership_format_login_name(string) {
string = string.toLowerCase();
string = string.replace(/[à]/gi, '@');
string = string.replace(/[;]/gi, '.');
string = string.replace(/[ ]/gi, '-');
string = membership_strip_accents(string);
string = string.replace(/[^a-zA-Z0-9_@.-]/gi, '');
return string; }
</script>
<?php }


function membership_format_medium_nice_name($string) {
$string = membership_strip_accents(trim(strip_tags($string)));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
return $string; }


function membership_format_name($string) {
$string = strtolower(trim(strip_tags($string)));
$string = str_replace(array(' ', '_'), '-', $string);
$strings = explode('-', $string);
$n = count($strings);
for ($i = 0; $i < $n; $i++) { $strings[$i] = ucfirst($strings[$i]); }
$string = implode('-', $strings);
return $string; }


function membership_format_name_js() { ?>
<script type="text/javascript">
function membership_format_name(string) {
string = string.toLowerCase();
string = string.replace(/[ _]/gi, '-');
var strings = string.split('-');
var n = strings.length;
var i = 0; while (i != n) { strings[i] = (strings[i]).substr(0, 1).toUpperCase()+(strings[i]).substr(1); i = i + 1; }
string = strings.join('-');
return string; }
</script>
<?php }


function membership_format_nice_name($string) {
$string = membership_strip_accents(strtolower(trim(strip_tags($string))));
$string = str_replace(' ', '-', $string);
$string = preg_replace('/[^a-zA-Z0-9_-]/', '', $string);
return $string; }


function membership_format_nice_name_js() { ?>
<script type="text/javascript">
function membership_format_nice_name(string) {
string = membership_strip_accents(string.toLowerCase());
string = string.replace(/[ ]/gi, '-');
string = string.replace(/[^a-zA-Z0-9_-]/gi, '');
return string; }
</script>
<?php }


function membership_format_url($string) {
if ($string != '') {
$string = trim(strip_tags($string));
$string = str_replace(' ', '-', $string);
if (!strstr($string, 'http')) {
if (substr($string, 0, 3) == 'www') { $string = 'http://'.$string; }
else { $string = 'http://'.$_SERVER['SERVER_NAME'].'/'.$string; } }
while (strstr($string, '//')) { $string = str_replace('//', '/', $string); }
$string = str_replace(':/', '://', $string); }
return $string; }


function membership_i18n($string) {
$strings = array(
__('active', 'membership-manager'),
__('inactive', 'membership-manager'),
__('no', 'membership-manager'),
__('unlimited', 'membership-manager'),
__('yes', 'membership-manager'));
return __(__($string), 'membership-manager'); }


function membership_instructions() {
add_shortcode('membership-redirection', 'membership_redirection');
$root_url = explode('/', str_replace('//', '||', HOME_URL));
$root_url = str_replace('||', '//', $root_url[0]);
$path = explode('?', strtolower($_SERVER['REQUEST_URI']));
$path = explode('#', $path[0]);
$path = str_replace(HOME_URL, '', $root_url.$path[0]);
while (substr($path, 0, 1) == '/') { $path = substr($path, 1); }
while (substr($path, -1) == '/') { $path = substr($path, 0, -1); }
$post = get_page_by_path($path);
do_shortcode(get_post_meta($post->ID, 'membership', true)); }


function membership_login_form() {
if (!membership_session('')) {
global $post, $wpdb;
add_action('wp_footer', 'membership_strip_accents_js');
add_action('wp_footer', 'membership_format_login_name_js');
add_action('wp_footer', 'membership_login_form_js');
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'm_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$_POST['login'] = membership_format_login_name(trim(mysql_real_escape_string(strip_tags($_POST['login']))));
$_POST['password'] = trim(mysql_real_escape_string(strip_tags($_POST['password'])));
$result = $wpdb->get_row("SELECT login, status FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_POST['login']."' AND password = '".hash('sha256', $_POST['password'])."'", OBJECT);
if (!$result) { $error .= __('Invalid login or password', 'membership-manager'); }
elseif ($result->status == 'inactive') { $error .= __('Your account is inactive.', 'membership-manager'); }
if ($error == '') {
session_start();
$_SESSION['m_login'] = $_POST['login'];
if (isset($_POST['remember'])) {
$T = time() + 90*86400;
if (!headers_sent()) { setcookie('m_login', $_POST['login'].hash('sha256', $_POST['login'].AUTH_KEY), $T, '/'); }
else {
$expiration_date = date('D', $T).', '.date('d', $T).' '.date('M', $T).' '.date('Y', $T).' '.date('H:i:s', $T).' UTC';
$content .= '<script type="text/javascript">document.cookie="m_login='.$_POST['login'].hash('sha256', $_POST['login'].AUTH_KEY).'; expires='.$expiration_date.'";</script>'; } }
if (!headers_sent()) { membership_instructions(); }
else { $content .= '<script type="text/javascript">window.location = \''.do_shortcode(get_post_meta($post->ID, 'membership', true)).'\';</script>'; } } }

if ($error != '') { $content .= '<p class="error">'.$error.'</p>'; }

$content .= '
<form method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" onsubmit="return validate_membership_login_form(this);">
<table style="width: 100%;">
<tr class="login" style="vertical-align: top;"><td style="width: 40%;"><strong><label for="m_login">'.__('Login name', 'membership-manager').'</label></strong></td>
<td style="width: 60%;"><input type="text" name="m_login" id="m_login" size="20" value="'.$_POST['login'].'" onchange=\'document.getElementById("m_login").value = membership_format_login_name(document.getElementById("m_login").value);\' /><br />
<span class="error" id="m_login_error"></span></td></tr>
<tr class="password" style="vertical-align: top;"><td style="width: 40%;"><strong><label for="m_password">'.__('Password', 'membership-manager').'</label></strong></td>
<td style="width: 60%;"><input type="password" name="m_password" id="m_password" size="20" /><br />
<span class="error" id="m_password_error"></span></td></tr>
</table>
<p style="margin: 0.75em; text-align: center;"><label><input type="checkbox" name="m_remember" id="m_remember" value="yes"'.(isset($_POST['remember']) ? ' checked="checked"' : '').' /> '.__('Remember me', 'membership-manager').'</label></p>
<div style="text-align: center;"><input type="submit" name="m_submit" value="'.__('Login', 'membership-manager').'" /></div>
</form>';

return $content; } }

add_shortcode('membership-login-form', 'membership_login_form');


function membership_login_form_js() { ?>
<script type="text/javascript">
function validate_membership_login_form(form) {
var error = false;
form.m_login.value = membership_format_login_name(form.m_login.value);
if (form.m_login.value == '') {
document.getElementById('m_login_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
if (form.m_password.value == '') {
document.getElementById('m_password_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
return !error; }
</script>
<?php }


function membership_logout() {
session_start();
unset($_SESSION['m_login']);
setcookie('m_login', '', time() - 86400, '/'); }


function membership_password_reset_form() {
if (!membership_session('')) {
global $wpdb;
add_action('wp_footer', 'membership_strip_accents_js');
add_action('wp_footer', 'membership_format_email_address_js');
add_action('wp_footer', 'membership_password_reset_form_js');
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'm_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$_POST['email_address'] = membership_format_email_address(trim(mysql_real_escape_string(strip_tags($_POST['email_address']))));
$result = $wpdb->get_row("SELECT email_address FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if (!$result) { $error .= __('This email address does not match a member account.', 'membership-manager'); $content .= '<p class="error">'.$error.'</p>'; }
else {
$_POST['password'] = substr(md5(mt_rand()), 0, 8);
$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET password = '".hash('sha256', $_POST['password'])."' WHERE email_address = '".$_POST['email_address']."'");
$_GET['member_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_POST['email_address']."'", OBJECT);
$_GET['member_data']->password = $_POST['password'];
$sender = membership_data('password_reset_email_sender');
$receiver = membership_data('password_reset_email_receiver');
$subject = membership_data('password_reset_email_subject');
$body = membership_data('password_reset_email_body');
$headers = 'From: '.$sender;
wp_mail($receiver, $subject, $body, $headers);
$content .= '<p class="valid">'.__('Your password has been reset successfully.', 'membership-manager').'</p>'; } }

$content .= '
<form style="text-align: center;" method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" onsubmit="return validate_membership_password_reset_form(this);">
<p class="email-address"><label><strong>'.__('Your email address:', 'membership-manager').'</strong><br />
<input type="text" name="m_email_address" id="m_email_address" size="40" value="'.$_POST['email_address'].'" onchange=\'document.getElementById("m_email_address").value = membership_format_email_address(document.getElementById("m_email_address").value);\' /><br /></label>
<span class="error" id="m_email_address_error"></span></p>
<div><input type="submit" name="m_submit" value="'.__('Reset', 'membership-manager').'" /></div>
</form>';

return $content; } }

add_shortcode('membership-password-reset-form', 'membership_password_reset_form');


function membership_password_reset_form_js() { ?>
<script type="text/javascript">
function validate_membership_password_reset_form(form) {
var error = false;
form.m_email_address.value = membership_format_email_address(form.m_email_address.value);
if ((form.m_email_address.value.indexOf('@') == -1) || (form.m_email_address.value.indexOf('.') == -1)) {
document.getElementById('m_email_address_error').innerHTML = '<?php _e('This email address appears to be invalid.', 'membership-manager'); ?>';
error = true; }
if (form.m_email_address.value == '') {
document.getElementById('m_email_address_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
return !error; }
</script>
<?php }


function membership_profile_form() {
if (membership_session('')) {
global $wpdb;
if ((isset($_SESSION['m_login'])) && ($_GET['member_data']->login != $_SESSION['m_login'])) {
$_GET['member_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_SESSION['m_login']."'", OBJECT); }
$member_data = $_GET['member_data'];
add_action('wp_footer', 'membership_strip_accents_js');
add_action('wp_footer', 'membership_format_login_name_js');
add_action('wp_footer', 'membership_format_name_js');
add_action('wp_footer', 'membership_format_email_address_js');
add_action('wp_footer', 'membership_profile_form_js');
$minimum_login_length = membership_data('minimum_login_length');
$maximum_login_length = membership_data('maximum_login_length');
$minimum_password_length = membership_data('minimum_password_length');
$maximum_password_length = membership_data('maximum_password_length');
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'm_') { $_POST[substr($key, 2)] = $value; } }
if (isset($_POST['submit'])) {
$_POST = array_map('strip_tags', $_POST);
$_POST = array_map('membership_quotes_entities', $_POST);
$_POST = array_map('mysql_real_escape_string', $_POST);
$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('trim', $_POST);
$_POST['login'] = membership_format_login_name($_POST['login']);
$_POST['first_name'] = membership_format_name($_POST['first_name']);
$_POST['last_name'] = membership_format_name($_POST['last_name']);
$_POST['email_address'] = membership_format_email_address($_POST['email_address']);
$_POST['website_url'] = membership_format_url($_POST['website_url']);
if (is_numeric($_POST['login'])) { $error .= __('Your login name must be a non-numeric string.', 'membership-manager'); }
if (strlen($_POST['login']) < $minimum_login_length) {
$error .= ' '.sprintf(__('Your login name must contain at least %d characters.', 'membership-manager'), $minimum_login_length); }
if (strlen($_POST['login']) > $maximum_login_length) {
$error .= ' '.sprintf(__('Your login name must contain at most %d characters.', 'membership-manager'), $maximum_login_length); }
else { $result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_POST['login']."'", OBJECT);
if (($result) && ($result->login != $_SESSION['m_login'])) { $error .= ' '.__('This login name is not available.', 'membership-manager'); }
elseif ($_POST['login'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET login = '".$_POST['login']."' WHERE login = '".$_SESSION['m_login']."'"); } }
if ($_POST['password'] != '') {
if (strlen($_POST['password']) < $minimum_password_length) {
$error .= ' '.sprintf(__('Your password must contain at least %d characters.', 'membership-manager'), $minimum_password_length); }
if (strlen($_POST['password']) > $maximum_password_length) {
$error .= ' '.sprintf(__('Your password must contain at most %d characters.', 'membership-manager'), $maximum_password_length); }
if ($error == '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET password = '".hash('sha256', $_POST['password'])."' WHERE login = '".$_SESSION['m_login']."'"); } }
if ($_POST['first_name'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET first_name = '".$_POST['first_name']."' WHERE login = '".$_SESSION['m_login']."'"); }
if ($_POST['last_name'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET last_name = '".$_POST['last_name']."' WHERE login = '".$_SESSION['m_login']."'"); }
$result = $wpdb->get_row("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if (($result) && ($result->login != $_SESSION['m_login'])) { $error .= ' '.__('This email address is not available.', 'membership-manager'); }
elseif ($_POST['email_address'] != '') { $results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET email_address = '".$_POST['email_address']."' WHERE login = '".$_SESSION['m_login']."'"); }
if (($_POST['login'] == '') || ($_POST['first_name'] == '') || ($_POST['last_name'] == '') || ($_POST['email_address'] == '')) {
$error .= ' '.__('Please fill out the required fields.', 'membership-manager'); }

$results = $wpdb->query("UPDATE ".$wpdb->prefix."membership_manager_members SET
	website_name = '".$_POST['website_name']."',
	website_url = '".$_POST['website_url']."',
	address = '".$_POST['address']."',
	postcode = '".$_POST['postcode']."',
	town = '".$_POST['town']."',
	country = '".$_POST['country']."',
	phone_number = '".$_POST['phone_number']."' WHERE login = '".$_SESSION['m_login']."'");

$_GET['member_data'] = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_SESSION['m_login']."'", OBJECT);
$member_data = $_GET['member_data'];

if ($error != '') { $content .= '<p class="error">'.$error.'</p>'; }
else { $content .= '<p class="valid">'.__('Your profile has been changed successfully.', 'membership-manager').'</p>'; } }

$content .= '
<form method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" onsubmit="return validate_membership_profile_form(this);">
<table style="width: 100%;">
<tr class="login" style="vertical-align: top;"><td><strong><label for="m_login">'.__('Login name', 'membership-manager').'</label></strong>*</td>
<td><input type="text" name="m_login" id="m_login" size="30" value="'.$member_data->login.'" onchange=\'document.getElementById("m_login").value = membership_format_login_name(document.getElementById("m_login").value);\' /><br />
<span class="description">'.__('Letters, numbers, hyphens, underscores, points and <em>@</em> only', 'membership-manager').'</span></td></tr>
<tr class="password" style="vertical-align: top;"><td><strong><label for="m_password">'.__('Password', 'membership-manager').'</label></strong></td>
<td><input type="password" name="m_password" id="m_password" size="30" value="" /><br />
<span class="description">'.__('(if you want to change it)', 'membership-manager').'</span><br />
<span class="error" id="m_password_error"></span></td></tr>
<tr class="first-name" style="vertical-align: top;"><td><strong><label for="m_first_name">'.__('First name', 'membership-manager').'</label></strong>*</td>
<td><input type="text" name="m_first_name" id="m_first_name" size="30" value="'.$member_data->first_name.'" /><br />
<span class="error" id="m_first_name_error"></span></td></tr>
<tr class="last-name" style="vertical-align: top;"><td><strong><label for="m_last_name">'.__('Last name', 'membership-manager').'</label></strong>*</td>
<td><input type="text" name="m_last_name" id="m_last_name" size="30" value="'.$member_data->last_name.'" /><br />
<span class="error" id="m_last_name_error"></span></td></tr>
<tr class="email-address" style="vertical-align: top;"><td><strong><label for="m_email_address">'.__('Email address', 'membership-manager').'</label></strong>*</td>
<td><input type="text" name="m_email_address" id="m_email_address" size="30" value="'.$member_data->email_address.'" onchange=\'document.getElementById("m_email_address").value = membership_format_email_address(document.getElementById("m_email_address").value);\' /><br />
<span class="error" id="m_email_address_error"></span></td></tr>
<tr class="website-name" style="vertical-align: top;"><td><strong><label for="m_website_name">'.__('Website name', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_website_name" id="m_website_name" size="30" value="'.$member_data->website_name.'" /></td></tr>
<tr class="website-url" style="vertical-align: top;"><td><strong><label for="m_website_url">'.__('Website URL', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_website_url" id="m_website_url" size="30" value="'.$member_data->website_url.'" /></td></tr>
<tr class="address" style="vertical-align: top;"><td><strong><label for="m_address">'.__('Address', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_address" id="m_address" size="30" value="'.$member_data->address.'" /></td></tr>
<tr class="postcode" style="vertical-align: top;"><td><strong><label for="m_postcode">'.__('Postcode', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_postcode" id="m_postcode" size="30" value="'.$member_data->postcode.'" /></td></tr>
<tr class="town" style="vertical-align: top;"><td><strong><label for="m_town">'.__('Town', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_town" id="m_town" size="30" value="'.$member_data->town.'" /></td></tr>
<tr class="country" style="vertical-align: top;"><td><strong><label for="m_country">'.__('Country', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_country" id="m_country" size="30" value="'.$member_data->country.'" /></td></tr>
<tr class="phone-number" style="vertical-align: top;"><td><strong><label for="m_phone_number">'.__('Phone number', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_phone_number" id="m_phone_number" size="30" value="'.$member_data->phone_number.'" /></td></tr>
</table>
<p id="m_form_error"></p>
<div style="text-align: center;"><input type="submit" name="m_submit" value="'.__('Modify', 'membership-manager').'" /></div>
</form>';

return $content; } }

add_shortcode('membership-profile-form', 'membership_profile_form');


function membership_profile_form_js() { ?>
<script type="text/javascript">
function validate_membership_profile_form(form) {
var error = false;
form.m_login.value = membership_format_login_name(form.m_login.value);
form.m_first_name.value = membership_format_name(form.m_first_name.value);
form.m_last_name.value = membership_format_name(form.m_last_name.value);
form.m_email_address.value = membership_format_email_address(form.m_email_address.value);
if (<?php $minimum_login_length = membership_data('minimum_login_length'); echo $minimum_login_length; ?> > form.m_login.value.length) {
document.getElementById('m_login_error').innerHTML = '<?php sprintf(__('Your login name must contain at least %d characters.', 'membership-manager'), $minimum_login_length); ?>';
error = true; }
if (form.m_login.value.length > <?php $maximum_login_length = membership_data('maximum_login_length'); echo $maximum_login_length; ?>) {
document.getElementById('m_login_error').innerHTML = '<?php sprintf(__('Your login name must contain at most %d characters.', 'membership-manager'), $maximum_login_length); ?>';
error = true; }
if (form.m_login.value == '') {
document.getElementById('m_login_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
if (form.m_first_name.value == '') {
document.getElementById('m_first_name_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
if (form.m_last_name.value == '') {
document.getElementById('m_last_name_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
if ((form.m_email_address.value.indexOf('@') == -1) || (form.m_email_address.value.indexOf('.') == -1)) {
document.getElementById('m_email_address_error').innerHTML = '<?php _e('This email address appears to be invalid.', 'membership-manager'); ?>';
error = true; }
if (form.m_email_address.value == '') {
document.getElementById('m_email_address_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
if (error) { document.getElementById('m_form_error').innerHTML = '<?php _e('An error has occurred. Please check the fields and resubmit the form.', 'membership-manager'); ?>'; }
return !error; }
</script>
<?php }


function membership_quotes_entities($string) {
return str_replace(array("'", '"'), array("&apos;", '&quot;'), $string); }


function membership_quotes_entities_decode($string) {
return str_replace(array("&apos;", '&quot;'), array("'", '"'), $string); }


function membership_redirection($atts) {
extract(shortcode_atts(array('action' => '', 'condition' => '', 'id' => '', 'url' => ''), $atts));
$action = strtolower($action);
$condition = strtolower($condition);
if ($url == '') { $url = '../'; }
switch ($condition) {
case 'session': if (membership_session($id)) {
if ($action == 'logout') { membership_logout(); }
if (!headers_sent()) { header('Location: '.$url); exit; } } break;
case '!session': if ((!membership_session($id)) && (!headers_sent())) { header('Location: '.$url); exit; } break;
default: if (($action == 'logout') && (membership_session($id))) { membership_logout(); }
if (!headers_sent()) { header('Location: '.$url); exit; } }
return $url; }

add_shortcode('membership-redirection', 'membership_redirection');


function membership_registration_form($atts) {
if (!membership_session('')) {
global $wpdb;
extract(shortcode_atts(array('id' => ''), $atts));
add_action('wp_footer', 'membership_strip_accents_js');
add_action('wp_footer', 'membership_format_login_name_js');
add_action('wp_footer', 'membership_format_name_js');
add_action('wp_footer', 'membership_format_email_address_js');
add_action('wp_footer', 'membership_registration_form_js');
$minimum_login_length = membership_data('minimum_login_length');
$maximum_login_length = membership_data('maximum_login_length');
$minimum_password_length = membership_data('minimum_password_length');
$maximum_password_length = membership_data('maximum_password_length');
foreach ($_POST as $key => $value) { if (substr($key, 0, 2) == 'm_') { $_POST[substr($key, 2)] = $value; } }
if (!isset($_POST['referring_url'])) { $_POST['referring_url'] = htmlspecialchars($_SERVER['HTTP_REFERER']); }
if (isset($_POST['submit'])) {
$_POST = array_map('strip_tags', $_POST);
$_POST = array_map('membership_quotes_entities', $_POST);
$_POST = array_map('mysql_real_escape_string', $_POST);
$_POST = array_map('stripslashes', $_POST);
$_POST = array_map('trim', $_POST);
$_POST['login'] = membership_format_login_name($_POST['login']);
$_POST['first_name'] = membership_format_name($_POST['first_name']);
$_POST['last_name'] = membership_format_name($_POST['last_name']);
$_POST['email_address'] = membership_format_email_address($_POST['email_address']);
$_POST['website_url'] = membership_format_url($_POST['website_url']);
$_POST['referring_url'] = html_entity_decode($_POST['referring_url']);
if (is_numeric($_POST['login'])) { $error .= __('Your login name must be a non-numeric string.', 'membership-manager'); }
if (strlen($_POST['login']) < $minimum_login_length) {
$error .= ' '.sprintf(__('Your login name must contain at least %d characters.', 'membership-manager'), $minimum_login_length); }
if (strlen($_POST['login']) > $maximum_login_length) {
$error .= ' '.sprintf(__('Your login name must contain at most %d characters.', 'membership-manager'), $maximum_login_length); }
$result = $wpdb->get_results("SELECT login FROM ".$wpdb->prefix."membership_manager_members WHERE login = '".$_POST['login']."'", OBJECT);
if ($result) { $error .= ' '.__('This login name is not available.', 'membership-manager'); }
if (strlen($_POST['password']) < $minimum_password_length) {
$error .= ' '.sprintf(__('Your password must contain at least %d characters.', 'membership-manager'), $minimum_password_length); }
if (strlen($_POST['password']) > $maximum_password_length) {
$error .= ' '.sprintf(__('Your password must contain at most %d characters.', 'membership-manager'), $maximum_password_length); }
$result = $wpdb->get_results("SELECT email_address FROM ".$wpdb->prefix."membership_manager_members WHERE email_address = '".$_POST['email_address']."'", OBJECT);
if ($result) { $error .= ' '.__('This email address is not available.', 'membership-manager'); }
if (($_POST['login'] == '') || ($_POST['first_name'] == '') || ($_POST['last_name'] == '') || ($_POST['email_address'] == '')) {
$error .= ' '.__('Please fill out the required fields.', 'membership-manager'); }

if ($error == '') {
$_POST['id'] = '';
$members_areas = array_unique(preg_split('#[^0-9]#', $id));
sort($members_areas, SORT_NUMERIC);
foreach ($members_areas as $member_area) { $members_areas_list .= $member_area.', '; }
$_POST['members_areas'] = substr($members_areas_list, 0, -2);
if (count($members_areas) == 1) { $_GET['member_area_id'] = (int) $members_areas[0]; } else { unset($_GET['member_area_id']); }
$_POST['category_id'] = member_area_data('members_initial_category_id');
$_POST['status'] = member_area_data('members_initial_status');
$_POST['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
$_POST['ip_address'] = $_SERVER['REMOTE_ADDR'];
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$_POST['date'] = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$_POST['date_utc'] = date('Y-m-d H:i:s');
add_member($_POST);

if ($_GET['autoresponder_subscription'] == '') {
if (!headers_sent()) { header('Location: '.member_area_data('registration_confirmation_url')); exit; }
else { $content .= '<script type="text/javascript">window.location = \''.member_area_data('registration_confirmation_url').'\';</script>'; } }
else { $content .= '<div><img alt="" src="'.$_GET['autoresponder_subscription'].'" /></div><script type="text/javascript">setTimeout("window.location=\''.member_area_data('registration_confirmation_url').'\'", 3000);</script>'; } } }

if ($error != '') { $content .= '<p class="error">'.$error.'</p>'; }

$content .= '
<form method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" onsubmit="return validate_membership_registration_form(this);">
<table style="width: 100%;">
<tr class="login" style="vertical-align: top;"><td><strong><label for="m_login">'.__('Login name', 'membership-manager').'</label></strong>*</td>
<td><input type="text" name="m_login" id="m_login" size="30" value="'.$_POST['login'].'" onchange=\'document.getElementById("m_login").value = membership_format_login_name(document.getElementById("m_login").value); $.get("'.MEMBERSHIP_MANAGER_URL.'?action=check-login",{ login: $("#m_login").val() } ,function(data){ $("#m_login_available").html(data); });\' /> 
<span id="m_login_available">'.(strstr($error, __('login name', 'membership-manager')) ? '<span class="error">'.__('Unavailable', 'membership-manager').'</span>' : '').'</span><br />
<span class="description">'.__('Letters, numbers, hyphens, underscores, points and <em>@</em> only', 'membership-manager').'<br />
<span class="error" id="m_login_error"></span></td></tr>
<tr class="password" style="vertical-align: top;"><td><strong><label for="m_password">'.__('Password', 'membership-manager').'</label></strong>*</td>
<td><input type="password" name="m_password" id="m_password" size="30" value="'.$_POST['password'].'" /> <span class="description">'.sprintf(__('at least %d characters', 'membership-manager'), $minimum_password_length).'</span><br />
<span class="error" id="m_password_error"></span></td></tr>
<tr class="first-name" style="vertical-align: top;"><td><strong><label for="m_first_name">'.__('First name', 'membership-manager').'</label></strong>*</td>
<td><input type="text" name="m_first_name" id="m_first_name" size="30" value="'.$_POST['first_name'].'" /><br />
<span class="error" id="m_first_name_error"></span></td></tr>
<tr class="last-name" style="vertical-align: top;"><td><strong><label for="m_last_name">'.__('Last name', 'membership-manager').'</label></strong>*</td>
<td><input type="text" name="m_last_name" id="m_last_name" size="30" value="'.$_POST['last_name'].'" /><br />
<span class="error" id="m_last_name_error"></span></td></tr>
<tr class="email-address" style="vertical-align: top;"><td><strong><label for="m_email_address">'.__('Email address', 'membership-manager').'</label></strong>*</td>
<td><input type="text" name="m_email_address" id="m_email_address" size="30" value="'.$_POST['email_address'].'" onchange=\'document.getElementById("m_email_address").value = membership_format_email_address(document.getElementById("m_email_address").value);\' /><br />
<span class="error" id="m_email_address_error"></span></td></tr>
<tr class="website-name" style="vertical-align: top;"><td><strong><label for="m_website_name">'.__('Website name', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_website_name" id="m_website_name" size="30" value="'.$_POST['website_name'].'" /></td></tr>
<tr class="website-url" style="vertical-align: top;"><td><strong><label for="m_website_url">'.__('Website URL', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_website_url" id="m_website_url" size="30" value="'.$_POST['website_url'].'" /></td></tr>
<tr class="address" style="vertical-align: top;"><td><strong><label for="m_address">'.__('Address', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_address" id="m_address" size="30" value="'.$_POST['address'].'" /></td></tr>
<tr class="postcode" style="vertical-align: top;"><td><strong><label for="m_postcode">'.__('Postcode', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_postcode" id="m_postcode" size="30" value="'.$_POST['postcode'].'" /></td></tr>
<tr class="town" style="vertical-align: top;"><td><strong><label for="m_town">'.__('Town', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_town" id="m_town" size="30" value="'.$_POST['town'].'" /></td></tr>
<tr class="country" style="vertical-align: top;"><td><strong><label for="m_country">'.__('Country', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_country" id="m_country" size="30" value="'.$_POST['country'].'" /></td></tr>
<tr class="phone-number" style="vertical-align: top;"><td><strong><label for="m_phone_number">'.__('Phone number', 'membership-manager').'</label></strong></td>
<td><input type="text" name="m_phone_number" id="m_phone_number" size="30" value="'.$_POST['phone_number'].'" /></td></tr>
</table>
<p id="m_form_error"></p>
<div style="text-align: center;"><input type="hidden" name="m_referring_url" value="'.$_POST['referring_url'].'" />
<input type="submit" name="m_submit" value="'.__('Register', 'membership-manager').'" /></div>
</form>';

return $content; } }

add_shortcode('membership-registration-form', 'membership_registration_form');


function membership_registration_form_js() { ?>
<script type="text/javascript">
function validate_membership_registration_form(form) {
var error = false;
form.m_login.value = membership_format_login_name(form.m_login.value);
form.m_first_name.value = membership_format_name(form.m_first_name.value);
form.m_last_name.value = membership_format_name(form.m_last_name.value);
form.m_email_address.value = membership_format_email_address(form.m_email_address.value);
if (<?php $minimum_login_length = membership_data('minimum_login_length'); echo $minimum_login_length; ?> > form.m_login.value.length) {
document.getElementById('m_login_error').innerHTML = '<?php sprintf(__('Your login name must contain at least %d characters.', 'membership-manager'), $minimum_login_length); ?>';
error = true; }
if (form.m_login.value.length > <?php $maximum_login_length = membership_data('maximum_login_length'); echo $maximum_login_length; ?>) {
document.getElementById('m_login_error').innerHTML = '<?php sprintf(__('Your login name must contain at most %d characters.', 'membership-manager'), $maximum_login_length); ?>';
error = true; }
if (form.m_login.value == '') {
document.getElementById('m_login_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
if (<?php $minimum_password_length = membership_data('minimum_password_length'); echo $minimum_password_length; ?> > form.m_password.value.length) {
document.getElementById('m_password_error').innerHTML = '<?php printf(__('Your password must contain at least %d characters.', 'membership-manager'), $minimum_password_length); ?>';
error = true; }
if (form.m_password.value.length > <?php $maximum_password_length = membership_data('maximum_password_length'); echo $maximum_password_length; ?>) {
document.getElementById('m_password_error').innerHTML = '<?php printf(__('Your password must contain at most %d characters.', 'membership-manager'), $maximum_password_length); ?>';
error = true; }
if (form.m_password.value == '') {
document.getElementById('m_password_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
if (form.m_first_name.value == '') {
document.getElementById('m_first_name_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
if (form.m_last_name.value == '') {
document.getElementById('m_last_name_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
if ((form.m_email_address.value.indexOf('@') == -1) || (form.m_email_address.value.indexOf('.') == -1)) {
document.getElementById('m_email_address_error').innerHTML = '<?php _e('This email address appears to be invalid.', 'membership-manager'); ?>';
error = true; }
if (form.m_email_address.value == '') {
document.getElementById('m_email_address_error').innerHTML = '<?php _e('This field is required.', 'membership-manager'); ?>';
error = true; }
if (error) { document.getElementById('m_form_error').innerHTML = '<?php _e('An error has occurred. Please check the fields and resubmit the form.', 'membership-manager'); ?>'; }
return !error; }
</script>
<script type="text/javascript" src="<?php echo MEMBERSHIP_MANAGER_URL; ?>libraries/jquery-1.5.1.min.js"></script>
<?php }


function membership_session($members_areas) {
if (!is_array($members_areas)) { $members_areas = preg_split('#[^0-9]#', $members_areas); }
$members_areas = array_unique($members_areas);
$n = count($members_areas);
session_start();
if ((isset($_COOKIE['m_login'])) && (!isset($_SESSION['m_login']))) {
$login = substr($_COOKIE['m_login'], 0, -64);
if (substr($_COOKIE['m_login'], -64) == hash('sha256', $login.AUTH_KEY)) { $_SESSION['m_login'] = $login; } }	
if (isset($_SESSION['m_login'])) {
if ($n == 0) { return true; }
else {
$list = members_areas_list(member_data('members_areas'));
if (in_array(0, $list)) { $session = true; }
else {
for ($i = 0; $i < $n; $i++) { $members_areas[$i] = (int) $members_areas[$i]; }
$session = false; $i = 0; while ((!$session) && ($i < $n)) {
if (in_array($members_areas[$i], $list)) { $session = true; }
$i = $i + 1; } }
return $session; } }
else { return false; } }


function membership_string_map($function, $string) {
if (!function_exists($function)) { $function = 'membership_'.$function; }
if (function_exists($function)) { $array = array_map($function, array($string)); $string = $array[0]; }
return $string; }


function membership_strip_accents($string) {
return str_replace(
explode(' ', 'á à â ä ã å ç é è ê ë í ì î ï ñ ó ò ô ö õ ø ú ù û ü ý ÿ Á À Â Ä Ã Å Ç É È Ê Ë Í Ì Î Ï Ñ Ó Ò Ô Ö Õ Ø Ú Ù Û Ü Ý Ÿ'),
explode(' ', 'a a a a a a c e e e e i i i i n o o o o o o u u u u y y A A A A A A C E E E E I I I I N O O O O O O U U U U Y Y'),
$string); }


function membership_strip_accents_js() { ?>
<script type="text/javascript">
function membership_strip_accents(string) {
string = string.replace(/[áàâäãå]/gi, 'a');
string = string.replace(/[ç]/gi, 'c');
string = string.replace(/[éèêë]/gi, 'e');
string = string.replace(/[íìîï]/gi, 'i');
string = string.replace(/[ñ]/gi, 'n');
string = string.replace(/[óòôöõø]/gi, 'o');
string = string.replace(/[úùûü]/gi, 'u');
string = string.replace(/[ýÿ]/gi, 'y');
string = string.replace(/[ÁÀÂÄÃÅ]/gi, 'A');
string = string.replace(/[Ç]/gi, 'C');
string = string.replace(/[ÉÈÊË]/gi, 'E');
string = string.replace(/[ÍÌÎÏ]/gi, 'I');
string = string.replace(/[Ñ]/gi, 'N');
string = string.replace(/[ÓÒÔÖÕØ]/gi, 'O');
string = string.replace(/[ÚÙÛÜ]/gi, 'U');
string = string.replace(/[ÝŸ]/gi, 'Y');
return string; }
</script>
<?php }


add_filter('get_the_excerpt', 'do_shortcode');
add_filter('get_the_title', 'do_shortcode');
add_filter('single_post_title', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');
add_filter('the_excerpt_rss', 'do_shortcode');
add_filter('the_title', 'do_shortcode');
add_filter('the_title_attribute', 'do_shortcode');
add_filter('the_title_rss', 'do_shortcode');
add_filter('widget_text', 'do_shortcode');