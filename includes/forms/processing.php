<?php $GLOBALS[$prefix.'processed'] = 'yes';
if (!isset($GLOBALS['form_error'])) { $GLOBALS['form_error'] = ''; }
foreach ($GLOBALS[$prefix.'required_fields'] as $field) {
if (((!isset($_POST[$prefix.$field])) || ($_POST[$prefix.$field] == '')) && ((!isset($_FILES[$prefix.$field])) || ($_FILES[$prefix.$field]['error'] == 4))) {
$GLOBALS[$prefix.'unfilled_fields_error'] = installations_data('unfilled_fields_message'); $GLOBALS['form_error'] = 'yes'; } }
$invalid_captcha = '';
if (isset($GLOBALS[$prefix.'recaptcha_js'])) {
$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
if (!$resp->is_valid) { $invalid_captcha = 'yes'; } }
elseif (in_array('captcha', $GLOBALS[$prefix.'fields'])) {
if (hash('sha256', $_POST[$prefix.'captcha']) != $_POST[$prefix.'valid_captcha']) { $invalid_captcha = 'yes'; } }
if ($invalid_captcha == 'yes') { $GLOBALS[$prefix.'invalid_captcha_error'] = installations_data('invalid_captcha_message'); $GLOBALS['form_error'] = 'yes'; }
if ($GLOBALS['form_error'] == '') {
foreach ($_POST as $key => $value) { if (strstr($key, $prefix)) {
$_POST[str_replace($prefix, $canonical_prefix, $key)] = $value;
$_POST[str_replace($prefix, '', $key)] = $value; } }
$_POST['ip_address'] = $_SERVER['REMOTE_ADDR'];
$_POST['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

if (($redirection != '') && (substr($redirection, 0, 1) != '#')) {
$redirection = format_url($redirection);
if (!headers_sent()) { header('Location: '.$redirection); exit; }
else { $content .= '<script type="text/javascript">window.location = \''.htmlspecialchars($redirection).'\';</script>'; } } }