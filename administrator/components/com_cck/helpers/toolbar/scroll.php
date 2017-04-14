<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: scroll.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JButton
class JButtonCckScroll extends JButton
{
	protected $_name = 'CckScroll';
	
	// fetchButton
	public function fetchButton( $type = 'CckScroll', $name = '', $text = '', $url = NULL )
	{
		$class	=	$this->fetchIconClass( $name );
		$doTask	=	$this->_getCommand( $url );
		$text	=	JText::_( $text );
		
		$html	=	'<a href="'.$doTask.'" class="scroll btn btn-small">'
				.	'<span class="'.$class.'"></span>'
				.	"\n".$text
				.	'</a>';
		
		return $html;
	}
	
	// fetchId
	public function fetchId( $type = 'CckScroll', $name = '' )
	{
		return $this->_parent->getName().'-'.$name;
	}
	
	// _getCommand
	protected function _getCommand( $url )
	{
		return $url;
	}
}

// JToolbarButton
class JToolbarButtonCckScroll extends JButtonCckScroll
{
}
?>