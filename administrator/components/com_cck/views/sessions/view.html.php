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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

require_once JPATH_COMPONENT.'/helpers/helper_session.php';

// View
class CCKViewSessions extends JCckBaseLegacyViewList
{
	protected $vName	=	'session';
	protected $vTitle	=	_C8_TEXT;

	// getSortFields
	protected function getSortFields()
	{
		return array(
					'a.id'=>Text::_( 'COM_CCK_ID' ),
					'a.title'=>Text::_( 'COM_CCK_TITLE' )
				);
	}

	// prepareToolbar
	public function prepareToolbar()
	{
		$canDo				=	Helper_Admin::getActions();
		$this->extension	=	$this->state->get( 'filter.extension' );
		
		if ( $this->extension == 'extension' ) {
			ToolbarHelper::title( Text::_( 'COM_CCK_SESSION_MANAGER' ), Helper_Admin::getIcon( $this->vName ) );
			return;
		}
		
		Helper_Session::loadExtensionLang( $this->extension );
		
		ToolbarHelper::title( Text::_( 'COM_CCK_SESSION_MANAGER' ).' - '.Text::_( $this->extension ), Helper_Admin::getIcon( $this->vName ) );
		if ( $canDo->get( 'core.delete' ) ) {
			ToolbarHelper::custom( $this->vName.'s'.'.delete', 'delete', 'delete', 'JTOOLBAR_DELETE', true );
		}

		require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/toolbar/link.php';
		Toolbar::getInstance( 'toolbar' )->appendButton( 'CckLink', 'cck-addon', Text::_( $this->extension ), Route::_( 'index.php?option='.$this->extension ), '_self' );
		
		$this->sidebar = '';
	}
}
?>