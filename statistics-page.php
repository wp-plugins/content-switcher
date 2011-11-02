<?php include 'tables.php';
include_once 'tables-functions.php';
add_action('admin_footer', 'membership_date_picker_js');
$back_office_options = get_option('membership_manager_back_office');
$undisplayed_rows = (array) $back_office_options['statistics_page_undisplayed_rows'];
$undisplayed_columns = (array) $back_office_options['statistics_page_undisplayed_columns'];
include 'admin-pages.php';
$options = get_option('membership_manager_statistics');

$tables_names = array(
'members' => __('Members', 'membership-manager'),
'members_areas' => __('Members areas', 'membership-manager'),
'members_areas_categories' => __('Members areas categories', 'membership-manager'),
'members_categories' => __('Members categories', 'membership-manager'));
$max_tables = count($tables_names);

$filterby_options = array(
'postcode' => __('postcode', 'membership-manager'),
'town' => __('town', 'membership-manager'),
'country' => __('country', 'membership-manager'),
'ip_address' => __('IP address ', 'membership-manager'),
'user_agent' => __('user agent', 'membership-manager'),
'referring_url' => __('referring URL', 'membership-manager'));

if ((isset($_POST['submit'])) && (check_admin_referer($_GET['page']))) {
$_POST = array_map('stripslashes', $_POST);
$_GET['s'] = $_POST['s'];
$filterby = $_POST['filterby'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$start_table = (int) $_POST['start_table'] - 1;
$start_table = $start_table % $max_tables;
if ($start_table < 0) { $start_table = $start_table + $max_tables; }
for ($i = 0; $i < $max_tables; $i++) { $tables_slugs[$i] = $_POST['table'.$i]; }
$tables_number = (int) $_POST['tables_number'];
if ($tables_number > $max_tables) { $tables_number = $max_tables; }
elseif ($tables_number < 1) { $tables_number = 0; } }
else {
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
$end_date = date('Y-m-d H:i:s', time() + 3600*UTC_OFFSET);
$filterby = $options['filterby'];
$start_date = $options['start_date'];
$start_table = $options['start_table'];
$tables_slugs = $options['tables'];
$tables_number = $options['tables_number']; }

$start_date = trim(mysql_real_escape_string(strip_tags($start_date)));
if (strlen($start_date) == 10) { $start_date .= ' 00:00:00'; }
$end_date = trim(mysql_real_escape_string(strip_tags($end_date)));
if (strlen($end_date) == 10) { $end_date .= ' 23:59:59'; }

if ($options) {
$options = array(
'filterby' => $filterby,
'start_date' => $start_date,
'start_table' => $start_table,
'tables' => $tables_slugs,
'tables_number' => $tables_number);
update_option('membership_manager_statistics', $options); }

for ($i = 0; $i < $max_tables; $i++) { $tables_slugs[$max_tables + $i] = $tables_slugs[$i]; }

if ($_GET['s'] != '') { $filter_criteria = "AND (".$filterby." = '".$_GET['s']."')"; }

$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."membership_manager_members WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$members_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."membership_manager_members WHERE status = 'active' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$active_members_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."membership_manager_members WHERE status = 'inactive' AND (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$inactive_members_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."membership_manager_members_categories WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$members_categories_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."membership_manager_members_areas WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$members_areas_number = (int) $row->total;
$row = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."membership_manager_members_areas_categories WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria", OBJECT);
$members_areas_categories_number = (int) $row->total;

$members_a_tag = '<a style="text-decoration: none;" href="admin.php?page=membership-manager-members">';
$active_members_a_tag = '<a style="color: #008000; text-decoration: none;" href="admin.php?page=membership-manager-members&amp;status=active">';
$inactive_members_a_tag = '<a style="color: #e08000; text-decoration: none;" href="admin.php?page=membership-manager-members&amp;status=inactive">';
$members_categories_a_tag = '<a style="text-decoration: none;" href="admin.php?page=membership-manager-members-categories">';
$members_areas_a_tag = '<a style="text-decoration: none;" href="admin.php?page=membership-manager-members-areas">';
$members_areas_categories_a_tag = '<a style="text-decoration: none;" href="admin.php?page=membership-manager-members-areas-categories">'; ?>

