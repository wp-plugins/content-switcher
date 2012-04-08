<script type="text/javascript">
var comment_toolbar_buttons_array = new Array(
     new comment_toolbar_button("<strong>b</strong>", "insert_html_tags(document.getElementById('comment'), 'strong', '', '', '')", "<?php _e('Bold text', 'kleor'); ?>"),
     new comment_toolbar_button("<em>i</em>", "insert_html_tags(document.getElementById('comment'), 'em', '', '', '')", "<?php _e('Italic text', 'kleor'); ?>"),
     new comment_toolbar_button("<del>del</del>", "insert_html_tags(document.getElementById('comment'), 'del', '', '', '')", "<?php _e('Strikethrough text', 'kleor'); ?>"),
	 new comment_toolbar_button("<ins>ins</ins>", "insert_html_tags(document.getElementById('comment'), 'ins', '', '', '')", "<?php _e('Underlined text', 'kleor'); ?>"),
     new comment_toolbar_button("url", "insert_html_tags(document.getElementById('comment'), 'a', {href: ''}, {href: '<?php _e('URL of the page:', 'kleor'); ?>'}, '', '')", "<?php _e('Hyperlink', 'kleor'); ?>"),
     new comment_toolbar_button("citation", "insert_html_tags(document.getElementById('comment'), 'blockquote', '', '', '')", "<?php _e('Quote', 'kleor'); ?>"),
	 new comment_toolbar_button("pre", "insert_html_tags(document.getElementById('comment'), 'pre', '', '', '')", "<?php _e('Preformatted paragraph', 'kleor'); ?>"),
	 new comment_toolbar_button("code", "insert_html_tags(document.getElementById('comment'), 'code', '', '', '')", "<?php _e('Code', 'kleor'); ?>"),
	 new comment_toolbar_button("ul", "insert_html_tags(document.getElementById('comment'), 'ul', '', '', '')", "<?php _e('Unordered list', 'kleor'); ?>"),
     new comment_toolbar_button("ol", "insert_html_tags(document.getElementById('comment'), 'ol', '', '', '')", "<?php _e('Ordered list', 'kleor'); ?>"),
	 new comment_toolbar_button("li", "insert_html_tags(document.getElementById('comment'), 'li', '', '', '')", "<?php _e('List item', 'kleor'); ?>"),
     new comment_toolbar_smilies('<img style="width: 15px; height: 15px;" src="/wp-includes/images/smilies/icon_smile.gif" />', '<?php _e('Smilies', 'kleor'); ?>')
  );


function comment_toolbar_button(butt_text, hand_onclick, title) {
     this.button = '<a href="#" onclick="'+hand_onclick+'; return false" title="'+title+'">'+butt_text+'</a>'; }


