<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JCck::loadjQuery();
$app	=	JFactory::getApplication();
$doc	=	JFactory::getDocument();
$css	=	'';
$elem	=	'';
$href	=	'index.php?option=com_cck&view=form&layout=select&quickadd=1&quicklayout='.$modal_layout.'&tmpl=component';
$label	=	JText::_( 'MOD_CCK_QUICKADD_ADD_NEW_CONTENT' );

require JModuleHelper::getLayoutPath('mod_cck_quickadd', 'default_'.$module->position );

if ( $css ) {
	$doc->addStyleDeclaration( $css );
}
if ( $elem ) {
	$doc->addScript( JUri::root( true ).'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
	$doc->addStyleSheet( JUri::root( true ).'/media/cck/scripts/jquery-colorbox/css/colorbox.css' );
	$js	=	'
			jQuery(document).ready(function($){
				$("'.$elem.'").on("click", function(e) { e.preventDefault();
					$.colorbox({href:$(this).attr(\'href\'),open:true,iframe:true,innerWidth:850,innerHeight:430,scrolling:true,overlayClose:false,fixed:true});
					return false;
				});
			});
			';
	$doc->addScriptDeclaration( $js );
}
?>