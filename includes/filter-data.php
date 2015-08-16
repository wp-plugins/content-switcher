<?php if (is_string($filter)) { $filter = preg_split('#[^a-zA-Z0-9_]#', str_replace('-', '_', kleor_do_shortcode_in_attribute($filter)), 0, PREG_SPLIT_NO_EMPTY); }
if (is_array($filter)) { foreach ($filter as $function) {
if ($function != 'eval') {
if (!function_exists($function)) { $function = 'content_switcher_'.$function; }
if (function_exists($function)) { $data = @$function($data); } } } }