function comment_toolbar_smilies(butt_text, title) {
  var smilies = {
     ':mrgreen:':'icon_mrgreen.gif',
     ':twisted:':'icon_twisted.gif',
     ':arrow:':'icon_arrow.gif',
     ':shock:':'icon_eek.gif',
     ':smile:':'icon_smile.gif',
     ':evil:':'icon_evil.gif',
     ':idea:':'icon_idea.gif',
     ':oops:':'icon_redface.gif',
     ':roll:':'icon_rolleyes.gif',
     ':cry:':'icon_cry.gif',
     ':lol:':'icon_lol.gif',
     ':-(':'icon_sad.gif',
     ':-)':'icon_smile.gif',
     ':-?':'icon_confused.gif',
     ':-D':'icon_biggrin.gif',
     ':-P':'icon_razz.gif',
     ':-o':'icon_surprised.gif',
     ':-x':'icon_mad.gif',
     ':-|':'icon_neutral.gif',
     ';-)':'icon_wink.gif',
     '8)':'icon_cool.gif',
     ':x':'icon_mad.gif',
     ':|':'icon_neutral.gif',
     ';)':'icon_wink.gif',
     ':!:':'icon_exclaim.gif',
     ':?:':'icon_question.gif'
  };
  this.button = '<a href="#" class="comment-toolbar-drop-menu-button" onclick="return false" onmouseover="comment_toolbar_list(this.nextSibling, true)" onmouseout="comment_toolbar_list(this.nextSibling, false)"  title="'+title+'">'+butt_text+'</a><ul onmouseover="comment_toolbar_list(this, true)" onmouseout="comment_toolbar_list(this, false)" style="display: none;" class="drop-menu"><li>';
  var i = 0;
  for (var key in smilies) {
	  this.button += '<img style="margin: 2px; width: 15px; height: 15px; cursor: pointer;" src="/wp-includes/images/smilies/'+smilies[key]+'" onclick="insert_html_tags(document.getElementById(\'comment\'), \'smile_'+key+'\')" title="'+key+'" />';
      i++;
	  if (i == 6) {
		  this.button += '<br />'
		  i = 0;
	  }
  }
  this.button += '</li><ul>'
}
function comment_toolbar_list(listObj, mode) {
	 if (mode) {
	     listObj.style.display = 'block';
	 }
	 else if (!mode) {
	     listObj.style.display = 'none';
	 } 
}
function createList(tagName,strAttr, text, lt, gt) {
   var str = '';
   var spl = '\n'
   if (navigator.appName == 'Microsoft Internet Explorer' || navigator.appName == 'Opera')
	   spl = '\r\n';
   this.openTag = spl+lt+tagName+strAttr+gt;
   this.closeTag = lt+'/'+tagName+gt+spl;
   if (text) {
	   var list = text.split(spl);
	   for (var i = 0; i < list.length; i++) {
		   if (list[i] != '') str += lt+'li'+gt+list[i]+lt+'/li'+gt+spl;
	   }
	   str = spl+str;
   }
   else {
       this.openTag = spl+lt+tagName+strAttr+gt+spl+lt+'li'+gt;
       this.closeTag = lt+'/li'+gt+spl+lt+'/'+tagName+gt+spl;
   }
   this.text = str;
}

