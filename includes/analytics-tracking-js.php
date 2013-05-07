<?php $analytics_tracking = false;
if (current_user_can('manage_options')) { if (content_switcher_data('administrator_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('edit_pages')) { if (content_switcher_data('editor_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('publish_posts')) { if (content_switcher_data('author_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('edit_posts')) { if (content_switcher_data('contributor_tracked') == 'yes') { $analytics_tracking = true; } }
elseif (current_user_can('read')) { if (content_switcher_data('subscriber_tracked') == 'yes') { $analytics_tracking = true; } }
else { if (content_switcher_data('visitor_tracked') == 'yes') { $analytics_tracking = true; } }
if ($analytics_tracking) { ?>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?php echo content_switcher_data('analytics_tracking_id'); ?>']);
_gaq.push(['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<?php }