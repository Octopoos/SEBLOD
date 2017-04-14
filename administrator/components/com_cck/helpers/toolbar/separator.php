<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: link.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JButton
class JButtonCckSeparator extends JButton
{
	protected $_name	=	'CckSeparator';
	protected $tag		=	'li';
	
	// fetchButton
	public function fetchButton( $type = 'CckSeparator' )
	{
	}
	
	// render
	public function render( &$definition )
	{
		$class	=	( empty( $definition[1] ) ) ? 'divider' : 'btn-group '.$definition[1];
		
		return '<'.$this->tag.' class="'.$class.'"></'.$this->tag.'>';
	}
}

// JToolbarButton
class JToolbarButtonCckSeparator extends JButtonCckSeparator
{
	protected $tag		=	'div';
	
	// render
	public function render( &$definition )
	{
		$class	=	( empty( $definition[1] ) ) ? '' : 'btn-group '.$definition[1];
		
		return $class ? '<'.$this->tag.' class="'.$class.'"></'.$this->tag.'>' : '';
	}
}
?>