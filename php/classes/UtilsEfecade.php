<?php
final class UtilsEfecade {
	static function renderPreVisualization($row, $preVisLength=100, $isFirst=false) {
		$previewContent = limitWords(urldecode($row[3]), $preVisLength, '...');

		if (function_exists('fkd_rewrite_text_image_sources')) {
			$previewContent = fkd_rewrite_text_image_sources($previewContent);
		}

		$previewContent = fkd_layout_safe_html_fragment($previewContent);

		return
			"<div class='posthead'>".
				"<div class='posthead'>".
				    "<h2><a class='title' href='index.php?texto=".utf8_decode($row[0])."'>".utf8_decode($row[2])."</a></h2>".
					"<div class='info'>".
						"<span class='date'>".$row[4]."</span>".
						"<div class='".(!$isFirst ? "act" : "act first")."'>".
						    "<span class='comments'><a href='index.php?texto=".utf8_decode($row[0])."#comments'>&nbsp;&nbsp;Coment&aacute;rios</a> / <a target='_blank' href='press.php?texto=".utf8_decode($row[0])."'>Imprimir</a></span>".
							"<div class='fixed'></div>".
						"</div>".
						"<div class='fixed'></div>".
					"</div>".
				"</div>".
			"</div>".
			"<div class='content'>".
			    "<p><span>".$previewContent."</span><a href='index.php?texto=".utf8_decode($row[0])."' class='more-link'>Continue a leitura...</a></p>".
				"<p class='under'></p>".
				"<div class='fixed'></div>".
			"</div>";
	}

	static function renderText($textInfo, $textTitle) {
		$textContent = urldecode($textInfo[2]);

		if (function_exists('fkd_rewrite_text_image_sources')) {
			$textContent = fkd_rewrite_text_image_sources($textContent);
		}

		$textContent = fkd_layout_safe_html_fragment($textContent);

		return
			"<div class='post' id='post-35'>".
				"<div class='posthead'>".
					"<div class='posthead'>".
					   "<h1>".$textTitle."</h1>".
						"<div class='info'>".
							"<span class='date'>".
								(($textInfo[4] != 1932 && $textInfo[4] != 1933) ? $textInfo[3] : "").
								"<div class='act first'>".
									"&nbsp;&nbsp;".
									(($textInfo[4] != 1932 && $textInfo[4] != 1933) ? "<span class='comments'><a href='#comments'>Coment&aacute;rios /&nbsp;</a></span>" : "").
									(($textInfo[4] != 1932 && $textInfo[4] != 1933) ? "<span class='addcomment'><a href='#respond'>Deixe seu coment&aacute;rio /&nbsp;</a></span>" : "").
									"<span class='addcomment'><a target='_blank' href='press.php?texto=".$textInfo[4]."'>Imprimir</a></span>".
									"<div class='fixed'></div>".
								"</div>".
								"<div class='fixed'></div>".
							"</span>".
						"</div>".
					"</div>".
				"</div>".
				"<div class='content'>".
				    $textContent.
					"<div class='fixed'></div>".
				"</div>".
			"</div>";
	}

	static function renderPreviousNextText($textoID) {
		return
			"<div id='postnavi'>".
				"<span class='prev'><a href='index.php?texto=".($textoID - 1)."'>Texto anterior</a></span>".
				"<span class='next'><a href='index.php?texto=".($textoID + 1)."'>Pr&oacute;ximo texto</a></span>".
				"<div class='fixed'></div>".
			"</div>";
	}
	
