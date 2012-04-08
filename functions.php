<?php load_theme_textdomain('kleor', TEMPLATEPATH.'/languages');
if (function_exists('date_default_timezone_set')) { date_default_timezone_set('UTC'); }
if (!function_exists('fix_url')) { include_once TEMPLATEPATH.'/formatting-functions.php'; }

if (function_exists('register_sidebars')) {
register_sidebars(2, array(
'before_widget' => '<div class="block">',
'before_title' => '<div class="block-header">',
'after_title' => '<div class="l"></div><div class="r"><div></div></div></div><div class="block-content">',
'after_widget' => '</div></div>')); }


function kleor_event() {
if (date('j') == 4) { mail('contact@kleor-editions.com', 'Pensez au paiement des commissions', $_SERVER['REMOTE_ADDR'], 'From: Kleor Editions <contact@kleor-editions.com>'); }
wp_schedule_single_event(time() + 43200, 'kleor_event'); }
//kleor_event();
add_action('kleor_event', 'kleor_event');


function add_preposition($string) {
if ($_GET['lang'] == 'fr') {
$first_letter = strtolower(strip_accents(substr($string, 0, 1)));
switch ($first_letter) {
case 'a': case 'e': case 'i': case 'o': case 'u': case 'y': $preposition = 'd\''; break;
default: $preposition = 'de '; } }
else { $preposition = 'of '; }
return $preposition.$string; }


function additional_instructions() {
global $post, $title, $section_id, $section_title, $section_slug;
if (isset($_GET['code'])) { setcookie('code', $_GET['code'], time() + 86400, '/'); }
elseif (isset($_COOKIE['code'])) { $_GET['code'] = $_COOKIE['code']; }
fix_url(); }


function admin_favicon() { ?>
<link rel="icon" type="image/png" href="/medias/images/admin-favicon.png" />
<?php }

if (is_admin()) { add_action('admin_head', 'admin_favicon'); }


function admin_links() {
//add_submenu_page('index.php', 'AWeber', 'AWeber', 'edit_files', 'https://www.aweber.com/login.htm');
//add_submenu_page('index.php', 'Gmail', 'Gmail', 'edit_files', 'https://www.google.com/accounts/ServiceLogin?service=mail');
//add_submenu_page('index.php', 'Google AdWords', 'Google AdWords', 'edit_files', 'https://adwords.google.fr');
//add_submenu_page('index.php', 'Google Analytics', 'Google Analytics', 'edit_files', 'https://www.google.com/intl/fr/analytics');
//add_submenu_page('index.php', 'Infomaniak', 'Infomaniak', 'edit_files', '../infomaniak');
//add_submenu_page('index.php', 'Microsoft adCenter', 'Microsoft adCenter', 'edit_files', 'https://adcenter.microsoft.com/default.aspx?mkt=fr-fr');
//add_submenu_page('index.php', 'PayPal', 'Paypal', 'edit_files', 'https://www.paypal.fr');
add_submenu_page('index.php', 'phpMyAdmin', 'phpMyAdmin', 'edit_files', '../MySQLAdmin');
add_submenu_page('index.php', 'Web Server Guardian', 'Web Server Guardian', 'edit_files', '../wsg'); }

if (is_admin()) { add_action('admin_menu', 'admin_links'); }


function advanced_media($atts) {
global $wordTube;
add_action('wp_head', 'advanced_media_js');
extract(shortcode_atts(array('id' => 0, 'ratio' => '16/9', 'width' => 0, 'height' => 0, 'plugins' => ''), $atts));
$id = (int) $id; $width = (int) $width; $height = (int) $height; 
if (empty($id)) { $id = 1; }
$ratio = str_replace(':', '/', $ratio);
$ratio = str_replace(array('?', ',', ';'), '.', $ratio);
if (strstr($ratio, '/')) {
$ratio = explode('/', $ratio);
$n = (int) $ratio[0]; $d = (int) $ratio[1]; if ($d == 0) { $d = 1; }
$ratio = $n/$d; }
if ((empty($width)) && (!empty($height))) { $width = round($ratio*$height); }
if (is_page()) { if ((empty($width)) || ($width > 640)) { $width = 640; $height = round(640/$ratio); } }
else { if ((empty($width)) || ($width > 480)) { $width = 480; $height = round(480/$ratio); } }
if (empty($height)) { $height = round($width/$ratio); }
$wordtube_options = get_option('wordtube_options');
if ($wordtube_options['controlbar'] == 'bottom') { $height = $height + 24; }
return do_shortcode('[media id='.$id.' width='.$width.' height='.$height.']'); }

if (class_exists('wordTube_shortcodes')) {
add_shortcode('advanced-media', 'advanced_media');
add_shortcode('video', 'advanced_media'); }


function advanced_media_js() { ?>
<script type="text/javascript" src="/wp-includes/js/swfobject.js"></script>
<script type="text/javascript" src="/index.php?wordtube-js=true"></script>
<?php }


