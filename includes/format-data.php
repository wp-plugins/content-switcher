<?php $data = do_shortcode($data);
if ($field != 'code') { $data = quotes_entities_decode($data); }
if (($data == '0000-00-00 00:00:00') && ((substr($field, -4) == 'date') || (substr($field, -8) == 'date_utc'))) { $data = ''; }
elseif (substr($field, -13) == 'email_address') { $data = format_email_address($data); }
elseif (($field == 'url') || (substr($field, -4) == '_url')
 || ((is_numeric(substr($field, -1))) && (substr($field, -5, -1) == '_url'))) { $data = format_url($data); }
switch ($field) {
case 'id': $data = (int) $data; break; }