<?php function kleor_do_shortcode($content) {
if (strstr($content, '[')) {
$pattern = get_shortcode_regex();
$content = preg_replace_callback("/$pattern/s", 'kleor_do_shortcode_tag', $content); }
return $content; }


function kleor_do_shortcode_in_attribute($string) {
$string = (string) $string;
$string = kleor_do_shortcode(str_replace(array('(', ')'), array('[', ']'), $string));
$string = str_replace(array('[', ']'), array('(', ')'), $string);
$string = str_replace(array('&#40;', '&#41;', '&#91;', '&#93;'), array('(', ')', '[', ']'), $string);
return $string; }


function kleor_do_shortcode_tag($m) {
global $shortcode_tags;
if (($m[1] == '[') && ($m[6] == ']')) { return substr($m[0], 1, -1); }
else { return $m[1].call_user_func($shortcode_tags[$m[2]], kleor_shortcode_parse_atts($m[3]), (isset($m[5]) ? $m[5] : null), $m[2]).$m[6]; } }


function kleor_shortcode_atts($default_values, $atts) {
$atts = (array) $atts; $string = '';
foreach ($atts as $key => $value) { if (is_int($key)) { $string .= $value.' '; } }
$string = trim($string);
if (strstr($string, '=')) {
$new_keys = array();
$array = explode('=', $string);
$n = count($array); for ($i = 0; $i < $n - 1; $i++) {
$array2 = array_reverse(explode(' ', $array[$i]));
if (($array2[0] != '') && (!in_array(substr($array2[0], 0, 1), array('"', "'")))
 && (!in_array($array2[0], $new_keys))) { $new_keys[] = $array2[0]; } }
foreach ($new_keys as $key) {
if ($string != '') {
$array = explode($key.'=', $string);
if (!isset($array[1])) { $string = ''; }
else {
$string = $array[1];
$n = count($array); for ($i = 2; $i < $n; $i++) { $string .= $key.'='.$array[$i]; }
$character = substr($string, 0, 1); switch ($character) {
case '"': case "'": $array2 = explode($character, $string);
$atts[$key] = $array2[1]; $string = substr($string, strlen($array2[1]) + 2); break;
default: $array2 = explode(' ', $string);
$atts[$key] = $array2[0]; $string = substr($string, strlen($array2[0])); } } } } }
$atts = array_map('kleor_do_shortcode_in_attribute', (array) $atts);
foreach ($default_values as $key => $value) {
if ((!isset($atts[$key])) || ($atts[$key] == '')) { $atts[$key] = $default_values[$key]; } }
return $atts; }


function kleor_shortcode_parse_atts($text) {
$atts = array();
$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
foreach ($match as $m) {
if (!empty($m[1])) { $atts[strtolower($m[1])] = stripcslashes($m[2]); }
elseif (!empty($m[3])) { $atts[strtolower($m[3])] = stripcslashes($m[4]); }
elseif (!empty($m[5])) { $atts[strtolower($m[5])] = stripcslashes($m[6]); }
elseif ((isset($m[7])) && (strlen($m[7]))) { $atts[] = stripcslashes($m[7]); }
elseif (isset($m[8])) { $atts[] = stripcslashes($m[8]); } } }
else { $atts = ltrim($text); }
return $atts; }