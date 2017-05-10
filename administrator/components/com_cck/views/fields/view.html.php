<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCKViewFields extends JCckBaseLegacyViewList
{
	protected $vName	=	'field';
	protected $vTitle	=	_C3_TEXT;
	
	// getSortFields
	protected function getSortFields()
	{
		return array(
					'folder_title'=>JText::_( 'COM_CCK_APP_FOLDER' ),
					'a.id'=>JText::_( 'COM_CCK_ID' ),
					'a.published'=>JText::_( 'COM_CCK_STATUS' ),
					'a.title'=>JText::_( 'COM_CCK_TITLE' )
				);
	}

	// prepareToolbar
	public function prepareToolbar()
	{
		Helper_Admin::addToolbar( $this->vName, $this->vTitle, $this->state->get( 'filter.folder' ) );
	}
}
?>