<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cckexport.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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
			$text	=	self::_getHTML( 'en-GB', $url, ' btn-small' );
			
			if ( $lang != 'en-GB' ) {
				$text	.=	'&nbsp;&nbsp;<span style="font-weight: normal;">or</span>&nbsp;&nbsp;';
				$tag	=	'&lang_tag='.$lang;
				$url	=	'index.php?option=com_cck&task=export'.$extension.$tag;
				$text	.=	self::_getHTML( $lang, $url, ' btn-small' );
			}
		} else {
			$lang	=	JFactory::getLanguage();
			$lang->load( 'com_cck_default', JPATH_SITE );
			$id		=	$app->input->getInt( 'extension_id', 0 );
			$id		=	'&extension_id='.$id;
			$url	=	'index.php?option=com_cck&task=export'.$extension.$id;
			$text	=	self::_getHTML( JText::_( 'COM_CCK_DOWNLOAD' ), $url, ' btn-success' );
		}
		if ( !JCck::on() ) {
			$text	=	'<div style="float: left; padding-top: 7px; font-weight: bold;">'.$text.'</div>';
		}
		
		return $text;
	}
	
	// _getHTML
	protected function _getHTML( $text, $url, $class = '' )
	{
		if ( JCck::on() ) {
			$html	=	'<a href="'.$url.'" class="btn'.$class.'">'
					.	'<span class="icon-download"></span>'
					.	"\n".$text
					.	'</a>';

		} else {
			$html	=	'<a href="'.$url.'">'.$text.' &dArr;</a>';
		}

		return $html;
	}
}
?>