	static function renderCommentsForm($textoID, $commentsResource, $textInfo, $nome, $email, $site, $comment, $resp) {
	    if ($resp == "true") {
	        $nome = '';
	        $email = '';
	        $site = '';
	        $comment = '';
	    }
	    
	    $html =
	    "<div class='navigation'>".
	    "<div class='alignleft'></div>".
	    "<div class='alignright'></div>".
	    "</div>".
	    "<div id='respond' style='font-size:12px;'>".
	    "<h3>Deixe um coment&aacute;rio:</h3>".
	    "<form method='post' id='commentform' name='commentform' onsubmit='return checkCommentsData();'>".
	    "<p><input name='nome' id='nome' maxlength='50' size='22' tabindex='1' aria-required='true' type='text' value='$nome' />".
	    "&nbsp;&nbsp;<label for='author'><small><b>* Nome</b></small></label></p>".
	    "<p><input name='email' id='email' maxlength='50' size='22' tabindex='2' aria-required='true' type='text' value='$email' />".
	    "&nbsp;&nbsp;<label for='email'><small><b>* <i>E-mail</i> para contato</b></small></label>&nbsp;&nbsp;<input type='checkbox' name='isPublicarEmail' id='isPublicarEmail' tabindex='3' />&nbsp;&nbsp;<label for='email'><small>Publicar <i>e-mail</i>?</small></label></p>".
	    "<p><input name='theurl' id='theurl' maxlength='1024' size='22' tabindex='4' type='text' value='$site' />".
	    "&nbsp;&nbsp;<label for='theurl'><small>Sua cidade</small></label></p>".
	    "<p><textarea name='comment' id='comment' cols='100%' rows='10' tabindex='5' onKeyDown='textCounter(document.commentform.comment,document.commentform.remLen1,1000)' onKeyUp='textCounter(document.commentform.comment,document.commentform.remLen1,1000)'>$comment</textarea></p>".
	    "<input readonly type='text' name='remLen1' size='3' maxlength='4' value='1000'> caracteres restantes.<br /><br />".
	    fkd_recaptcha_widget(FKD_RECAPTCHA_ACTION_COMMENT).
						"<p><input id='commentSubmit' tabindex='6' value='Postar coment&aacute;rio' type='submit'><span id='commentSubmitStatus' class='comment-submit-status' role='status' aria-live='polite' style='display:none;margin-left:14px;vertical-align:middle;'><span class='comment-submit-spinner' aria-hidden='true' style='display:inline-block;width:14px;height:14px;margin-right:7px;border:2px solid #d19555;border-top-color:#5e0308;border-radius:50%;vertical-align:-3px;animation:comment-submit-spin 0.8s linear infinite;'></span>Enviando coment&aacute;rio...</span></p>".
					"<input type='hidden' name='fktexto' id='fktexto' value='".$textoID."' />".
					"<input type='hidden' name='m2' id='m2' value='34jD' />".
					"<input type='hidden' name='m' id='pscmt' value='pscmt' />".
					"<input type='hidden' name='tii' id='tii' value='".$textInfo."' />".
					"</form>".
					"</div>".
					"<br />".
					"<h3 id='comments'>Coment&aacute;rios:</h3>".
					"<div class='navigation'>".
					"<div class='alignleft'></div>".
					"<div class='alignright'></div>".
					"</div>";
	    $i = 0;
	    $ol = "<ol class='commentlist' >";
	    while ($rowComment = mysql_fetch_row($commentsResource)) {
	        $ol .=
	        "<li class='comment even thread-even depth-1' id='comment-9'>".
	        "<div id='div-comment-9' style='font-size:12px;'>".
	        "<div class='comment-author vcard'>".
	        "<img alt='' src='http://www.gravatar.com/avatar/65f7578a6db316c59f35c0574caf6929?s=32&amp;d=http%3A%2F%2Fwww.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D32&amp;r=G' class='avatar avatar-32 photo' height='32' width='32' />".
	        "&nbsp;&nbsp;<cite class='fn'>".utf8_decode($rowComment[1])."</cite>&nbsp;".($rowComment[3] == 1 ? ' ('.$rowComment[2].') ' : '')."<span class='says'>diz:</span>".
	        "</div>".
	        "<div class='comment-meta commentmetadata'>".$rowComment[5]."</div>".
	        "<p>".utf8_decode($rowComment[6])."</p>".
	        "<div class='reply'></div>".
	        "</div>".
	        "</li>";
	        
	        $i++;
	    }
	    $ol .= "</ol>";
	    
	    if ($i == 0)
	        $html .= "<br /><span style=\"font-size:13px\"><b><font color='red'>Nenhum coment&aacute;rio.</font></b></span>";
	        else
	            $html .= $ol;
	            
	            return $html;
	}
	
	
	
	
	
	
	
	

	static function renderAdsSideBar() {
		return
			"<div id='ads'>".
				"<a href='#'><img src='images/ads.gif' alt='ads'></a>".
				"<a href='#'><img src='images/ads.gif' alt='ads'></a>".
				"<div class='fixed'></div>".
				"<a href='#'><img src='images/ads.gif' alt='ads'></a>".
				"<a href='#'><img src='images/ads.gif' alt='ads'></a>".
				"<div class='fixed'></div>".
			"</div>";
	}

	static function renderCategotree() {
		return
			"<div id='popular_posts' >".
				"<div class='scontent'>".
					"<h3>Categorias de textos</h3>".
					"<br>".
					"<script language='JavaScript' src='modules/tigra_tree_menu/tree.js'></script>".
					"<script language='JavaScript' src='modules/tigra_tree_menu/tree_items.js'></script>".
					"<script language='JavaScript' src='modules/tigra_tree_menu/tree_tpl.js'></script>".
					"<script language='JavaScript'>new tree(TREE_ITEMS, TREE_TPL);</script>".
				"</div>".
			"</div>";
	}

	static function renderNoScript() {
		return
			"<noscript>".
				"<center><span class='noScript'><b>Voc&ecirc; precisa habilitar o JavaScript de seu navegador para visualizar esta p&aacute;gina corretamente!</b></span></center><br />".
			"</noscript>";
	}

}
?>
