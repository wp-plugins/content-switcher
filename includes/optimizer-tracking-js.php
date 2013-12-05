<?php global $post;
if ((isset($post)) && (is_object($post))) {
$optimizer = do_shortcode(get_post_meta($post->ID, 'optimizer', true));
if (substr($optimizer, 0, 1) != '/') { $optimizer = '/'.$optimizer; }
$type = substr($optimizer, -4);
if (($type == 'test') || ($type == 'goal')) { ?>
<script type="text/javascript">
if(typeof(_gat)!='object')document.write('<sc'+'ript src="http'+
(document.location.protocol=='https:'?'s://ssl':'://www')+
'.google-analytics.com/ga.js"></sc'+'ript>');
try {
var gwoTracker=_gat._getTracker("<?php echo content_switcher_data('optimizer_tracking_id'); ?>");
gwoTracker._trackPageview("<?php echo $optimizer; ?>");
}catch(err){}
</script>
<?php } }