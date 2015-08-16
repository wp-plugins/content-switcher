<?php global $post;
if ((isset($post)) && (is_object($post)) && (isset($post->ID))) {
$optimizer = kleor_do_shortcode(get_post_meta($post->ID, 'optimizer', true));
if (substr($optimizer, 0, 1) != '/') { $optimizer = '/'.$optimizer; }
$optimizer = explode('/', $optimizer);
if ((isset($optimizer[2])) && ($optimizer[2] == 'test')) { ?>
<script type="text/javascript">
function utmx_section(){}function utmx(){}
(function(){var k='<?php echo $optimizer[1]; ?>',d=document,l=d.location,c=d.cookie;function f(n){
if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);return escape(c.substring(i+n.
length+1,j<0?c.length:j))}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;
d.write('<sc'+'ript src="'+
'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'
+'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='
+new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
'" type="text/javascript" charset="utf-8"></sc'+'ript>')})();
</script>
<?php } }