<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCKViewFolders extends JCckBaseLegacyViewList
{
	protected $vName	=	'folder';
	protected $vTitle	=	_C0_TEXT;
	
	// getSortFields
	protected function getSortFields()
	{
		return array(
					'a.id'=>JText::_( 'COM_CCK_ID' ),
					'lft'=>JText::_( 'COM_CCK_ORDERING' ),
					'a.published'=>JText::_( 'COM_CCK_STATUS' ),
					'title'=>JText::_( 'COM_CCK_TITLE' )
				);
	}

	// prepareToolbar
	public function prepareToolbar()
	{
		Helper_Admin::addToolbar( $this->vName, 'COM_CCK_'.$this->vTitle, $this->state->get( 'filter.folder' ) );
	}
}
?>