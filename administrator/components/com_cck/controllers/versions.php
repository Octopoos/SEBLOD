<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: versions.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

jimport( 'joomla.application.component.controlleradmin' );

// Controller
class CCKControllerVersions extends JControllerAdmin
{
	protected $text_prefix	=	'COM_CCK';
	
	// __construct
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}
	
	// getModel
	public function getModel( $name = 'Version', $prefix = CCK_MODEL, $config = array( 'ignore_request' => true ) )
	{
		return parent::getModel( $name, $prefix, $config );
	}
	
	// delete
	public function delete()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app	=	JFactory::getApplication();
		$cid	=	$app->input->get( 'cid', array(), 'array' );
		
		if ( !is_array( $cid ) || count( $cid ) < 1 ) {
			JError::raiseWarning( 500, JText::_( $this->text_prefix . '_NO_ITEM_SELECTED' ) );
		} else {
			// Get the model.
			$model	=	$this->getModel();
			
			// Make sure the item ids are integers
			$cid	=	ArrayHelper::toInteger( $cid );
			
			// Remove the items.
			if ( $model->delete( $cid ) ) {
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			} else {
				$this->setMessage( $model->getError() );
			}
		}

		$vars	=	'';
		$type	=	$app->input->get( 'element_type', '' );
		if ( $type ) {
			$vars	=	'&filter_e_type='.$type;
		}
		
		$this->setRedirect( JRoute::_( 'index.php?option=' . $this->option . '&view=' . $this->view_list . $vars, false ) );
	}
	
	// revert
	public function revert()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app	=	JFactory::getApplication();
		$pks	=	$app->input->post->get( 'cid', array(), 'array' );
		$pk		=	(int)( count( $pks ) ) ? $pks[0] : 0;
		$type	=	$app->input->post->getString( 'element_type', 'type' );
		
		$model	=	$this->getModel();
		$user	=	JFactory::getUser();		
		$res	=	( $user->authorise( 'core.edit', CCK_COM ) ) ? $model->revert( $pk, $type ) : false;
		
		if ( $res ) {
			if ( $type == 'search' ) {
				$link	=	_C4_LINK;
			} elseif ( $type == 'type' ) {
				$link	=	_C2_LINK;
			}
			$msg	=	JText::_( 'COM_CCK_SUCCESSFULLY_RESTORED' );
			$type	=	'message';
		} else {
			$link	=	_C6_LINK.'&filter_e_type='.$type;
			$msg	=	JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
			$type	=	'error';
		}
		
		$this->setRedirect( $link, $msg, $type );
	}
}
?>