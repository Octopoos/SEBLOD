<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: field.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

jimport( 'cck.joomla.application.component.controllerform' );

// Controller
class CCKControllerField extends CCK_ControllerForm
{
	protected $text_prefix	=	'COM_CCK';
	
	// allowAdd
	protected function allowAdd( $data = array() )
	{
		$app		=	JFactory::getApplication();
		$user		=	JFactory::getUser();
		$folderId	=	ArrayHelper::getValue( $data, 'folder', $app->input->getInt( 'filter_folder_id' ), 'int' );
		$allow		=	null;
		
		if ( $folderId ) {
			// If Folder
			$allow	=	$user->authorise( 'core.create', $this->option.'.folder.'.$folderId );
		}
		
		if ( $allow === null ) {
			// Component Permissions
			return parent::allowAdd( $data );
		} else {
			return $allow;
		}
	}

	// allowEdit
	protected function allowEdit( $data = array(), $key = 'id' )
	{
		$user		=	JFactory::getUser();
		$recordId	=	(int)isset( $data[$key] ) ? $data[$key] : 0;
		$folderId	=	0;
		
		if ( $recordId ) {
			$folderId	=	(int)$this->getModel()->getItem( $recordId )->folder;
		}
		
		if ( $folderId ) {
			// Folder Permissions
			return $user->authorise( 'core.edit', $this->option.'.folder.'.$folderId );
		} else {
			// Component Permissions
			return parent::allowEdit( $data, $key );
		}
	}
	
	// add
	public function add()
	{
		$app	=	JFactory::getApplication();

		// Parent Method
		$result	=	parent::add();
		if ( JError::isError( $result ) ) {
			return $result;
		}
		
		// Additional Vars
		$app->setUserState( CCK_COM.'.add.field.ajax_state', $app->input->getInt( 'ajax_state' ) );
		$app->setUserState( CCK_COM.'.edit.field.ajax_type', $app->input->getString( 'ajax_type', '' ) );
	}
	
	// edit
	public function edit( $key = null, $urlVar = null )
	{
		$app	=	JFactory::getApplication();
		
		// Parent Method
		$result	=	parent::edit();
		if ( JError::isError( $result ) ) {
			return $result;
		}
		
		// Additional Vars
		$app->setUserState( CCK_COM.'.edit.field.ajax_type', $app->input->getString( 'ajax_type', '' ) );
	}
	
	// cancel
	public function cancel( $key = null )
	{
		$app	=	JFactory::getApplication();
		
		parent::cancel();
		
		$app->setUserState( CCK_COM.'.add.field.ajax_state', null );
		$app->setUserState( CCK_COM.'.edit.field.ajax_type', null );
	}
	
	// postSaveHook
	protected function postSaveHook( JModelLegacy $model, $validData = array() )
	{
		$app	=	JFactory::getApplication();
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