<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: link.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JButton
class JButtonCckLink extends JButton
{
	protected $_name	=	'CckLink';
	protected $tag		=	'a';
	protected $tag2		=	'span';
	
	// fetchButton
	public function fetchButton( $type = 'CckLink', $name = '', $text = '', $url = '', $target = '' )
	{
		$class	=	$this->fetchIconClass( $name );
		$class2	=	( $name == 'apply' || $name == 'new' ) ? 'btn btn-small btn-success' : 'btn btn-small';
		$target	=	$target ? ' target="'.$target.'"' : '';
		$text	=	JText::_( $text );
		
		if ( $target ) {
			$html	=	'<a href="'.$url.'"'.$target.' class="'.$class2.'">'
					.	'<'.$this->tag2.' class="'.$class.'"></'.$this->tag2.'>'
					.	"\n".$text
					.	'</a>';
		} else {
			$task	=	$this->_getCommand( $url );
			$html	=	'<'.$this->tag.' '.$task.' class="'.$class2.'">'
					.	'<'.$this->tag2.' class="'.$class.'"></'.$this->tag2.'>'
					.	"\n".$text
					.	'</'.$this->tag.'>';
		}
		
		return $html;
	}
	
	// fetchId
	public function fetchId( $type = 'CckLink', $name = '' )
	{
		return $this->_parent->getName().'-'.$name;
	}
	
	// _getCommand
	protected function _getCommand( $url )
	{
		return 'href="'.$url.'"';
	}
}

// JToolbarButton
class JToolbarButtonCckLink extends JButtonCckLink
{
	protected $tag		=	'button';
	protected $tag2		=	'i';
	
	// _getCommand
	protected function _getCommand( $url )
	{
		return 'onclick="'.str_replace( 'javascript:', '', $url ).'"';
	}
}
?>