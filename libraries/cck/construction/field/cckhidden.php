<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cckhidden.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JFormField
class JFormFieldCCKhidden extends JFormField
{
	protected $type	=	'CCKhidden';

	// getInput
	protected function getInput()
	{
		$style	=	'style="display: none;"';
		$html	=	'<div '.$style.'><textarea id="'.$this->id.'" name="'.$this->name.'" cols="25" rows="3">'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'</textarea></div>'
				.	'<div id="list_live_show"></div>';
				
		return $html;
	}
}
?>
