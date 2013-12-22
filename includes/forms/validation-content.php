<?php $form_id = $GLOBALS['installations_form_id'];
$prefix = $GLOBALS['installations_form_prefix'];
$atts = array_map('installations_do_shortcode', (array) $atts);
extract(shortcode_atts(array('filter' => ''), $atts));
$content = explode('[other]', do_shortcode($content));
if (!isset($_POST[$prefix.'submit'])) { $n = 2; }
elseif ((isset($GLOBALS['form_error'])) && ($GLOBALS['form_error'] == 'yes')) { $n = 1; }
else { $n = 0; }
if (!isset($content[$n])) { $content[$n] = ''; }
$content = installations_filter_data($filter, $content[$n]);