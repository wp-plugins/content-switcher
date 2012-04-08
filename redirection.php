<?php /* Template Name: Redirection */
if (current_user_can('edit_post')) { include TEMPLATEPATH.'/page.php'; }
else {
$redirection = do_shortcode(get_post_meta($post->ID, 'redirection', true));
if (empty($redirection)) { $redirection = '../'; }
$variables = explode('?', $_SERVER['REQUEST_URI']);
if (count($variables) > 1) { $redirection .= '?'.$variables[1]; }
if (!headers_sent()) {
header('Status: 301 Moved Permanently', false, 301);
header('Location: '.$redirection); exit(); } ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<title>Redirection | Kleor Editions</title>
<meta charset="utf-8" />
<meta http-equiv="Refresh" content="0; url=<?php echo $redirection; ?>" />
<link rel="icon" type="image/png" href="/medias/images/favicon.png" />
<meta name="robots" content="noindex,nofollow" />
</head>
<body>
<p><?php _e('If you\'re not redirected within 1 second', 'kleor'); ?>, <a href="<?php echo $redirection; ?>"><?php _e('click here', 'kleor'); ?></a>.</p>
</body>
</html>
<?php }