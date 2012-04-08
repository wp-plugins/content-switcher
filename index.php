<?php additional_instructions();
header_template(); ?>

<div id="post-wrapper">
<?php $post = $posts[0]; $n = $wp_query->found_posts;
if ((is_category()) && (!is_category('Concours')) && (!is_category('Offres Promotionnelles')) && (!is_category('Produits')) && (!is_category('Cadeaux'))) { ?><h1><?php _e('Post', 'kleor'); if ($n != 1) { echo 's'; } ?> <?php _e('of the category', 'kleor'); ?> <em><?php single_cat_title(); ?></em> (<?php echo $n; _e('result', 'kleor'); if ($n > 1) { echo 's'; } ?>)</h1><?php }
elseif (is_tag()) { ?><h1><?php _e('Post', 'kleor'); if ($n != 1) { echo 's'; } ?> <?php _e('with the tag', 'kleor'); ?> <em><?php single_tag_title(); ?></em> (<?php echo $n; _e('result', 'kleor'); if ($n > 1) { echo 's'; } ?>)</h1><?php }
elseif (is_search()) { ?><h1><?php echo $n; _e('result', 'kleor'); if ($n > 1) { echo 's'; } ?> <?php _e('for', 'kleor'); ?> <em><?php echo strip_tags($_GET['s']); ?></em></h1><?php }
elseif (is_day()) { ?><h1><?php _e('Post', 'kleor'); if ($n != 1) { echo 's'; } ?> <?php _e('of ', 'kleor'); ?> <?php the_time('j F Y'); ?> (<?php echo $n; _e('result', 'kleor'); if ($n > 1) { echo 's'; } ?>)</h1><?php }
elseif (is_month()) { ?><h1><?php _e('Post', 'kleor'); if ($n != 1) { echo 's'; } ?> <?php _e('of', 'kleor'); ?> <?php the_time('F Y'); ?> (<?php echo $n; _e('result', 'kleor'); if ($n > 1) { echo 's'; } ?>)</h1><?php }
elseif (is_year()) { ?><h1><?php _e('Post', 'kleor'); if ($n != 1) { echo 's'; } ?> <?php _e('of', 'kleor'); ?> <?php the_time('Y'); ?> (<?php echo $n; _e('result', 'kleor'); if ($n > 1) { echo 's'; } ?>)</h1><?php }
elseif (is_author()) { $userdata = get_userdatabylogin(get_query_var('author_name')); ?>
<h1><?php _e('Post', 'kleor'); if ($n != 1) { echo 's'; } ?> de <?php echo $userdata->display_name; ?> (<?php echo $n; _e('result', 'kleor'); if ($n > 1) { echo 's'; } ?>)</h1><?php } ?>

<?php $prev_link = get_previous_posts_link(__('Newer entries &raquo;', 'kleor'));
$next_link = get_next_posts_link(__('&laquo; Older entries', 'kleor')); ?>
<?php if ((is_home()) && ($prev_link == '')) {
$sticky = get_option('sticky_posts'); if (count($sticky) == 0) {
query_posts(array('post__in' => random_posts(), 'orderby' => 'rand', 'posts_per_page' => 1));
if (have_posts()) { the_post(); post_template(); $random_post = $post->ID; }
query_posts(array('post__not_in' => array($random_post), 'orderby' => 'date')); } } ?>

<?php if (have_posts()) : while (have_posts()) { the_post(); post_template(); comments_template(); } ?>
<?php if ($prev_link || $next_link) : ?>
<div class="navigation">
<div class="alignleft"><?php echo $next_link; ?></div>
<div class="alignright"><?php echo $prev_link; ?></div>
</div>
<?php endif; ?>
<?php else : ?>
<?php if (is_404()) { ?>
<div class="block">
<div class="block-header"><?php _e('Error 404', 'kleor'); ?><div class="l"></div><div class="r"><div></div></div></div>
<div class="block-content">
<h2 class="aligncenter"><?php _e('Not found', 'kleor'); ?></h2>
<?php echo search_form(); ?>
</div>
</div><?php }
else { ?><h2><?php _e('No entries found. Perhaps with a different search?', 'kleor'); ?></h2>
<?php echo search_form(); } ?>
<?php endif; ?>
</div>

<?php sidebar_template(); ?>

<?php footer_template();