<?php global $wpdb;

foreach (array(
'category_id',
'ip_address',
'status') as $field) {
if (isset($_GET[$field])) {
$_GET['selection_criteria'] .= '&amp;'.$field.'='.$_GET[$field];
$selection_criteria .= " AND ".$field." = '".$_GET[$field]."'"; } }


function no_items($table) {
switch ($table) {
case 'members': $no_items = __('No members', 'membership-manager'); break;
case 'members_areas': $no_items = __('No members areas', 'membership-manager'); break;
case 'members_areas_categories': case 'members_categories': $no_items = __('No categories', 'membership-manager'); }
return $no_items; }


function row_actions($table, $item) {
global $wpdb;
switch ($table) {
case 'members': $row_actions = 
'<span class="edit"><a href="admin.php?page=membership-manager-member&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=membership-manager-member&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'; break;
case 'members_areas': $row_actions = 
'<span class="edit"><a href="admin.php?page=membership-manager-member-area&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=membership-manager-member-area&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'; break;
case 'members_areas_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."membership_manager_members_areas WHERE category_id = ".$item->id, OBJECT);
$members_areas_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."membership_manager_members_areas_categories WHERE category_id = ".$item->id, OBJECT);
$categories_number = (int) $row->total;
$row_actions = 
'<span class="edit"><a href="admin.php?page=membership-manager-member-area-category&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=membership-manager-member-area-category&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'
.($members_areas_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=membership-manager-members-areas&amp;category_id='.$item->id.'">'.__('Members areas', 'membership-manager').'</a></span>')
.($categories_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=membership-manager-members-areas-categories&amp;category_id='.$item->id.'">'.__('Subcategories', 'membership-manager').'</a></span>'); break;
case 'members_categories':
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."membership_manager_members WHERE category_id = ".$item->id, OBJECT);
$members_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."membership_manager_members_categories WHERE category_id = ".$item->id, OBJECT);
$categories_number = (int) $row->total;
$row_actions = 
'<span class="edit"><a href="admin.php?page=membership-manager-member-category&amp;id='.$item->id.'">'.__('Edit').'</a></span>
 | <span class="delete"><a href="admin.php?page=membership-manager-member-category&amp;id='.$item->id.'&amp;action=delete">'.__('Delete').'</a></span>'
.($members_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=membership-manager-members&amp;category_id='.$item->id.'">'.__('Members', 'membership-manager').'</a></span>')
.($categories_number == 0 ? '' : ' | <span class="view"><a href="admin.php?page=membership-manager-members-categories&amp;category_id='.$item->id.'">'.__('Subcategories', 'membership-manager').'</a></span>'); break; }
return '<div class="row-actions" style="margin-top: 2em; position: absolute;">'.$row_actions.'</div>'; }


function table_name($table) {
global $wpdb;
return $wpdb->prefix.'membership_manager_'.$table; }


