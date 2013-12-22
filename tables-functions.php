<?php global $wpdb;

foreach ((array) $_POST as $key => $value) { if (is_string($value)) { $_POST[$key] = quotes_entities($_POST[$key]); } }
$GLOBALS['selection_criteria'] = ''; $selection_criteria = '';
foreach (all_tables_keys($tables) as $field) {
if (isset($_GET[$field])) {
$GLOBALS['selection_criteria'] .= '&amp;'.$field.'='.str_replace(' ', '%20', $_GET[$field]);
$selection_criteria .= ($field == "plugins" ? " AND (".$field." LIKE '%".$_GET[$field]."%')" :
 (is_numeric($_GET[$field]) ? " AND (".$field." = ".$_GET[$field].")" : " AND (".$field." = '".$_GET[$field]."')")); } }


function all_tables_keys($tables) {
$keys = array();
foreach ($tables as $table_slug => $table) {
foreach ($table as $key => $value) {
if (!in_array($key, $keys)) { $keys[] = $key; } } }
return $keys; }


function no_items($table) { return __('No websites', 'installations-manager'); }


function row_actions($table, $item) {
$row_actions = 
'<span class="edit"><a href="admin.php?page=installations-manager-website&amp;id='.$item->id.'">'.__('Edit', 'installations-manager').'</a></span>
 | <span class="delete"><a href="admin.php?page=installations-manager-website&amp;id='.$item->id.'&amp;action=delete">'.__('Delete', 'installations-manager').'</a></span>';
return '<div class="row-actions" style="margin-top: 2em; position: absolute;">'.$row_actions.'</div>'; }


function single_page_slug($table) { return 'website'; }


function table_criteria($table) {
switch ($table) {
case 'affiliation_manager_websites': $table_criteria = " AND plugins LIKE '%Affiliation Manager%'"; break;
case 'commerce_manager_websites': $table_criteria = " AND plugins LIKE '%Commerce Manager%'"; break;
case 'contact_manager_websites': $table_criteria = " AND plugins LIKE '%Contact Manager%'"; break;
case 'membership_manager_websites': $table_criteria = " AND plugins LIKE '%Membership Manager%'"; break;
case 'optin_manager_websites': $table_criteria = " AND plugins LIKE '%Optin Manager%'"; break;
default: $table_criteria = ''; }
return $table_criteria; }


function table_name($table) {
global $wpdb;
return $wpdb->prefix.'installations_manager_websites'; }


function table_undisplayed_keys($tables, $table, $back_office_options) {
global $wpdb;
$undisplayed_modules = (array) $back_office_options[single_page_slug($table).'_page_undisplayed_modules'];
$undisplayed_keys = array();
foreach ($tables[$table] as $key => $value) {
foreach ((array) $value['modules'] as $module) {
if (in_array($module, $undisplayed_modules)) { $undisplayed_keys[] = $key; } } }
return $undisplayed_keys; }


function table_data($table, $column, $item) {
$GLOBALS['website_id'] = $item->id;
$GLOBALS['website_data'] = (array) $item;
$data = website_data($column);
return $data; }


function table_td($table, $column, $item) {
$data = htmlspecialchars(table_data($table, $column, $item));
switch ($column) {
case 'language_code': $table_td = ($data == '' ? '' : '<a href="admin.php?page='.$_GET['page'].$GLOBALS['criteria'].'&amp;'.$column.'='.str_replace(' ', '%20', $data).'">'.installations_excerpt($data, 50).'</a>'); break;
case 'name': $url = htmlspecialchars(table_data($table, 'url', $item)); $table_td = ($url == '' ? installations_excerpt($data, 50) : '<a href="'.$url.'">'.installations_excerpt(($data == '' ? str_replace(ROOT_URL, '', $url) : $data), 50).'</a>'); break;
case 'plugins':
$plugins = explode(',', $data);
$plugins_list = '';
foreach ($plugins as $plugin) {
$plugin = trim($plugin);
$plugin = '<a href="admin.php?page='.$_GET['page'].$GLOBALS['criteria'].'&amp;plugins='.$plugin.'">'.$plugin.'</a>';
$plugins_list .= $plugin.', '; }
$table_td = substr($plugins_list, 0, -2); break;
case 'url': $table_td = ($data == '' ? '' : '<a href="'.$data.'">'.($data == ROOT_URL ? '/' : installations_excerpt(str_replace(ROOT_URL, '', $data), 80)).'</a>'); break;
default: $table_td = installations_excerpt($data); }
return $table_td; }


function table_th($tables, $table, $column) {
$reverse_order = ($_GET['order'] == 'asc' ? 'desc' : 'asc');
$table_th = '<th scope="col" class="manage-column '.($_GET['orderby'] == $column ? 'sorted '.$_GET['order'] : 'sortable '.$reverse_order).'" style="width: '.$tables[$table][$column]['width'].'%;">
<a href="admin.php?page='.$_GET['page'].$GLOBALS['criteria'].'&amp;orderby='.$column.'&amp;order='.($_GET['orderby'] == $column ? $reverse_order : $_GET['order']).'">
<span>'.$tables[$table][$column]['name'].'</span><span class="sorting-indicator"></span></a></th>';
return $table_th; }


function tablenav_pages($table, $n, $max_paged, $location) {
$singular = __('website', 'installations-manager'); $plural = __('websites', 'installations-manager');
if ($_GET['paged'] == 1) { $prev_paged = 1; } else { $prev_paged = $_GET['paged'] - 1; }
if ($_GET['paged'] == $max_paged) { $next_paged = $max_paged; } else { $next_paged = $_GET['paged'] + 1; }
$url = 'admin.php?page='.$_GET['page'].$GLOBALS['criteria'].'&amp;orderby='.$_GET['orderby'].'&amp;order='.$_GET['order'];
echo '<div class="tablenav-pages" style="float: right;"><span class="displaying-num">'.$n.' '.($n <= 1 ? $singular : $plural).'</span>
<a class="first-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the first page', 'installations-manager').'" href="'.$url.'&amp;paged=1">&laquo;</a>
<a class="prev-page'.($_GET['paged'] == 1 ? ' disabled' : '').'" title="'.__('Go to the previous page', 'installations-manager').'" href="'.$url.'&amp;paged='.$prev_paged.'">&lsaquo;</a>
<span class="paging-input">'.($location == 'top' ? '<input class="current-page" title="'.__('Current page', 'installations-manager').'" type="text" name="paged" id="paged" value="'.$_GET['paged'].'" size="2" />' : $_GET['paged']).' '.__('of', 'installations-manager').' <span class="total-pages">'.$max_paged.'</span></span>
<a class="next-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the next page', 'installations-manager').'" href="'.$url.'&amp;paged='.$next_paged.'">&rsaquo;</a>
<a class="last-page'.($_GET['paged'] == $max_paged ? ' disabled' : '').'" title="'.__('Go to the last page', 'installations-manager').'" href="'.$url.'&amp;paged='.$max_paged.'">&raquo;</a></div>'; }