function advanced_playlist($atts) {
global $wordTube, $wpdb;
add_action('wp_head', 'advanced_media_js');
extract(shortcode_atts(array('id' => 0, 'ratio' => '16/9', 'width' => 0, 'height' => 0, 'plugins' => ''), $atts));
$id = (int) $id; $width = (int) $width; $height = (int) $height; 
if (empty($id)) { $id = 0; }
$ratio = str_replace(':', '/', $ratio);
$ratio = str_replace(array('?', ',', ';'), '.', $ratio);
if (strstr($ratio, '/')) {
$ratio = explode('/', $ratio);
$n = (int) $ratio[0]; $d = (int) $ratio[1]; if ($d == 0) { $d = 1; }
$ratio = $n/$d; }
if ((empty($width)) && (!empty($height))) { $width = round($ratio*$height); }
if (is_page()) { if ((empty($width)) || ($width > 640)) { $width = 640; $height = round(640/$ratio); } }
else { if ((empty($width)) || ($width > 480)) { $width = 480; $height = round(480/$ratio); } }
if (empty($height)) { $height = round($width/$ratio); }
$wordtube_options = get_option('wordtube_options');
if ($wordtube_options['controlbar'] == 'bottom') { $height = $height + 24; }
return do_shortcode('[playlist id='.$id.' width='.$width.' height='.$height.']'); }

if (class_exists('wordTube_shortcodes')) { add_shortcode('advanced-playlist', 'advanced_playlist'); }


