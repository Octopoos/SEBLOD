<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cckexport.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JFormField
class JFormFieldCCKexport extends JFormField
{
	protected $type	=	'CCKexport';

	// getInput
	protected function getInput()
	{
		$app		=	JFactory::getApplication();
		$type		=	(string)$this->element['extension_type'];
		$type		=	( $type ) ? $type : 'plugin';
		$extension	=	'&extension='.$type;
		if ( $type == 'languages' ) {
			$lang	=	JFactory::getLanguage()->getTag();			
			$url	=	'index.php?option=com_cck&task=export'.$extension.'&lang_tag=en-GB';
			$text	=	self::_getHtml( 'en-GB', $url, ' btn-small' );
			
			if ( $lang != 'en-GB' ) {
				$text	.=	'&nbsp;&nbsp;<span style="font-weight: normal;">or</span>&nbsp;&nbsp;';
				$tag	=	'&lang_tag='.$lang;
				$url	=	'index.php?option=com_cck&task=export'.$extension.$tag;
				$text	.=	self::_getHtml( $lang, $url, ' btn-small' );
			}
		} else {
			$lang	=	JFactory::getLanguage();
			$lang->load( 'com_cck_default', JPATH_SITE );
			$id		=	$app->input->getInt( 'extension_id', 0 );
			$id		=	'&extension_id='.$id;
			$url	=	'index.php?option=com_cck&task=export'.$extension.$id;
			$text	=	self::_getHtml( JText::_( 'COM_CCK_DOWNLOAD' ), $url, ' btn-success' );
		}
		
		return $text;
	}
	
	// _getHtml
	protected function _getHtml( $text, $url, $class = '' )
	{
		$html	=	'<a href="'.$url.'" class="btn'.$class.'">'
				.	'<span class="icon-download"></span>'
				.	"\n".$text
				.	'</a>';

		return $html;
	}
}
?>