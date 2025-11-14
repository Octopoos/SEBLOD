<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: field.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

jimport( 'cck.joomla.application.component.controllerform' );

// Controller
class CCKControllerField extends CCK_ControllerForm
{
	protected $text_prefix	=	'COM_CCK';

	// add
	public function add()
	{
		$app	=	Factory::getApplication();

		// Parent Method
		$result	=	parent::add();

		if ( $result instanceof Exception ) {
			return $result;
		}
		
		// Additional Vars
		$app->setUserState( CCK_COM.'.add.field.ajax_state', $app->input->getInt( 'ajax_state' ) );
		$app->setUserState( CCK_COM.'.edit.field.ajax_type', $app->input->getString( 'ajax_type', '' ) );
	}
	
	// allowAdd
	protected function allowAdd( $data = array() )
	{
		$app		=	Factory::getApplication();
		$user		=	Factory::getUser();
		$folderId	=	ArrayHelper::getValue( $data, 'folder', $app->input->getInt( 'filter_folder_id' ), 'int' );
		$allow		=	null;
		
		if ( $folderId ) {
			// Folder Permissions
			$allow	=	$user->authorise( 'core.create', $this->option.'.folder.'.$folderId );
		}
		
		if ( $allow !== null ) {
			return $allow;
		}

		// Component Permissions
		return parent::allowAdd( $data );
	}

	// allowEdit
	protected function allowEdit( $data = array(), $key = 'id' )
	{
		$user		=	Factory::getUser();
		$recordId	=	(int)isset( $data[$key] ) ? $data[$key] : 0;
		$folderId	=	0;
		
		if ( $recordId ) {
			$folderId	=	(int)$this->getModel()->getItem( $recordId )->folder;
		}
		
		if ( $folderId ) {
			// Folder Permissions
			return $user->authorise( 'core.edit', $this->option.'.folder.'.$folderId );
		}

		// Component Permissions
		return parent::allowEdit( $data, $key );
	}

	// cancel
	public function cancel( $key = null )
	{
		Session::checkToken() or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app	=	Factory::getApplication();

		parent::cancel();
		
		$app->setUserState( CCK_COM.'.add.field.ajax_state', null );
		$app->setUserState( CCK_COM.'.edit.field.ajax_type', null );
	}
	
	// edit
	public function edit( $key = null, $urlVar = null )
	{
		$app	=	Factory::getApplication();
		
		// Parent Method
		$result	=	parent::edit();

		if ( $result instanceof Exception ) {
			return $result;
		}
		
		// Additional Vars
		$app->setUserState( CCK_COM.'.edit.field.ajax_type', $app->input->getString( 'ajax_type', '' ) );
	}
	
	// postSaveHook
	protected function postSaveHook( BaseDatabaseModel $model, $validData = array() )
	{
		$app	=	Factory::getApplication();
		$task	=	$this->getTask();
		
		switch ( $task )
		{
			case 'save2new':
				$app->setUserState( CCK_COM.'.edit.field.ajax_type', $model->getItem()->type );
				break;
			default:
				$app->setUserState( CCK_COM.'.edit.field.ajax_type', null );
				break;
		}
	}
}