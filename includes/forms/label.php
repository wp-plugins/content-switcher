<?php $form_id = $GLOBALS['installations_form_id'];
$prefix = $GLOBALS['installations_form_prefix'];
$atts = installations_shortcode_atts(array(0 => 'email_address'), $atts);
$markup = '';
foreach ($atts as $key => $value) {
if ((is_string($key)) && ($value != '')) { $c = (strstr($value, '"') ? "'" : '"'); $markup .= ' '.$key.'='.$c.$value.$c; } }
$name = str_replace('-', '_', format_nice_name($atts[0]));
$content = '<label for="'.$prefix.$name.'"'.$markup.'>'.do_shortcode($content).'</label>';