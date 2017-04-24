<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: modalbox.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JButton
class JButtonCckModalBox extends JButton
{
	protected $_name	=	'CckModalBox';
	
	// fetchButton
	public function fetchButton( $type = 'CckModalBox', $name = '', $text = '', $url = '', $width = 820, $height = 400, $top = 0, $left = 0 )
	{
		JCck::loadjQuery();
		
		$class	=	$this->fetchIconClass( $name );
		$class2	=	( $name == 'apply' || $name == 'new' ) ? 'btn btn-small btn-success' : 'btn btn-small';
		$text	=	JText::_( $text );
		$url	=	$this->_getCommand( $name, $url, $width, $height, $top, $left );
		
		$html	=	'<a class="cbox_button '.$class2.'" href="'.$url.'">'
				.	'<span class="'.$class.'"></span>'
				.	"\n".$text
				.	'</a>';
		
		return $html;
	}
	
	// fetchId
	public function fetchId( $type = 'CckModalBox', $name = '' )
	{
		return $this->_parent->getName().'-'."popup-$name";
	}
	
	// _getCommand
	protected function _getCommand( $name, $url, $width, $height, $top, $left )
	{
		if ( substr( $url, 0, 4 ) !== 'http' ) {
			$url	=	JUri::base().$url;
		}
		
		return $url;
	}
}

// JToolbarButton
class JToolbarButtonCckModalBox extends JButtonCckModalBox
{
}
?>