function table_td($table, $column, $item) {
switch ($table) {
case 'members': $_GET['member_data'] = $item; $data = member_data($column); break;
case 'members_areas': $_GET['member_area_id'] = $item->id; $_GET['member_area_data'] = $item; $data = member_area_data($column); break;
case 'members_areas_categories': $_GET['member_area_category_id'] = $item->id; $_GET['member_area_category_data'] = $item; $data = member_area_category_data($column); break;
case 'members_categories': $_GET['member_category_id'] = $item->id; $_GET['member_category_data'] = $item; $data = member_category_data($column); break;
default: $data = membership_format_data($column, $item->$column); }
$data = htmlspecialchars($data);
switch ($column) {
case 'description': case 'instructions': case 'registration_confirmation_email_body': case 'registration_custom_instructions': case 'registration_notification_email_body': if (strlen($data) <= 80) { $table_td = $data; }
else { $table_td = substr($data, 0, 80); if (stristr($table_td, ' ')) { while (substr($table_td, -1) != ' ') { $table_td = substr($table_td, 0, -1); } } $table_td .= '[...]'; } break;
case 'category_id': case 'ip_address': $table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].'&amp;'.$column.'='.$data.'">'.$data.'</a>'); break;
case 'email_address': $table_td = '<a href="mailto:'.$data.'">'.$data.'</a>'; break;
case 'login': $table_td = '<a href="admin.php?page=membership-manager-member&amp;id='.$item->id.'">'.$data.'</a>'; break;
case 'member_subscribed_to_autoresponder': case 'registration_confirmation_email_sent': case 'registration_custom_instructions_executed': case 'registration_notification_email_sent':
if ($data == 'yes') { $table_td = '<span style="color: #008000;">'.__('Yes', 'membership-manager').'</span>'; }
elseif ($data == 'no')  { $table_td = '<span style="color: #c00000;">'.__('No', 'membership-manager').'</span>'; }
else { $table_td = $data; } break;
case 'members_areas':
$members_areas = array_unique(preg_split('#[^0-9]#', $data));
foreach ($members_areas as $member_area) {
if ($member_area > 0) { $member_area = '<a href="admin.php?page=membership-manager-member-area&amp;'.$id.'='.$member_area.'">'.$member_area.'</a>'; }
$members_areas_list .= $member_area.', '; }
$table_td = substr($members_areas_list, 0, -2); break;
case 'members_initial_status': if ($data == 'active') { $table_td = '<span style="color: #008000;">'.__('Active', 'membership-manager').'</span>'; }
elseif ($data == 'inactive') { $table_td = '<span style="color: #e08000;">'.__('Inactive', 'membership-manager').'</span>'; }
else { $table_td = $data; } break;
case 'referring_url': case 'registration_confirmation_url': case 'url': case 'website_url': $table_td = ($data == '' ? '' : '<a href="'.$data.'">'.($data == 'http://'.$_SERVER['SERVER_NAME'] ? '/' : str_replace('http://'.$_SERVER['SERVER_NAME'], '', $data)).'</a>'); break;
case 'status': if ($data == 'active') { $table_td = '<a style="color: #008000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=active">'.__('Active', 'membership-manager').'</a>'; }
elseif ($data == 'inactive') { $table_td = '<a style="color: #e08000;" href="admin.php?page='.$_GET['page'].'&amp;'.$column.'=inactive">'.__('Inactive', 'membership-manager').'</a>'; }
else { $table_td = $data; } break;
case 'website_name': $website_url = htmlspecialchars(membership_format_data($column, $item->website_url)); $table_td = ($website_url == '' ? $data : '<a href="'.$website_url.'">'.($data == '' ? str_replace('http://'.$_SERVER['SERVER_NAME'], '', $website_url) : $data).'</a>'); break;
default: $table_td = $data; }
return $table_td; }


function table_th($table, $column) {
include 'tables.php';
if (strstr($_GET['page'], 'statistics')) { $table_th = '<th scope="col" class="manage-column" style="width: '.$tables[$table][$column]['width'].'%;">'.$tables[$table][$column]['name'].'</th>'; }
else {
$table_th = '<th scope="col" class="manage-column '.($_GET['orderby'] == $column ? 'sorted '.$_GET['order'] : 'sortable desc').'" style="width: '.$tables[$table][$column]['width'].'%;">
<a href="admin.php?page='.$_GET['page'].'&amp;orderby='.$column.'&amp;order='.(($_GET['orderby'] == $column && $_GET['order'] == 'asc') ? 'desc' : 'asc').
$_GET['selection_criteria'].($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']).'">
<span>'.$tables[$table][$column]['name'].'</span><span class="sorting-indicator"></span></a></th>'; }
return $table_th; }


function tablenav_pages($table, $n, $max_paged, $location) {
switch ($table) {
case 'members': $singular = __('member', 'membership-manager'); $plural = __('members', 'membership-manager'); break;
case 'members_areas': $singular = __('member area', 'membership-manager'); $plural = __('members areas', 'membership-manager'); break;
case 'members_areas_categories': case 'members_categories': $singular = __('category', 'membership-manager'); $plural = __('categories', 'membership-manager'); break;
default: $singular = __('item', 'membership-manager'); $plural = __('items', 'membership-manager'); }
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'].$_GET['selection_criteria'].($_GET['s'] == '' ? '' : '&amp;s='.$_GET['s']);
echo '<div class="tablenav-pages" style="float: right;"><span class="displaying-num">'.$n.' '.($n <= 1 ? $singular : $plural).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>'; }


remove_shortcode('membership-user');