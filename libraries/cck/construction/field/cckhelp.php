<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cckhelp.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JFormField
class JFormFieldCCKhelp extends JFormField
{
	protected $type	=	'CCKhelp';
	
	// getLabel
	protected function getLabel()
	{
		return;
	}
	
	// getInput
	protected function getInput()
	{
		$type		=	(string)$this->element['extension_type'];
		$slug		=	(string)$this->element['extension_slug'];
		
		$link	=	'https://www.seblod.com/support/documentation/'.$slug.'?tmpl=component';
		$opts	=	'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=685,height=600';
		$help	=	'<div class="how-to-setup">'
				.	'<a href="'.$link.'" onclick="window.open(this.href, \'targetWindow\', \''.$opts.'\'); return false;">' . JText::_( 'COM_CCK_HOW_TO_SETUP_THIS_'.$type ) . '</a>'
				.	'</div>';
		
		return $help;
	}
}
?>