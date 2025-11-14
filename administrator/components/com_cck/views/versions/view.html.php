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

// View
class CCKViewVersions extends JCckBaseLegacyViewList
{
	protected $vName	=	'version';
	protected $vTitle	=	_C6_TEXT;

	// getSortFields
	protected function getSortFields()
	{
		return array(
					'a.id'=>Text::_( 'COM_CCK_ID' ),
					'b.title'=>Text::_( 'COM_CCK_TITLE' )
				);
	}

	// prepareToolbar
	public function prepareToolbar()
	{
		$canDo			=	Helper_Admin::getActions();
		$this->e_type	=	$this->state->get( 'filter.e_type' );
		$type			=	( $this->e_type == 'search' ) ? _C4_TEXT : _C2_TEXT;
		$type2			=	( $this->e_type == 'search' ) ? 'search' : 'form';
		
		ToolbarHelper::title( Text::_( _C6_TEXT.'_MANAGER' ).' - '.Text::_( 'COM_CCK_'.$type.'s' ), Helper_Admin::getIcon( $this->vName ) );
		if ( $canDo->get( 'core.delete' ) ) {
			ToolbarHelper::custom( $this->vName.'s'.'.delete', 'delete', 'delete', 'JTOOLBAR_DELETE', true );
		}

		require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/toolbar/link.php';
		Toolbar::getInstance( 'toolbar' )->appendButton( 'CckLink', 'cck-'.$type2, Text::_( 'COM_CCK_'.$type.'S' ), Route::_( 'index.php?option=com_cck&view='.$this->e_type.'s' ), '_self' );
	}
}
?>