<div class="wrap">
<div id="poststuff">
<?php membership_manager_pages_top($back_office_options); ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
<?php wp_nonce_field($_GET['page']); ?>
<?php membership_manager_pages_menu($back_office_options); ?>
<?php membership_manager_pages_search_field('filter', $filterby, $filterby_options); ?>
<?php membership_manager_pages_date_picker($start_date, $end_date); ?>
<?php $global_table_ths = '
<th scope="col" class="manage-column" style="width: 30%;">'.$statistics_columns['data']['name'].'</th>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<th scope="col" class="manage-column" style="width: 20%;">'.$statistics_columns['quantity']['name'].'</th>').'
'.(in_array('members_percentage', $undisplayed_columns) ? '' : '<th scope="col" class="manage-column" style="width: 30%;">'.$statistics_columns['members_percentage']['name'].'</th>');
echo '
<h3 id="global-statistics"><strong>'.__('Global statistics', 'membership-manager').'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$global_table_ths.'</tr></thead>
<tfoot><tr>'.$global_table_ths.'</tr></tfoot>
<tbody>';
if (!in_array('members', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['members']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$members_a_tag.$members_number.'</a></td>').'
'.(in_array('members_percentage', $undisplayed_columns) ? '' : '<td>'.$members_a_tag.($members_number == 0 ? '--' : '100 %').'</a></td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('active_members', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['active_members']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$active_members_a_tag.$active_members_number.'</a></td>').'
'.(in_array('members_percentage', $undisplayed_columns) ? '' : '<td>'.($members_number == 0 ? '--' : $active_members_a_tag.((round(10000*$active_members_number/$members_number))/100).' %</a>').'</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('inactive_members', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['inactive_members']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$inactive_members_a_tag.$inactive_members_number.'</a></td>').'
'.(in_array('members_percentage', $undisplayed_columns) ? '' : '<td>'.($members_number == 0 ? '--' : $inactive_members_a_tag.((round(10000*$inactive_members_number/$members_number))/100).' %</a>').'</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('members_categories', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['members_categories']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$members_categories_a_tag.$members_categories_number.'</a></td>').'
'.(in_array('members_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('members_areas', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['members_areas']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$members_areas_a_tag.$members_areas_number.'</a></td>').'
'.(in_array('members_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
if (!in_array('members_areas_categories', $undisplayed_rows)) { echo '
<tr'.($boolean ? '' : ' class="alternate"').'>
<td><strong>'.$statistics_rows['members_areas_categories']['name'].'</strong></td>
'.(in_array('quantity', $undisplayed_columns) ? '' : '<td>'.$members_areas_categories_a_tag.$members_areas_categories_number.'</a></td>').'
'.(in_array('members_percentage', $undisplayed_columns) ? '' : '<td>--</td>').'
</tr>'; $boolean = !$boolean; }
echo '</tbody></table>'; ?>
<div style="text-align: center;">
<?php for ($i = 0; $i < $max_tables; $i++) {
echo '<label>'.__('Table', 'membership-manager').' '.($i + 1).' <select'.($i < 9 ? ' style="margin-right: 0.75em;"': '').' name="table'.$i.'" id="table'.$i.'">';
foreach ($tables_names as $key => $value) { echo '<option value="'.$key.'"'.($tables_slugs[$i] == $key ? ' selected="selected"' : '').'>'.$value.'</option>'."\n"; }
echo '</select></label>'; } ?><br />
<?php _e('Display', 'membership-manager'); ?> <input style="text-align: center;" type="text" name="tables_number" id="tables_number" size="2" value="<?php echo $tables_number; ?>" /> 
<?php _e('tables starting from table', 'membership-manager'); ?> <input style="text-align: center;" type="text" name="start_table" id="start_table" size="2" value="<?php echo $start_table + 1; ?>" /> 
<input type="submit" class="button-secondary" name="submit" value="<?php _e('Update'); ?>" />
</div>
<?php if ($tables_number > 1) {
for ($i = 1; $i < $tables_number; $i++) { $summary .= '<li>| <a href="#'.str_replace('_', '-', $tables_slugs[$start_table + $i]).'">'.$tables_names[$tables_slugs[$start_table + $i]].'</a></li>'; }
$summary = '<ul class="subsubsub" style="float: none; text-align: center;">
<li><a href="#'.str_replace('_', '-', $tables_slugs[$start_table]).'">'.$tables_names[$tables_slugs[$start_table]].'</a></li>
'.$summary.'</ul>'; }
for ($i = 0; $i < $tables_number; $i++) {
$table_slug = $tables_slugs[$start_table + $i];
$table_name = table_name($table_slug);
$options = get_option('membership_manager_'.$table_slug);
$columns = $options['columns'];
$max_columns = count($columns);
$columns_number = $options['columns_number'];
$start_column = $options['start_column'];
for ($j = 0; $j < $max_columns; $j++) { $columns[$max_columns + $j] = $columns[$j]; }
for ($j = 0; $j < $columns_number; $j++) { $table_ths .= table_th($table_slug, $columns[$start_column + $j]); }
echo $summary.'
<h3 id="'.str_replace('_', '-', $tables_slugs[$start_table + $i]).'"><strong>'.$tables_names[$tables_slugs[$start_table + $i]].'</strong></h3>
<table class="wp-list-table widefat fixed" style="margin: 1em 0 2em 0;">
<thead><tr>'.$table_ths.'</tr></thead>
<tfoot><tr>'.$table_ths.'</tr></tfoot>
<tbody>';
$items = $wpdb->get_results("SELECT * FROM $table_name WHERE (date BETWEEN '$start_date' AND '$end_date') $selection_criteria $filter_criteria ORDER BY date DESC", OBJECT);
if ($items) { foreach ($items as $item) {
for ($j = 1; $j < $columns_number; $j++) { $table_tds .= '<td>'.table_td($table_slug, $columns[$start_column + $j], $item).'</td>'; }
echo '<tr'.($boolean ? '' : ' class="alternate"').'><td style="height: 6em;">'.table_td($table_slug, $columns[$start_column], $item).row_actions($table_slug, $item).'</td>'.$table_tds.'</tr>';
$table_tds = ''; $boolean = !$boolean; } }
else { echo '<tr class="no-items"><td class="colspanchange" colspan="'.$columns_number.'">'.no_items($table_slug).'</td></tr>'; }
echo '</tbody></table>';
$table_ths = ''; } ?>
</form>
</div>
</div>