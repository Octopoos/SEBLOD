<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$doc	=	JFactory::getDocument();
$js		=	'
			(function ($){
				JCck.Dev = {
					getEditor: function() {
						if (window.CKEDITOR) {
							editor = "ck"; 
						} else if (window.JContentEditor) {
							editor = "jce";
						} else if (window.tinyMCE) {
							editor = "tiny";
						} else {
							editor = null;
						}
						return editor;
					},
					reset: function() {
						var elem	= "'.$this->item->id.'";
						var content = "";
						parent.jQuery("#"+elem).val(content);
						this.close();
					},
					submit: function() {
						var content = "";
						var elem	=	"'.$this->item->id.'";
						var editor	=	JCck.Dev.getEditor();
						switch( editor ) {
							case "ck":
								content = window.CKEDITOR.instances[elem].getData(); break;
							case "jce":
								content = window.JContentEditor.getContent(elem); break;
							case "tiny":
								if (!tinyMCE.get(elem) || tinyMCE.get(elem).isHidden()) {
									content = document.getElementById(elem).value;
								} else {
									content = tinyMCE.get(elem).getContent();
								}
								break;
							default:
								if (document.getElementById(elem)) {
									content = document.getElementById(elem).value;
								}
								break;
						}
						parent.jQuery("#"+elem).val(content);
						this.close();
						return;
					}
    			}
				$(document).ready(function(){
					var elem 	=	"'.$this->item->id.'";
					var content =	parent.jQuery("#"+elem).val();
					$("#"+elem).val(content);
				});
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );

$value		=	'';
$buttons	=	array( 'pagebreak', 'readmore' );
$editor		=	JFactory::getEditor( @$this->item->type ? $this->item->type : null );
$params		=	explode( '||', $this->item->params );
$width		=	( $params[0] ) ? $params[0] : '100%';
$width		=	urldecode( $width );
$height		=	( $params[1] ) ? $params[1] : '280';
$asset		=	( $params[2] ) ? $params[2] : '';
?>

<div class="seblod">
	<?php echo $editor->display( $this->item->id, $value, $width, $height, '60', '20', $buttons, $this->item->id, $asset ); ?>
</div>