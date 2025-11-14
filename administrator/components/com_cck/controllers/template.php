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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

jimport( 'joomla.application.component.controllerform' );

// Controller
class CCKControllerTemplate extends FormController
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
		$app->setUserState( CCK_COM.'.edit.template.mode', $app->input->getString( 'mode', '' ) );
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

	// exportVariation
	public function exportVariation()
	{
		Session::checkToken( 'get' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app	=	Factory::getApplication();
		$model	=	$this->getModel();
		$name	=	$app->input->getString( 'variation', '' );
		$folder	=	$app->input->getString( 'folder', '' );
		
		if ( $file = $model->prepareExport_Variation( $name, $folder ) ) {
			$file	=	JCckDevHelper::getRelativePath( $file, false );
			$this->setRedirect( Uri::base().'index.php?option=com_cck&task=download&file='.$file );
		} else {
			$this->setRedirect( 'index.php?option=com_cck&view=variations' );
		}
	}
}