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
class CCKViewVariations extends JCckBaseLegacyViewList
{
	protected $vName	=	'variation';
	protected $vTitle	=	_C7_TEXT;

	// prepareToolbar
	public function prepareToolbar()
	{	
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/toolbar/link.php';

		JToolBarHelper::title( JText::_( _C7_TEXT.'_MANAGER' ), Helper_Admin::getIcon( $this->vName ) );
		JToolBar::getInstance( 'toolbar' )->appendButton( 'CckLink', 'cck-template', JText::_( _C1_TEXT.'S' ), JRoute::_( 'index.php?option=com_cck&view=templates' ), '_self' );
	}
}
?>