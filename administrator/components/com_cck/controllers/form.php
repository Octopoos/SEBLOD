<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.controllerform' );

// Controller
class CCKControllerForm extends JControllerForm
{
	protected $text_prefix	=	'COM_CCK';
	
	// __construct
	public function __construct( $config = array() )
	{
		parent::__construct( $config );

		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'save2new', 'save' );
		$this->registerTask( 'save2view', 'save' );
	}
	
	// saveAjax
	public function saveAjax()
	{
		$config		=	$this->save( null, null, true );
		$return		=	array(
							'error'=>0,
							'id'=>@(int)$config['id'],
							'isNew'=>@$config['isNew'],
							'pk'=>$config['pk']
						);
		
		if ( !$return['pk'] ) {
			$return['error']	=	1;
		}
		
		echo json_encode( $return );
	}

	// save
	public function save( $key = null, $urlVar = null, $isAjax = false )
	{
		if ( $isAjax !== true ) {
			JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		}
		
		$app		=	JFactory::getApplication();
		$model		=	$this->getModel( 'form' );
		$preconfig	=	$this->_getPreconfig();
		$task		=	$this->getTask();
		
		$config		=	$model->store( $preconfig );
		$id			=	$config['pk'];
		$link		=	'';
		
		// Return Now for Ajax..
		if ( $isAjax ) {
			return $config;
		}

		if ( $config['validate'] == 'retry' ) {
			parent::display();
			return true;
		}
		
		if ( $id ) {
			if ( $config['stage'] > -1  ) {
				$link	=	'index.php?option='.CCK_COM.'&view=form&type='.$preconfig['type'].'&id='.$id.$this->_getRedirectQuery();
				if ( $config['stage'] > 0 ) {
					$link	.=	'&stage='.$config['stage'];
				}
				$this->setRedirect( htmlspecialchars_decode( $link ) );
				return;
			}
			if ( $config['message_style'] ) {
				if ( isset( $config['message'] ) && $config['message'] ) {
					$msg	=	( $config['doTranslation'] ) ? JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $config['message'] ) ) ) : $config['message'];
				} else {
					$msg	=	JText::_( 'COM_CCK_SUCCESSFULLY_SAVED' );
				}
				$msgType	=	$config['message_style'];
			} else {
				$msg		=	'';
				$msgType	=	'';
			}
		} else {
			$msg		=	JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
			$msgType	= 'error';
		}
		
		switch ( $task ) {
			case 'apply':
				$link	=	'index.php?option='.CCK_COM.'&view=form&type='.$preconfig['type'].'&id='.$id.$this->_getRedirectQuery();
				break;
			case 'save2new':
				$link	=	'index.php?option='.CCK_COM.'&view=form&type='.$preconfig['type'].$this->_getRedirectQuery();
				break;
			case 'save2view':
				$location	=	JCckDatabase::loadResult( 'SELECT storage_location FROM #__cck_core WHERE id = '.(int)$id );
				$sef		=	0;
				$itemId2	=	0;
				if ( $location ) {
					require_once JPATH_SITE.'/plugins/cck_storage_location/'.$location.'/'.$location.'.php';
					$link	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location, 'getRoute', array( $config['pk'], $sef, $itemId2, array( 'type'=>$config['type'] ) ) );
					$link	=	str_replace( '/administrator/', '/', $link );
					break;
				}
                break;
			default:
				$link	=	$this->_getRedirectQuery( true );
				break;
		}
		
		$this->setRedirect( htmlspecialchars_decode( $link ), $msg, $msgType );
	}
	
	// cancel
	public function cancel( $key = null )
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		// Todo::Checkin,etc..
		
		$link	=	$this->_getRedirectQuery( true );
		
		$this->setRedirect( htmlspecialchars_decode( $link ) );
	}
	
	// _getPreconfig
	protected function _getPreconfig()
	{
		$data	=	JFactory::getApplication()->input->post->get( 'config', array(), 'array' );

		$data['copyfrom_id']	=	( !isset( $data['copyfrom_id'] ) ) ? 0 : $data['copyfrom_id'];
		$data['id']				=	( !isset( $data['id'] ) ) ? 0 : $data['id'];
		$data['itemId']			=	( !isset( $data['itemId'] ) ) ? 0 : $data['itemId'];
		$data['message']		=	( !isset( $data['message'] ) ) ? '' : $data['message'];
		$data['type']			=	( !isset( $data['type'] ) ) ? '' : $data['type'];
		$data['unique']			=	( !isset( $data['unique'] ) ) ? '' : $data['unique'];
		$data['url']			=	( !isset( $data['url'] ) ) ? '' : $data['url'];

		return $data;
	}

	// _getRedirectQuery
	protected function _getRedirectQuery( $full = false )
	{
		$app	=	JFactory::getApplication();
		
		if ( $full ) {
			$return	=	$app->input->getBase64( 'return' );
			if ( !empty( $return ) ) {
				return urldecode( base64_decode( $return ) );
			}
		}
		
		$return		=	$app->input->get( 'return_o', 'content' );
		if ( ! $return ) {
			$return	=	'content';
		}
		
		if ( $full ) {
			$query	=	'index.php?option=com_'.$return;
			$query	.=	$this->_getRedirectQuery_More( 'getCmd', 'view', 'return_v' );
		} else {
			$query	=	'&return_o='.$return;
			$query	.=	$this->_getRedirectQuery_More( 'getCmd', 'return_v', 'return_v' );
		}
		$query	.=	$this->_getRedirectQuery_More( 'getCmd', 'extension', 'return_extension' );
		
		$return	=	$app->input->getBase64( 'return' );
		if ( $return ) {
			$query	.=	'&return='.$return;
		}
		
		return $query;
	}
	
	// _getRedirectQuery_More
	protected function _getRedirectQuery_More( $request, $var, $name )
	{
		$app	=	JFactory::getApplication();
		$query	=	'';
		
		$value	=	(string)$app->input->$request( $name, '' );
		if ( $value != '' ) {
			$query	=	'&'.$var.'='.$value;
		}
		
		return $query;
	}
}