function insert_html_tags(taObj, htmlTag, attributes, prompts, lt, gt) {
  if (!taObj === null) return false; 
  if (!lt || !gt) {
      lt = '<';
	  gt = '>';
  }
  var caretPos = 0; 
  var start = 0;
  var end = 0;
  var selText;
  var strAttr = '';
  var unpariedTags = new Array('img', 'br', 'hr', 'input', 'link');
  if (attributes) {
	var ic = 0;
    for (var i in attributes) {
	  if (prompts) {
	      if (prompts[i]) {
	          var req = prompt(prompts[i], attributes[i]);
			  req = (req) ? req:'';
	          if (req && req != 'udefined')  strAttr += ' '+i+'="'+req+'"';
			  else if ((!req || req == 'udefined') && (ic === 0)) return false;
		      else if ((!req || req == 'udefined') && (ic > 0)) break;
	       }
		   else strAttr += ' '+i+'="'+attributes[i]+'"'; 
	    }
	   else strAttr += ' '+i+'="'+attributes[i]+'"'; 
	   ic++;
	}
	var openTag = lt+htmlTag+strAttr+gt;
  }
  var unparied = false;
  for (var i = 0; i <unpariedTags.length; i++) {
     if (unpariedTags[i] == htmlTag.toLowerCase())
	     unparied = true;
  }
  if (unparied) {
     var openTag = lt+htmlTag+strAttr+" /"+gt;
     var closeTag = "";
  }
  else {
     var openTag = lt+htmlTag+strAttr+gt;
	 var closeTag = lt+"/"+htmlTag+gt;
  }
  taObj.focus();
  if (document.getSelection || window.getSelection)  {
      start = taObj.selectionStart;
      end = taObj.selectionEnd;
  }
  
  else if (document.selection) {
      var sel = document.selection.createRange();
      var clone = sel.duplicate();
      sel.collapse(true);
      clone.moveToElementText(taObj);
      clone.setEndPoint("EndToEnd", sel);
      start = clone.text.length;
      sel = document.selection.createRange();
      clone = sel.duplicate();
      sel.collapse(false);
      clone.moveToElementText(taObj);
      clone.setEndPoint("EndToEnd", sel);
      end = clone.text.length;
  }
  var selText = taObj.value.substring(start, end);
  switch (htmlTag.toLowerCase()) {
    case 'ul':
	   var extend = new createList(htmlTag, strAttr ,selText, lt, gt);
	   openTag = extend.openTag;
	   closeTag = extend.closeTag;
	   selText = extend.text;
	break;
    case 'ol':
	   var extend = new createList(htmlTag, strAttr ,selText, lt, gt);
	   openTag = extend.openTag;
	   closeTag = extend.closeTag;
	   selText = extend.text;
	break;
  }
  if (htmlTag.indexOf('smile_') == 0) {
	  openTag = '';
	  closeTag = '';
	  selText = htmlTag.substr(6);
  }
  if (selText === '') {
      var str = taObj.value;
	  var nPos = start+openTag.length;
	  var begText = str.substring(0, start);
	  if  (navigator.appName == 'Microsoft Internet Explorer') {
     	   var reSpl = /\r\n/g;
		   var i = 0;
		   var res;
		   while (reSpl.exec(begText+openTag) != null) {
		       i++;
		   }
		   nPos = nPos - i;
	  }
      taObj.value = begText+openTag+closeTag+str.substr(start);
      if (taObj.createTextRange) {
          var caret = taObj.createTextRange();
          caret.collapse();
          caret.moveStart("character", nPos);
          caret.select();
      }
      else if(window.getSelection) {
         taObj.setSelectionRange(nPos, nPos);
         taObj.focus();
      }
  }
  else if (selText) {
      var str = taObj.value;
      taObj.value = str.substring(0, start)+openTag+selText+closeTag+str.substr(end);
  }
}
function comment_toolbar_init() {
	var WpQtTaObj = document.getElementById('comment');
    var comment_toolbar = document.createElement('div');
    comment_toolbar.id = 'comment-toolbar';
	WpQtTaObj.style.marginTop = '2px';
	comment_toolbar.align = 'right';
	WpQtToolBarWidth = WpQtTaObj.offsetWidth;
	WpQtToolBarButtons = '';
	for (var i = 0; i < comment_toolbar_buttons_array.length; i++) {
        WpQtToolBarButtons += '<td>'+comment_toolbar_buttons_array[i].button+'</td>';
	}
	comment_toolbar.innerHTML += '<div style="width: '+WpQtToolBarWidth+'px;" align="left"><table border="0" cellpadding="0" cellspacing="1" ><tr>'+WpQtToolBarButtons+'</tr></table></div>';
	WpQtTaObj.parentNode.insertBefore(comment_toolbar, WpQtTaObj);
}

function validate_comment_form(form) {
var error = false;
form.email.value = format_email_address(form.email.value);
if (form.author.value == '') {
document.getElementById('comment-author-error').innerHTML = '<?php _e('This field is required.', 'kleor'); ?>';
error = true; }
if ((form.email.value.indexOf('@') == -1) || (form.email.value.indexOf('.') == -1)) {
document.getElementById('comment-email-error').innerHTML = '<?php _e('The email appears to be invalid.', 'kleor'); ?>';
error = true; }
if (form.email.value == '') {
document.getElementById('comment-email-error').innerHTML = '<?php _e('This field is required.', 'kleor'); ?>';
error = true; }
if (form.comment.value == '') {
document.getElementById('comment-comment-error').innerHTML = '<?php _e('Please enter a comment.', 'kleor'); ?>';
error = true; }
return !error; }

comment_toolbar_init();
</script>