<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;

// View
class CCKViewFolders extends JCckBaseLegacyViewList
{
	protected $vName	=	'folder';
	protected $vTitle	=	_C0_TEXT;
	
	// completeUI
	public function completeUI()
	{
		$this->document->setTitle( Text::_( 'COM_CCK_'.$this->vTitle.'_MANAGER' ) );
	}

	// getSortFields
	protected function getSortFields()
	{
		return array(
					'a.id'=>Text::_( 'COM_CCK_ID' ),
					'a.lft'=>Text::_( 'COM_CCK_ORDERING' ),
					'a.published'=>Text::_( 'COM_CCK_STATUS' ),
					'a.title'=>Text::_( 'COM_CCK_TITLE' )
				);
	}

	// prepareToolbar
	public function prepareToolbar()
	{
		Helper_Admin::addToolbar( $this->vName, 'COM_CCK_'.$this->vTitle, $this->state->get( 'filter.folder' ) );
	}
}
?>