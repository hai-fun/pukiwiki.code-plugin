<?php
/**
 * HTML キーワード定義ファイル
 */

$capital = 1;                      // 予約語の大文字小文字を区別しない
// コメント定義
$switchHash['<'] = PLUGIN_CODE_COMMENT;        // コメントは <!-- から --> まで
$code_comment = Array(
	'<' => Array(
				 Array('/^<\!--/', '-->', 3),
	)
);

$code_css = array(
  'operator',		// オペレータ関数
  'identifier',	// その他の識別子
  'pragma',		// module, import と pragma
  'system',		// 処理系組み込みの奴 __stdcall とか
  );

$code_keyword = array(
'_blank'=>2,
'_parent'=>2,
'_self'=>2,
'_top'=>2,
'a'=>2,
'abbr'=>2,
'above'=>2,
'absbottom'=>2,
'absmiddle'=>2,
'accesskey'=>2,
'acronym'=>2,
'action'=>2,
'address'=>2,
'align'=>2,
'all'=>2,
'applet'=>2,
'area'=>2,
'autoplay'=>2,
'autostart'=>2,
'b'=>2,
'background'=>2,
'base'=>2,
'basefont'=>2,
'baseline'=>2,
'behavior'=>2,
'below'=>2,
'bgcolor'=>2,
'bgsound'=>2,
'big'=>2,
'blink'=>2,
'blockquote'=>2,
'body'=>2,
'border'=>2,
'bordercolor'=>2,
'bordercolordark'=>2,
'bordercolorlight'=>2,
'bottom'=>2,
'box'=>2,
'br'=>2,
'button'=>2,
'caption'=>2,
'cellpadding'=>2,
'cellspacing'=>2,
'center'=>2,
'challenge'=>2,
'char'=>2,
'checkbox'=>2,
'checked'=>2,
'cite'=>2,
'clear'=>2,
'clip'=>2,
'code'=>2,
'codebase'=>2,
'codetype'=>2,
'col'=>2,
'colgroup'=>2,
'color'=>2,
'cols'=>2,
'colspan'=>2,
'comment'=>2,
'controls'=>2,
'data'=>2,
'dd'=>2,
'declare'=>2,
'defer'=>2,
'del'=>2,
'delay'=>2,
'dfn'=>2,
'dir'=>2,
'direction'=>2,
'disabled'=>2,
'div'=>2,
'dl'=>2,
'doctype'=>2,
'dt'=>2,
'em'=>2,
'embed'=>2,
'enctype'=>2,
'face'=>2,
'fieldset'=>2,
'file'=>2,
'font'=>2,
'for'=>2,
'form'=>2,
'frame'=>2,
'frameborder'=>2,
'frameset'=>2,
'get'=>2,
'groups'=>2,
'groups'=>2,
'gutter'=>2,
'h1'=>2,
'h2'=>2,
'h3'=>2,
'h4'=>2,
'h5'=>2,
'h6'=>2,
'h7'=>2,
'head'=>2,
'height'=>2,
'hidden'=>2,
'hn'=>2,
'hr'=>2,
'href'=>2,
'hsides'=>2,
'hspace'=>2,
'html'=>2,
'i'=>2,
'id'=>2,
'iframe'=>2,
'ilayer'=>2,
'image'=>2,
'img'=>2,
'index'=>2,
'inherit'=>2,
'input'=>2,
'ins'=>2,
'isindex'=>2,
'javascript'=>2,
'justify'=>2,
'kbd'=>2,
'keygen'=>2,
'label'=>2,
'language'=>2,
'layer'=>2,
'left'=>2,
'legend'=>2,
'lhs'=>2,
'li'=>2,
'link'=>2,
'listing'=>2,
'loop'=>2,
'map'=>2,
'marquee'=>2,
'maxlength'=>2,
'menu'=>2,
'meta'=>2,
'method'=>2,
'methods'=>2,
'middle'=>2,
'multicol'=>2,
'multiple'=>2,
'name'=>2,
'next'=>2,
'nextid'=>2,
'nobr'=>2,
'noembed'=>2,
'noframes'=>2,
'nolayer'=>2,
'none'=>2,
'nosave'=>2,
'noscript'=>2,
'notab'=>2,
'nowrap'=>2,
'object'=>2,
'ol'=>2,
'onblur'=>2,
'onchange'=>2,
'onclick'=>2,
'onfocus'=>2,
'onload'=>2,
'onmouseout'=>2,
'onmouseover'=>2,
'onreset'=>2,
'onselect'=>2,
'onsubmit'=>2,
'option'=>2,
'p'=>2,
'pagex'=>2,
'pagey'=>2,
'palette'=>2,
'panel'=>2,
'param'=>2,
'parent'=>2,
'password'=>2,
'plaintext'=>2,
'pluginspage'=>2,
'post'=>2,
'pre'=>2,
'previous'=>2,
'q'=>2,
'radio'=>2,
'rel'=>2,
'repeat'=>2,
'reset'=>2,
'rev'=>2,
'rhs'=>2,
'right'=>2,
'rows'=>2,
'rowspan'=>2,
'rules'=>2,
's'=>2,
'samp'=>2,
'save'=>2,
'script'=>2,
'scrollamount'=>2,
'scrolldelay'=>2,
'select'=>2,
'selected'=>2,
'server'=>2,
'shapes'=>2,
'show'=>2,
'size'=>2,
'small'=>2,
'song'=>2,
'spacer'=>2,
'span'=>2,
'src'=>2,
'standby'=>2,
'strike'=>2,
'strong'=>2,
'style'=>2,
'sub'=>2,
'submit'=>2,
'summary'=>2,
'sup'=>2,
'tabindex'=>2,
'table'=>2,
'target'=>2,
'tbody'=>2,
'td'=>2,
'text'=>2,
'textarea'=>2,
'textbottom'=>2,
'textfocus'=>2,
'textmiddle'=>2,
'texttop'=>2,
'tfoot'=>2,
'th'=>2,
'thead'=>2,
'title'=>2,
'top'=>2,
'tr'=>2,
'tt'=>2,
'txtcolor'=>2,
'type'=>2,
'u'=>2,
'ul'=>2,
'urn'=>2,
'usemap'=>2,
'valign'=>2,
'value'=>2,
'valuetype'=>2,
'var'=>2,
'visibility'=>2,
'void'=>2,
'vsides'=>2,
'vspace'=>2,
'wbr'=>2,
'width'=>2,
'wrap'=>2,
'xmp'=>2,
  );
?>
