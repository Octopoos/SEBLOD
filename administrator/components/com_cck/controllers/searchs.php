<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: searchs.php sebastienheraud $
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
class CCKControllerSearchs extends JControllerAdmin
{
	protected $text_prefix	=	'COM_CCK';
	
	// __construct
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}
	
	// getModel
	public function getModel( $name = 'Search', $prefix = CCK_MODEL, $config = array( 'ignore_request' => true ) )
	{
		return parent::getModel( $name, $prefix, $config );
	}
	
	// duplicate
	public function duplicate()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );

		$app	=	JFactory::getApplication();
		$pks	=	$app->input->post->get( 'cid', array(), 'array' );
		$pk		=	(int)( count( $pks ) ) ? $pks[0] : 0;
		
		if ( !$pk ) {
			$msg	=	JText::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ).'.';
			$type	=	'error';
		} else {
			$model	=	$this->getModel();
			$model->duplicate( $pk );
			$msg	=	JText::_( 'COM_CCK_SUCCESSFULLY_SAVED' );
			$type	=	'message';
		}
		
		$this->setRedirect( _C4_LINK, $msg, $type );
	}
	
	// version
	public function version()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app	=	JFactory::getApplication();
		$cid	=	$app->input->get( 'cid', array(), 'array' );
		$n		=	count( $cid );
		
		if ( !is_array( $cid ) || $n < 1 ) {
			$msg	=	JText::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ).'.';
			$type	=	'error';
		} else {
			$cid	=	ArrayHelper::toInteger( $cid );
			$model	=	$this->getModel();

			if ( $model->version( $cid ) ) {
				$msg	=	JText::sprintf( 'COM_CCK_SUCCESSFULLY_ARCHIVED', $n );
				$type	=	'message';
			} else {
				$msg	=	JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
				$type	=	'error';
			}
		}
		
		$this->setRedirect( _C4_LINK, $msg, $type );
	}
}
?>