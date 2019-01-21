<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: template.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

jimport( 'joomla.application.component.controllerform' );

// Controller
class CCKControllerTemplate extends JControllerForm
{
	protected $text_prefix	=	'COM_CCK';

	// add
	public function add()
	{
		$app	=	JFactory::getApplication();

		// Parent Method
		$result	=	parent::add();

		if ( $result instanceof Exception ) {
			return $result;
		}
		
		// Additional Vars
		$app->setUserState( CCK_COM.'.edit.template.mode', $app->input->getString( 'mode', '' ) );
	}

	// allowAdd
	protected function allowAdd( $data = array() )
	{
		$app		=	JFactory::getApplication();
		$user		=	JFactory::getUser();
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
		$user		=	JFactory::getUser();
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

	// exportVariation
	public function exportVariation()
	{
		JSession::checkToken( 'get' ) or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app	=	JFactory::getApplication();
		$model	=	$this->getModel();
		$name	=	$app->input->getString( 'variation', '' );
		$folder	=	$app->input->getString( 'folder', '' );
		
		if ( $file = $model->prepareExport_Variation( $name, $folder ) ) {
			$file	=	JCckDevHelper::getRelativePath( $file, false );
			$this->setRedirect( JUri::base().'index.php?option=com_cck&task=download&file='.$file );
		} else {
			$this->setRedirect( 'index.php?option=com_cck&view=variations' );
		}
	}
}