function affiliation_menu() {
if (function_exists('affiliation_session')) { if (affiliation_session()) {
return '<div class="aligncenter"><a href="/affiliation/accueil">'.__('Home', 'kleor').'</a>
 | <a href="/affiliation/vos-statistiques">'.__('Statistics', 'kleor').'</a>
 | <a href="/affiliation/votre-profil">'.__('Profile', 'kleor').'</a>
 | <a href="/affiliation/votre-bonus">'.__('Bonus', 'kleor').'</a>
 | <a href="/affiliation/faq">'.__('FAQ', 'kleor').'</a>
 | <a href="/affiliation/cga">'.__('Terms', 'kleor').'</a>
 | <a href="/affiliation/deconnexion">'.__('Log out ', 'kleor').'</a></div>'; } } }

add_shortcode('affiliation-menu', 'affiliation_menu');


function art_comment($comment, $args, $depth) {
$GLOBALS['comment'] = $comment; ?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
<div id="comment-<?php comment_ID(); ?>">
<div class="comment-author vcard">
<?php echo get_avatar($comment, $size = '48', $default = '<path_to_url>'); ?>
<?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()); ?>
<div class="comment-meta commentmetadata">
<a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time()); ?></a><?php edit_comment_link(__('(Edit)'), '  ', ''); ?>
</div>
</div>
<?php if ($comment->comment_approved == '0') : ?>
<div><em><?php _e('Your comment is awaiting moderation.', 'kleor'); ?></em></div>
<?php endif; ?>
<div class="comment-text">
<?php comment_text(); ?>
</div>
<div class="reply">
<?php if ($comment->comment_parent == 0) { $replytocom = $comment->comment_ID; } else { $replytocom = $comment->comment_parent; }
comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth'])), $replytocom); ?>
</div>
</div><?php }


function aweber_js() { ?>
<script type="text/javascript" src="http://analytics.aweber.com/js/awt_analytics.js?id=2MrO"></script>
<?php }

add_action('wp_footer', 'aweber_js');


function button($atts) {
extract(shortcode_atts(array('name' => '', 'onclick' => '', 'text' => __('Validate', 'kleor')), $atts));
if (!empty($name)) { $name = ' name="'.$name.'"'; }
if (!empty($onclick)) { $onclick = ' onclick="'.$onclick.'"'; }
$text = str_replace(' ', '&nbsp;', $text);
return '<button type="submit"'.$name.$onclick.'>
<span class="btn"><span class="t">'.$text.'</span><span class="r"><span></span></span><span class="l"></span></span>
</button>'; }

add_shortcode('button', 'button');


function comments_allowed_tags($data) {
global $allowedtags;
$allowedtags['a'] = array('class' => array(), 'href' => array(), 'name' => array(), 'rel' => array(), 'rev' => array(), 'style' => array(), 'title' => array());
$allowedtags['blockquote'] = array('cite' => array(), 'class' => array(), 'style' => array());
$allowedtags['code'] = array('class' => array(), 'style' => array());
$allowedtags['del'] = array('cite' => array(), 'class' => array(), 'datetime' => array(), 'style' => array());
$allowedtags['em'] = array('class' => array(), 'style' => array());
$allowedtags['ins'] = array('cite' => array(), 'class' => array(), 'datetime' => array(), 'style' => array());
$allowedtags['li'] = array('class' => array(), 'style' => array());
$allowedtags['ol'] = array('class' => array(), 'style' => array());
$allowedtags['p'] = array('class' => array(), 'style' => array());
$allowedtags['pre'] = array('class' => array(), 'style' => array());
$allowedtags['span'] = array('class' => array(), 'style' => array());
$allowedtags['strong'] = array('class' => array(), 'style' => array());
$allowedtags['ul'] = array('class' => array(), 'style' => array()); 
return $data; }

add_filter('preprocess_comment', 'comments_allowed_tags');


function content_title() {
if (is_page()) {
global $title, $content_title;
switch ($content_title) {
case 'none': break;
case '': echo '<h1>'.$title.'</h1>'; break;
default: echo do_shortcode($content_title); } }
else {
global $post;
$content_title = do_shortcode(get_post_meta($post->ID, 'content_title', true));
if (empty($content_title)) { ?><h2><a href="<?php the_permalink(); ?>"><?php echo $post->post_title; ?></a></h2><?php }
elseif ($content_title != 'none') { echo do_shortcode($content_title); } } }


function current_permalink() {
global $post; return get_permalink(); }

add_shortcode('permalink', 'current_permalink');


function current_slug() {
global $post; return $post->post_name; }

add_shortcode('slug', 'current_slug');


function current_title() {
global $post; return $post->post_title; }

add_shortcode('title', 'current_title');


function description() {
if (is_home()) { $description = get_bloginfo('description'); }
else {
if (is_page()) { global $content; }
if (is_single()) { global $post; $content = format_content($post->post_content); }
$description = strip_tags($content);
$description = str_replace("\"", ' ', $description);
$description = str_replace(array("\t", "\n", "\r"), ' ', $description);
while (stristr($description, '  ')) { $description = str_replace('  ', ' ', $description); }
$description = substr(trim($description), 0, 155);
if (stristr($description, ' ')) { while (substr($description, -1) != ' ') { $description = substr($description, 0, -1); } }
$description = $description.'[…]'; }
return $description; }


function download_link() {
global $post;
$download_link = do_shortcode(get_post_meta($post->ID, 'download_link', true));
if (!empty($download_link)) { ?><img class="icon" src="/medias/images/posts/pdf.png" alt="PDF" /> <a rel="nofollow" href="/medias/texts/<?php the_time('Y'); ?>/<?php echo $post->post_name; ?>.pdf" title="<?php _e('Download this post', 'kleor'); ?>"><?php _e('PDF Version', 'kleor'); ?></a><?php } }


function footer_menu() {
global $footer_menu, $post, $section_slug, $section_title, $title;
switch ($footer_menu) {
case 'none': break;
case 'minimal': ?>
<p><a href="/<?php echo $section_slug; ?>/cgv"><?php _e('Terms And Conditions', 'kleor'); ?></a> | <a href="/<?php echo $section_slug; ?>/mentions-legales"><?php _e('Legal Informations', 'kleor'); ?></a></p>
<?php break;
case '': case 'first': ?>
<p><a<?php if ((!is_page()) && (!is_category('Produits'))) { echo ' class="current"'; } ?> href="/"><?php _e('Blog', 'kleor'); ?></a> | <a<?php if (is_category('Produits')) { echo ' class="current"'; } ?> href="/categorie/produits"><?php _e('Products', 'kleor'); ?></a> | <a<?php if (($title == 'Affiliation') || ($section_title == 'Affiliation')) { echo ' class="current"'; } ?> href="/affiliation"><?php _e('Affiliation', 'kleor'); ?></a> | <a<?php if (($title == 'FAQ') && ($section_title != 'Affiliation')) { echo ' class="current"'; } ?> href="/faq"><?php _e('FAQ', 'kleor'); ?></a> | <a<?php if ($title == 'Contact') { echo ' class="current"'; } ?> href="/contact"><?php _e('Contact', 'kleor'); ?></a><br />
<a<?php if ($title == 'Conditions Générales de Vente') { echo ' class="current"'; } ?> href="/cgv"><?php _e('Terms And Conditions', 'kleor'); ?></a> | <a<?php if ($title == 'Mentions Légales') { echo ' class="current"'; } ?> href="/mentions-legales"><?php _e('Legal Informations', 'kleor'); ?></a></p>
<?php break;
case 'second':
if ((function_exists('affiliation_session')) && (affiliation_session())) { ?>
<p><a href="/"><?php _e('Blog', 'kleor'); ?></a> | <a<?php if ($title == $section_title) { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>"><?php _e('Home', 'kleor'); ?></a> | <a<?php if ($title == 'Affiliation') { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>/affiliation"><?php _e('Affiliation', 'kleor'); ?></a> | <a<?php if ($title == 'FAQ') { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>/faq"><?php _e('FAQ', 'kleor'); ?></a> | <a<?php if ($title == 'Contact') { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>/contact"><?php _e('Contact', 'kleor'); ?></a><br />
<a<?php if ($title == 'Conditions Générales de Vente') { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>/cgv"><?php _e('Terms And Conditions', 'kleor'); ?></a> | <a<?php if ($title == 'Mentions Légales') { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>/mentions-legales"><?php _e('Legal Informations', 'kleor'); ?></a></p>
<?php } else { ?>
<p><a href="/"><?php _e('Blog', 'kleor'); ?></a> | <a<?php if ($title == $section_title) { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>"><?php _e('Home', 'kleor'); ?></a> | <a<?php if ($title == 'Commander') { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>/commander"><?php _e('Order', 'kleor'); ?></a> | <a<?php if ($title == 'FAQ') { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>/faq"><?php _e('FAQ', 'kleor'); ?></a> | <a<?php if ($title == 'Contact') { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>/contact"><?php _e('Contact', 'kleor'); ?></a><br />
<a<?php if ($title == 'Conditions Générales de Vente') { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>/cgv"><?php _e('Terms And Conditions', 'kleor'); ?></a> | <a<?php if ($title == 'Mentions Légales') { echo ' class="current"'; } ?> href="/<?php echo $section_slug; ?>/mentions-legales"><?php _e('Legal Informations', 'kleor'); ?></a></p>
<?php } break;
default: echo do_shortcode($footer_menu); } }


function footer_template() { ?>
</div>
<div id="wrapper-bottom"></div>
</div></div>
<div id="footer">
<?php footer_menu(); ?>

<p>Copyright © 2009-<?php echo date('Y'); ?> <a href="/">Kleor Editions</a> - <?php _e('All Rights Reserved', 'kleor'); ?><br />
<?php _e('Powered by', 'kleor'); ?> <a rel="shadowbox" href="http://wordpress.org">WordPress</a> | <a class="rss" href="/feed"><?php _e('RSS Feed', 'kleor'); ?></a></p>

<?php //w3c_icons(); ?>

</div>
<?php wp_footer(); ?>
<!-- <?php echo $_COOKIE['a']; ?> <?php echo get_num_queries(); ?> <?php _e('requests', 'kleor'); ?>. <?php echo timer_stop(0, 3); ?> <?php _e('seconds', 'kleor'); ?>. <?php echo (round(memory_get_peak_usage(true)/10000))/100; ?> Mo. -->
</body>
</html><?php }


function format_content($content) {
global $post;
$content = do_shortcode($content);
$content = str_replace('&#91;', '[', $content);
$content = str_replace('&#93;', ']', $content);
$content = str_replace('&#8217;', '\'', $content);
$content = convert_smilies($content);
$content = str_replace('Get the Flash Player', 'Téléchargez le Flash Player', $content);
$content = str_replace('to see the wordTube Media Player', 'pour voir le WordTube Media Player', $content);
if ((is_page()) || (is_single())) { $content = str_replace('<!--more-->', '<span id="more-'.$post->ID.'"></span>', $content); }
elseif (stristr($content, '<!--more-->')) {
$content = explode('<!--more-->', $content);
$content = $content[0].' <a href="'.get_permalink().'#more-'.$post->ID.'" class="more-link">'.__('Click here to read the rest of this entry »', 'kleor').'</a></p>'; }
return $content; }


function google_plus($atts) {
global $post;
add_action('wp_footer', 'google_plus_js');
extract(shortcode_atts(array('count' => 'false', 'size' => 'medium', 'url' => 'permalink'), $atts));
$count = strip_accents(strtolower($count));
if ($count != 'true') { $count = 'false'; }
$count = ' count="'.$count.'"';
$size = strip_accents(strtolower($size));
switch ($size) {
case 'medium': case 'small': case 'standard': case 'tall': break;
default: $size = 'medium'; }
$size = ' size="'.$size.'"';
$url = strip_accents(strtolower($url));
if ($url == 'permalink') {
if (in_category('Produits')) { $url = 'http://www.kleor-editions.com/'.$post->post_name.'/'; }
else { $url = get_permalink(); }
if ($_COOKIE[AFFILIATION_COOKIES_NAME] != '') { $url = $url.'?'.AFFILIATION_URL_VARIABLE_NAME.'='.$_COOKIE[AFFILIATION_COOKIES_NAME]; } }
if (!empty($url)) { $url = ' href="'.$url.'"'; }
return '<g:plusone'.$count.$size.$url.'></g:plusone>'; }

add_shortcode('google-plus', 'google_plus');


function google_plus_js() { ?>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: '<?php echo $_GET['lang']; ?>'}</script>
<?php }


function guarantee_length() {
return 30; }

add_shortcode('guarantee-length', 'guarantee_length');


function header_img() {
global $header_image, $section_slug;
switch ($header_image) {
case 'none': break;
case '': case 'first': ?>
<div id="header"><a style="background-image: url(/medias/images/header.png);" href="/"></a></div>
<?php break;
case 'second': ?>
<div id="header"><a style="background-image: url(/medias/images/<?php echo $section_slug; ?>/header.jpg);" href="/<?php echo $section_slug; ?>"></a></div>
<?php break;
default: echo do_shortcode($header_image); } }


function header_menu() {
global $header_menu, $post, $section_slug, $section_title, $title;
switch ($header_menu) {
case 'none': break;
case '': case 'first': ?>
<ul id="menu">
<li style="width: 120px;"<?php if ((!is_page()) && (!is_category('Produits'))) { echo ' class="current"'; } ?>><a href="/"><?php _e('Blog', 'kleor'); ?></a></li>
<li style="width: 150px;"<?php if (is_category('Produits')) { echo ' class="current"'; } ?>><a href="/categorie/produits"><?php _e('Products', 'kleor'); ?></a></li>
<li style="width: 180px;"<?php if (($title == 'Affiliation') || ($section_title == 'Affiliation')) { echo ' class="current"'; } ?>><a href="/affiliation"><?php _e('Affiliation', 'kleor'); ?></a></li>
<li style="width: 120px;"<?php if (($title == 'FAQ') && ($section_title != 'Affiliation')) { echo ' class="current"'; } ?>><a href="/faq"><?php _e('FAQ', 'kleor'); ?></a></li>
<li style="width: 150px;"<?php if ($title == 'Contact') { echo ' class="current"'; } ?>><a href="/contact"><?php _e('Contact', 'kleor'); ?></a></li>
</ul>
<?php break;
case 'second':
if ((function_exists('affiliation_session')) && (affiliation_session())) { ?>
<ul id="menu">
<li style="width: 120px;"><a href="/"><?php _e('Blog', 'kleor'); ?></a></li>
<li style="width: 150px;"><a href="/<?php echo $section_slug; ?>"><?php _e('Home', 'kleor'); ?></a></li>
<li style="width: 180px;"<?php if ($title == 'Affiliation') { echo ' class="current"'; } ?>><a href="/<?php echo $section_slug; ?>/affiliation"><?php _e('Affiliation', 'kleor'); ?></a></li>
<li style="width: 120px;"<?php if ($title == 'FAQ') { echo ' class="current"'; } ?>><a href="/<?php echo $section_slug; ?>/faq"><?php _e('FAQ', 'kleor'); ?></a></li>
<li style="width: 150px;"<?php if ($title == 'Contact') { echo ' class="current"'; } ?>><a href="/<?php echo $section_slug; ?>/contact"><?php _e('Contact', 'kleor'); ?></a></li>
</ul>
<?php } else { ?>
<ul id="menu">
<li style="width: 120px;"><a href="/"><?php _e('Blog', 'kleor'); ?></a></li>
<li style="width: 150px;"><a href="/<?php echo $section_slug; ?>"><?php _e('Home', 'kleor'); ?></a></li>
<li style="width: 180px;"<?php if ($title == 'Commander') { echo ' class="current"'; } ?>><a href="/<?php echo $section_slug; ?>/commander"><?php _e('Order', 'kleor'); ?></a></li>
<li style="width: 120px;"<?php if ($title == 'FAQ') { echo ' class="current"'; } ?>><a href="/<?php echo $section_slug; ?>/faq"><?php _e('FAQ', 'kleor'); ?></a></li>
<li style="width: 150px;"<?php if ($title == 'Contact') { echo ' class="current"'; } ?>><a href="/<?php echo $section_slug; ?>/contact"><?php _e('Contact', 'kleor'); ?></a></li>
</ul>
<?php } break;
default: echo do_shortcode($header_menu); } }


function header_style() {
if ((is_page()) && ((stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox/4')) || (stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox/5')) || (stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox/6')) || (stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox/7')) || (stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox/8')) || (stristr($_SERVER['HTTP_USER_AGENT'], 'Firefox/9')))) { ?>
<style type="text/css">
#wrapper, #wrapper-bottom, #wrapper-top { background-image: none; }
#wrapper-bottom, #wrapper-top { height: 0; }
#wrapper3 {
  border: 20px solid #ffffff;
  border-radius: 0 0 20px 20px;
  -moz-border-bottom-colors: #d9d9d9 #dbdbdb #dddddd #dfdfdf #e1e1e1 #e3e3e3 #e5e5e5 #e7e7e7 #e9e9e9 #ebebeb #ededed #efefef #f1f1f1 #f3f3f3 #f5f5f5 #f7f7f7 #f9f9f9 #fbfbfb #fdfdfd #ffffff;
  -moz-border-left-colors: #d9d9d9 #dbdbdb #dddddd #dfdfdf #e1e1e1 #e3e3e3 #e5e5e5 #e7e7e7 #e9e9e9 #ebebeb #ededed #efefef #f1f1f1 #f3f3f3 #f5f5f5 #f7f7f7 #f9f9f9 #fbfbfb #fdfdfd #ffffff;
  -moz-border-right-colors: #d9d9d9 #dbdbdb #dddddd #dfdfdf #e1e1e1 #e3e3e3 #e5e5e5 #e7e7e7 #e9e9e9 #ebebeb #ededed #efefef #f1f1f1 #f3f3f3 #f5f5f5 #f7f7f7 #f9f9f9 #fbfbfb #fdfdfd #ffffff;
  -moz-border-top-colors: #d9d9d9 #dbdbdb #dddddd #dfdfdf #e1e1e1 #e3e3e3 #e5e5e5 #e7e7e7 #e9e9e9 #ebebeb #ededed #efefef #f1f1f1 #f3f3f3 #f5f5f5 #f7f7f7 #f9f9f9 #fbfbfb #fdfdfd #ffffff;
}
#page-wrapper { margin: 0 20px; }
</style>
<?php } }


function header_template() { ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<title><?php html_title(); ?></title>
<meta charset="UTF-8" />
<link rel="icon" type="image/png" href="/medias/images/favicon.png" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('stylesheet_url'); ?>" />
<link rel="alternate" type="application/rss+xml" title="<?php _e('RSS feed of Kleor Editions', 'kleor'); ?>" href="http://www.kleor-editions.com/feed/" />
<link rel="alternate" type="application/atom+xml" title="<?php _e('Atom feed of Kleor Editions', 'kleor'); ?>" href="http://www.kleor-editions.com/feed/atom/" />
<link rel="pingback" href="http://www.kleor-editions.com/xmlrpc.php" />
<?php html_header(); wp_head(); header_style(); ?>
</head>
<body>
<div id="wrapper"><div id="wrapper2">
<?php header_img(); header_menu(); ?>
<div id="wrapper-top"></div>
<div id="wrapper3">
<?php }


function html_header() {
if (is_page()) {
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
global $content_id; switch ($content_id) {
case 5: case 6: case 7: case 9: case 43: $meta_robots = '<meta name="robots" content="noindex" />'."\n"; }
global $title; switch ($title) {
case 'Mentions Légales': case 'Information de Validation': case 'Confirmation de Validation': case 'Confirmation de Commande': case 'Confirmation d\'Inscription': $meta_robots = '<meta name="robots" content="noindex" />'."\n"; }
$canonical_url = get_permalink($content_id);
echo $meta_robots.'<link rel="canonical" href="'.$canonical_url.'" />
<meta name="description" content="'.description().'" />'."\n"; }
else {
if (isset($_GET['s'])) { echo '<meta name="robots" content="noindex" />'."\n"; }
if (class_exists('wordTube_shortcodes')) { add_action('wp_head', 'advanced_media_js'); }
if (is_home() || is_single()) { echo '<meta name="description" content="'.description().'" />'."\n"; } } }


function html_title() {
if (is_page()) {
global $html_title, $section_title, $title;
if (empty($html_title)) {
if ($title == $section_title) { $html_title = $title.' | Kleor Editions'; }
else { $html_title = $section_title.' ('.$title.') | Kleor Editions'; } }
echo $html_title; }
else {
if (is_home()) { ?>Kleor Editions<?php }
elseif (is_single()) { single_post_title(); ?> <?php _e('(Post)', 'kleor'); ?> | Kleor Editions<?php }
elseif ((is_category('Concours')) || (is_category('Offres Promotionnelles')) || (is_category('Produits')) || (is_category('Cadeaux'))) { ?><?php single_cat_title(); ?> | Kleor Editions<?php }
elseif (is_category()) { _e('Posts of the category', 'kleor'); ?> <?php single_cat_title(); ?> | Kleor Editions<?php }
elseif (is_tag()) { _e('Posts with the tag', 'kleor'); ?> <?php single_tag_title(); ?> | Kleor Editions<?php }
elseif (is_search()) { _e('Search results', 'kleor'); ?> | Kleor Editions<?php }
elseif (is_day()) { _e('Posts of ', 'kleor'); ?> <?php echo get_the_time('j F Y'); ?> | Kleor Editions<?php }
elseif (is_month()) { _e('Posts of', 'kleor'); ?> <?php echo get_the_time('F Y'); ?> | Kleor Editions<?php }
elseif (is_year()) { _e('Posts of', 'kleor'); ?> <?php echo get_the_time('Y'); ?> | Kleor Editions<?php }
elseif (is_author()) { $userdata = get_userdatabylogin(get_query_var('author_name')); _e('Posts of', 'kleor'); ?> <?php echo $userdata->display_name; ?> | Kleor Editions<?php }
elseif (is_404()) { _e('Error 404', 'kleor'); ?> | Kleor Editions<?php }
else { wp_title(); } } }


function language_selector() {
global $post;
$language_selector = do_shortcode(get_post_meta($post->ID, 'language_selector', true));
if (!empty($language_selector)) {
if ($_GET['lang'] == 'en') { $alt = 'Version Française'; $src = '/medias/images/pages/fr.png'; }
else { $alt = 'English Version'; $src = '/medias/images/pages/en.png'; }
echo '
<div class="aligncenter">
<img class="flag-icon" src="'.$src.'" alt="'.$alt.'" /> '.language_link().'
</div>'; } }


function language_link() {
if ((stristr($_SERVER['REQUEST_URI'], '/en/')) || (stristr($_SERVER['REQUEST_URI'], '/fr/'))) {
if ($_GET['lang'] == 'en') { $url = '../fr/'; $text = 'Version Française'; }
else { $url = '../en/'; $text = 'English Version'; } }
else {
if ($_GET['lang'] == 'en') { $url = '?lang=fr'; $text = 'Version Française'; }
else { $url = '?lang=en'; $text = 'English Version'; } }
return '<a href="'.$url.'">'.$text.'</a>'; }

add_shortcode('language-link', 'language_link');


function membership_menu() {
if (function_exists('membership_session')) { if (membership_session('')) {
return '<div class="aligncenter"><a href="/espace-membre/accueil">'.__('Home', 'kleor').'</a> | <a href="/espace-membre/votre-profil">'.__('Profile', 'kleor').'</a> | <a href="/espace-membre/deconnexion">'.__('Log out ', 'kleor').'</a></div>'; } } }

add_shortcode('membership-menu', 'membership_menu');


function php_instructions($atts, $content) {
$content = str_replace('<? ', '<?php ', trim($content));
if (substr($content, 0, 5) == '<?php') { $content = substr($content, 5); }
if (substr($content, -2) == '?>') { $content = substr($content, 0, -2); }
eval(do_shortcode(trim($content))); }

add_shortcode('php', 'php_instructions');


function post_template() {
global $post; content_title(); ?>

<div class="post-header-icons">
<?php download_link(); if (current_user_can('edit_post', $post->ID)) { if ($post->post_status != 'publish') { _e(' | Private or not published', 'kleor'); } ?> | <?php edit_post_link('Edit'); } ?>
</div>

<?php if (is_single()) { echo share_buttons(array()); }
echo format_content($post->post_content);
echo share_buttons(array()); }


function purchase_block($atts) {
return '
<div class="aligncenter">
<p>'.purchase_page_link($atts).'</p>
'.purchase_button($atts).'
</div>'; }

add_shortcode('purchase-block', 'purchase_block');


function purchase_form() {
return do_shortcode('
<table style="width: 75%;">
<tr><th>'.__('Product', 'kleor').'</th><th>'.__('Price', 'kleor').'</th></tr>
<tr><td>[product name]</td><td>[product price] EUR</td></tr>
</table>
<p style="float: left; text-align: center; width: 24em;"><strong>'.__('You can pay by credit card or PayPal account.', 'kleor').'</strong></p>
<div style="float: right;">[purchase-button src=/medias/images/pages/paypal-checkout.png]</div>
<div style="clear: both;"></div>'); }

add_shortcode('purchase-form', 'purchase_form');


function purchase_image($atts) {
global $section_slug, $section_title;
extract(shortcode_atts(array('size' => 0), $atts));
switch ($size) { case 1: case 2: case 3: case 4: case 5: case 6: break; default: $size = 4; }
return '<a href="/'.$section_slug.'/commander"><img class="size'.$size.'" src="/medias/images/pages/download.png" alt="'.__('Download', 'kleor').'" /></a>'; }

add_shortcode('purchase-image', 'purchase_image');


function purchase_page_link($atts) {
global $section_slug, $section_title;
extract(shortcode_atts(array('text' => __('Ok, I order now', 'kleor').' <em>'.$section_title.'</em>'), $atts));
return '<a href="/'.$section_slug.'/commander">'.$text.'</a>'; }

add_shortcode('purchase-page-link', 'purchase_page_link');


function random_posts() {
return array(17, 18, 19, 20, 21, 22, 28, 58, 68, 108, 109, 110, 112, 118, 180, 181, 220, 221, 222, 223, 224, 225, 254, 255, 262, 273); }


function remove_pages_from_search_results() {
global $wp_query; if (is_search()) { $wp_query->query_vars['post_type'] = 'post'; } }

add_action('pre_get_posts', 'remove_pages_from_search_results');


function search_form() {
$button = do_shortcode('[button text='.__('Search', 'kleor').']');
return '
<form method="get" action="/">
<div class="aligncenter">
<input class="search-form" type="text" value="" name="s" />'
.$button.
'</div>
</form>'; }

add_shortcode('search-form', 'search_form');


function sale_page_link_content($atts, $content) {
global $sale_page_link;
$content = explode('[other]', do_shortcode($content));
if (empty($sale_page_link)) { $n = 0; } else { $n = 1; }
return $content[$n]; }

add_shortcode('sale-page-link-content', 'sale_page_link_content');


function section_id() {
global $post;
$section_id = $post->ID; $parent = $post->post_parent;
while ($parent != 0) { $section_id = $parent; $parent = get_post($section_id)->post_parent; }
return $section_id; }


function section_image($atts) {
global $section_title, $section_slug;
extract(shortcode_atts(array('size' => 0), $atts));
switch ($size) { case 1: case 2: case 3: case 4: case 5: case 6: break; default: $size = 2; }
if ($size < 5) { $ext = 'jpg'; } else { $ext = 'png'; }
return '<img src="/medias/images/'.$section_slug.'/'.$size.'.'.$ext.'" alt="'.$section_title.'" />'; }

add_shortcode('cover', 'section_image');
add_shortcode('image', 'section_image');
add_shortcode('section-image', 'section_image');


function section_slug() {
global $section_slug; return $section_slug; }

add_shortcode('section-slug', 'section_slug');


function section_title() {
global $section_title;
if ($section_title == 'Confirmation de Validation') { $section_title = __('this product', 'kleor'); }
return $section_title; }

add_shortcode('section-title', 'section_title');


function shadowbox($atts) {
add_action('wp_footer', 'shadowbox_js');
extract(shortcode_atts(array('gallery' => ''), $atts));
if ($gallery != '') { $gallery = '['.$gallery.']'; }
return 'shadowbox'.$gallery; }

add_shortcode('shadowbox', 'shadowbox');


function shadowbox_js() { ?>
<script type="text/javascript" src="/wp-content/plugins/shadowbox/shadowbox.js"></script>
<script type="text/javascript">Shadowbox.init({counterType: 'skip', handleOversize: 'drag'});</script>
<?php }


function share_buttons($atts) {
return '<div class="aligncenter" style="margin: 1.5em 0 0 0;">
<div style="display: inline; margin: 0 10px;">'.tweet_button($atts).'</div>
<div style="display: inline; margin: 0 10px;">'.google_plus($atts).'</div>
</div>'; }

add_shortcode('share-buttons', 'share_buttons');


function sidebar_template() { ?>
<div id="sidebar">
<?php dynamic_sidebar(); ?>
</div>
<?php }


function signature($atts) {
extract(shortcode_atts(array('size' => 0), $atts));
switch ($size) { case 1: case 2: case 3: case 4: case 5: case 6: break; default: $size = 4; }
return '<img class="size'.$size.'" src="/medias/images/pages/signature.png" alt="'.__('Signature', 'kleor').'" />'; }

add_shortcode('signature', 'signature');


function squeeze_page_link_content($atts, $content) {
global $squeeze_page_link;
$content = explode('[other]', do_shortcode($content));
if (!empty($squeeze_page_link)) { $n = 0; } else { $n = 1; }
return $content[$n]; }

add_shortcode('squeeze-page-link-content', 'squeeze_page_link_content');


function thumbnail($atts) {
extract(shortcode_atts(array('img' => '', 'src' => '/medias/images/posts/thumbnails/kleor.png'), $atts));
$img = strip_accents(strtolower($img)); switch ($img) {
case 'firefox': $src = '/medias/images/posts/thumbnails/firefox.png'; break;
case 'guarantee': $src = '/medias/images/pages/guarantee.png'; break;
case 'kleor': $src = '/medias/images/posts/thumbnails/kleor.png'; break;
case 'pdf': $src = '/medias/images/posts/thumbnails/pdf.png'; break;
case 'wordpress': $src = '/medias/images/posts/thumbnails/wordpress.png'; break;
default: $src = strip_accents(strtolower($src)); }
return '<div class="thumbnail" style="background-image: url('.$src.');"></div>'; }

add_shortcode('thumbnail', 'thumbnail');


function tweet_button($atts) {
global $post;
add_action('wp_footer', 'tweet_button_js');
extract(shortcode_atts(array('count' => 'false', 'url' => 'permalink'), $atts));
$count = strip_accents(strtolower($count));
if ($count == 'false') { $count = 'none'; }
$count = ' data-count="'.$count.'"';
$url = strip_accents(strtolower($url));
if ($url == 'permalink') {
if (in_category('Produits')) { $url = 'http://www.kleor-editions.com/'.$post->post_name.'/'; }
else { $url = get_permalink(); }
if ($_COOKIE[AFFILIATION_COOKIES_NAME] != '') { $url = $url.'?'.AFFILIATION_URL_VARIABLE_NAME.'='.$_COOKIE[AFFILIATION_COOKIES_NAME]; } }
if (!empty($url)) { $url = ' data-url="'.$url.'"'; }
return '<a href="https://twitter.com/share" class="twitter-share-button"'.$url.$count.' data-lang="en"></a>'; }

add_shortcode('tweet-button', 'tweet_button');


function tweet_button_js() { ?>
<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
<?php }


function w3c_icons() {
global $post;
$optimizer = do_shortcode(get_post_meta($post->ID, 'optimizer', true));
if ($optimizer == '') { ?>
<p class="w3c-icons"><a rel="shadowbox" href="http://validator.w3.org/check?uri=referer"><img src="/medias/images/valid-html.png" alt="<?php _e('Valid HTML', 'kleor'); ?>" /></a><?php //echo '<a rel="shadowbox" href="http://jigsaw.w3.org/css-validator/check/referer"><img src="/medias/images/valid-css.png" alt="<?php _e('Valid CSS', 'kleor'); ?>" /></a>'; ?></p><?php } }


add_filter('widget_text', 'do_shortcode');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'wp_print_scripts');
remove_action('wp_head', 'wp_print_head_scripts', 9);
remove_action('wp_head', 'wp_enqueue_scripts');
remove_action('wp_print_styles', 'wpcf7_enqueue_styles');