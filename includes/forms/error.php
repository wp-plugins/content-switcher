<?php $form_id = $GLOBALS['installations_form_id'];
$prefix = $GLOBALS['installations_form_prefix'];
$atts = installations_shortcode_atts(array(0 => 'email_address', 'class' => 'error', 'style' => ''), $atts);
$markup = '';
$name = str_replace('-', '_', format_nice_name($atts[0]));
if ((!isset($GLOBALS[$prefix.$name.'_error'])) && (!stristr($atts['style'], 'display:')) && (!stristr($atts['style'], 'display :'))) { $atts['style'] = 'display: none; '.$atts['style']; }
foreach ($atts as $key => $value) {
if (($key != 'id') && (is_string($key)) && ($value != '')) { $c = (strstr($value, '"') ? "'" : '"'); $markup .= ' '.$key.'='.$c.$value.$c; } }
$content = '<span id="'.$prefix.$name.'_error"'.$markup.'>'.(isset($GLOBALS[$prefix.$name.'_error']) ? $GLOBALS[$prefix.$name.'_error'] : '').'</span>';