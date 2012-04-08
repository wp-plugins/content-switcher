<?php global $wpdb;
$title = $post->post_title;
$section_id = section_id();
$section_title = get_post($section_id)->post_title;
$section_slug = get_post($section_id)->post_name;
additional_instructions();
$html_title = do_shortcode(get_post_meta($post->ID, 'html_title', true));
$header_image = do_shortcode(get_post_meta($post->ID, 'header_image', true));
$header_menu = do_shortcode(get_post_meta($post->ID, 'header_menu', true));
$footer_menu = do_shortcode(get_post_meta($post->ID, 'footer_menu', true));
$product_id = do_shortcode(get_post_meta($post->ID, 'product_id', true));
if ($section_id != $post->ID) {
if (empty($header_image)) { $header_image = do_shortcode(get_post_meta($section_id, 'header_image', true)); }
if (empty($header_menu)) { $header_menu = do_shortcode(get_post_meta($section_id, 'header_menu', true)); }
if (empty($footer_menu)) { $footer_menu = do_shortcode(get_post_meta($section_id, 'footer_menu', true)); }
if (empty($product_id)) { $product_id = do_shortcode(get_post_meta($section_id, 'product_id', true)); } }
$product_id = (int) $product_id; if ($product_id > 0) { $_GET['product_id'] = $product_id; }
$content_id = do_shortcode(get_post_meta($post->ID, 'content_id', true));
if (empty($content_id)) { $content_id = $post->ID; $content = $post->post_content; }
else { $content = get_post($content_id)->post_content; }
if (($content_id == 1) && (function_exists('affiliation_session'))) {
if (affiliation_session()) {
$sale_page_link = do_shortcode(get_post_meta($post->ID, 'sale_page_link', true));
$squeeze_page_link = do_shortcode(get_post_meta($post->ID, 'squeeze_page_link', true)); } }
$content = format_content($content);
$content_title = do_shortcode(get_post_meta($content_id, 'content_title', true));
header_template(); ?>

<div id="page-wrapper">
<?php language_selector(); ?>
<?php content_title(); ?>

<?php echo $content; ?>

<?php if (current_user_can('edit_post', $post->ID)) { ?>
<div class="aligncenter">
<p><?php edit_post_link(__('Edit', 'kleor')); ?></p>
<?php if (($content_id != $post->ID) && (current_user_can('edit_post', $content_id))) { ?>
<p><?php edit_post_link(__('Edit the original page', 'kleor'), '', '', $content_id); ?></p><?php } ?>
</div>
<?php } ?>

<?php if (comments_open()) { comments_template(); } ?>
</div>

<?php footer_template();