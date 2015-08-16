<?php if ((function_exists('current_user_can')) && (!current_user_can('manage_options'))
 && (function_exists('user_can')) && (!user_can($data['post_author'], 'manage_options'))) {
global $content_switcher_shortcodes;
foreach ((array) $content_switcher_shortcodes as $tag) {
foreach (array('post_content', 'post_content_filtered', 'post_excerpt', 'post_title') as $key) {
$data[$key] = str_replace(array('['.$tag, $tag.']'), array('&#91;'.$tag, $tag.'&#93;'), $data[$key]); } } }