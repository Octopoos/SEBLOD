<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

jimport( 'joomla.application.component.controlleradmin' );

// Controller
class CCKControllerList extends AdminController
{
	protected $text_prefix	=	'COM_CCK';
	
	// __construct
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}
	
	// delete
	public function delete()
	{
		Session::checkToken( 'get' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app	=	Factory::getApplication();
		$model	=	$this->getModel();
		$cid	=	$app->input->get( 'cid', array(), 'array' );
		$cid	=	ArrayHelper::toInteger( $cid );
		
		if ( $nb = $model->delete( $cid ) ) {
			$msg		=	Text::_( 'COM_CCK_SUCCESSFULLY_DELETED' ); /* TODO#SEBLOD: Text::plural( 'COM_CCK_N_SUCCESSFULLY_DELETED', $nb ); */
			$msgType	=	'message';
		} else {
			$msg		=	Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
			$msgType	=	'error';
		}
		
		$this->setRedirect( $this->_getReturnPage(), $msg, $msgType );
	}
	
	// export
	public function export()
	{
		if ( !Session::checkToken( 'get' ) ) {
			Session::checkToken( 'post' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );
		}
		
		if ( !is_file( JPATH_ADMINISTRATOR.'/components/com_cck_exporter/models/cck_exporter.php' ) ) {
			$this->setRedirect( $this->_getReturnPage(), Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
			return;
		}
		
		$app		=	Factory::getApplication();
		$ids		=	$app->input->get( 'cid', array(), 'array' );
		$task_id	=	$app->input->getInt( 'tid', 0 );
		$ids		=	ArrayHelper::toInteger( $ids );
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck_exporter/models/cck_exporter.php';
		$model		=	BaseDatabaseModel::getInstance( 'CCK_Exporter', 'CCK_ExporterModel' );
		$params		=	ComponentHelper::getParams( 'com_cck_exporter' );
		$output		=	0; // $params->get( 'output', 0 );
		
		if ( $file = $model->prepareExport( $params, $task_id, $ids ) ) {
			if ( $output > 0 ) {
				$this->setRedirect( $this->_getReturnPage(), Text::_( 'COM_CCK_SUCCESSFULLY_EXPORTED' ), 'message' );
			} else {
				$file	=	JCckDevHelper::getRelativePath( $file, false );
				$this->setRedirect( Uri::base().'index.php?option=com_cck&task=download&file='.$file );
			}
		} else {
			$this->setRedirect( $this->_getReturnPage(), Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}

	// getModel
	public function getModel( $name = 'List', $prefix = CCK_MODEL, $config = array( 'ignore_request' => true ) )
	{
		return parent::getModel( $name, $prefix, $config );
	}

	// process
	public function process()
	{
		if ( !Session::checkToken( 'get' ) ) {
			Session::checkToken( 'post' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );
		}
		
		if ( !is_file( JPATH_ADMINISTRATOR.'/components/com_cck_toolbox/models/cck_toolbox.php' ) ) {
			$this->setRedirect( $this->_getReturnPage(), Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
			return;
		}
		
		$app		=	Factory::getApplication();
		$ids		=	$app->input->get( 'cid', array(), 'array' );
		$task_id	=	$app->input->getInt( 'tid', 0 );
		$ids		=	ArrayHelper::toInteger( $ids );
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck_toolbox/models/cck_toolbox.php';
		$model		=	BaseDatabaseModel::getInstance( 'CCK_Toolbox', 'CCK_ToolboxModel' );
		$params		=	ComponentHelper::getParams( 'com_cck_toolbox' );
		$output		=	1; // $params->get( 'output', 0 );
		
		if ( $file = $model->prepareProcess( $params, $task_id, $ids ) ) {
			if ( $output > 0 ) {
				if ( isset( $config['message'] ) && $config['message'] != '' ) {
					$msg	=	( $config['doTranslation'] ) ? Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $config['message'] ) ) ) : $config['message'];
				} else {
					$msg	=	Text::_( 'COM_CCK_SUCCESSFULLY_PROCESSED' );
				}
				if ( isset( $config['message_style'] ) && $config['message_style'] != '' ) {
					$msgType	=	$config['message_style'];
				} else {
					$msgType	=	'message';
				}
				$this->setRedirect( $this->_getReturnPage(), $msg, $msgType );
			} else {
				$file	=	JCckDevHelper::getRelativePath( $file, false );
				$this->setRedirect( Uri::base().'index.php?option=com_cck&task=download&file='.$file );
			}
		} else {
			$this->setRedirect( $this->_getReturnPage(), Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}
	
	// search	
	public function search()
	{
		parent::display( true );
	}
	
	// _getReturnPage
	protected function _getReturnPage( $base = false )
	{
		$app	=	Factory::getApplication();
		$return	=	$app->input->getBase64( 'return' );
		
		if ( empty( $return ) || !Uri::isInternal( base64_decode( $return ) ) ) {
			return ( $base == true ) ? Uri::base() : 'index.php?option=com_cck';
		} else {
			return base64_decode( $return );
		}
	}
}
?>