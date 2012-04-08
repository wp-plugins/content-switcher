<?php if ((!empty($_SERVER['SCRIPT_FILENAME'])) && ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))) { die ('<a href="/">Kleor Editions</a>'); }
if (post_password_required()) { ?>
<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'kleor'); ?></p><?php } ?>
<?php if (have_comments()) : ?>
<h3 id="comments"><?php comments_number(__('No responses', 'kleor'), __('1 response', 'kleor'), __('% responses', 'kleor'));?></h3>
<?php ob_start();
previous_comments_link(__('&laquo; Older comments', 'kleor'));
$prev_comment_link = ob_get_clean(); ob_start();
next_comments_link(__('Newer comments &raquo;', 'kleor'));
$next_comment_link = ob_get_clean(); ?>
<?php if ($prev_comment_link || $next_comment_link): ?>
<div class="navigation">
<div style="float: left;"><?php echo $prev_comment_link; ?></div>
<div style="float: right;"><?php echo $next_comment_link; ?></div>
<div style="clear: both;"></div>
</div>
<?php endif; ?>
<ul class="commentlist">
<?php wp_list_comments('callback=art_comment'); ?>
</ul>
<?php if ($prev_comment_link || $next_comment_link): ?>
<div class="navigation">
<div style="float: left;"><?php echo $prev_comment_link; ?></div>
<div style="float: right;"><?php echo $next_comment_link; ?></div>
<div style="clear: both;"></div>
</div>
<?php endif; ?>
<?php else: ?>
<?php endif; ?>
<?php if (comments_open()) : ?>
<?php if (have_comments()) { ?><p class="aligncenter"><a class="rss" href="<?php the_permalink(); ?>feed"><?php _e('RSS feed of comments', 'kleor'); ?></a></p><?php } ?>
<div id="respond">
<h3><?php comment_form_title(__('Leave a reply', 'kleor'), __('Leave a reply for %s', 'kleor')); ?></h3>
<div class="cancel-comment-reply">
<?php cancel_comment_reply_link(); ?>
</div>
<?php if ((get_option('comment_registration')) && (!$user_ID)) : ?>
<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', 'kleor'), 'http://www.kleor-editions.com/wp-login.php?redirect_to='.urlencode(get_permalink())); ?></p>
<?php else: ?>
<?php include TEMPLATEPATH.'/comment-faq.php'; ?>
<?php if ($user_ID) : ?>
<form action="/wp-comments-post.php" method="post" id="commentform">
<p><?php printf(__('Logged in as <a href="%1$s">%2$s</a>.', 'kleor'), 'http://www.kleor-editions.com/wp-admin/profile.php', $user_identity); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out', 'kleor'); ?>"><?php _e('Log out &raquo;', 'kleor'); ?></a></p>
<?php else: ?>
<?php add_action('wp_footer', 'strip_accents_js'); add_action('wp_footer', 'format_email_address_js'); ?>
<form action="/wp-comments-post.php" method="post" id="commentform" onsubmit="return validate_comment_form(this);">
<p><label><?php _e('Your name', 'kleor'); if ($req) { _e('(required)', 'kleor'); } ?>:<br />
<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="36" /></label>
<span class="error" id="comment-author-error"></span></p>
<p><label><?php _e('Your email address (will not be published)', 'kleor'); if ($req) { _e('(required)', 'kleor'); } ?>:<br />
<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="60" /></label>
<span class="error" id="comment-email-error"></span></p>
<p><label><?php _e('Your website (optional)', 'kleor'); ?><br />
<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="60" /></label></p>
<?php endif; ?>
<p><textarea name="comment" id="comment" cols="60" rows="10"></textarea>
<span class="error" id="comment-comment-error"></span></p>
<?php if (function_exists('live_preview')) { ?>
<p class="aligncenter" style="margin:1.5em 0;" id="comment-preview-display"><a href="#comment-preview-undisplay" onclick="document.getElementById('comment-preview-display').style.display = 'none'; document.getElementById('comment-preview-undisplay').style.display = 'block'; document.getElementById('commentPreview').style.display = 'block';"><?php _e('Preview', 'kleor'); ?></a></p>
<p class="aligncenter" id="comment-preview-undisplay"><a href="#comment-preview-display" onclick="document.getElementById('comment-preview-display').style.display = 'block'; document.getElementById('comment-preview-undisplay').style.display = 'none'; document.getElementById('commentPreview').style.display = 'none';"><?php _e('Hide preview', 'kleor'); ?></a></p><?php live_preview(); } ?>
<div><?php comment_id_fields(); ?></div>
<div><?php include TEMPLATEPATH.'/comments-js.php'; do_action('comment_form', $post->ID); ?></div>
<div class="aligncenter">
<?php echo do_shortcode('[button name=submit text="'.__('Post your comment', 'kleor').'"]'); ?>
</div>
</form>
<?php endif; ?>
</div>
<?php endif;