<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: types.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

jimport( 'joomla.application.component.controlleradmin' );

// Controller
class CCKControllerTypes extends AdminController
{
	protected $text_prefix	=	'COM_CCK';
	
	// __construct
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}
	
	// duplicate
	public function duplicate()
	{
		Session::checkToken() or jexit( Text::_( 'JINVALID_TOKEN' ) );

		$app	=	Factory::getApplication();
		$pks	=	$app->input->post->get( 'cid', array(), 'array' );
		$pk		=	(int)( count( $pks ) ) ? $pks[0] : 0;
		
		if ( !$pk ) {
			$msg	=	Text::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ).'.';
			$type	=	'error';
		} else {
			$model	=	$this->getModel();
			$model->duplicate( $pk );
			$msg	=	Text::_( 'COM_CCK_SUCCESSFULLY_SAVED' );
			$type	=	'message';
		}
		
		$this->setRedirect( _C2_LINK, $msg, $type );
	}

	// getModel
	public function getModel( $name = 'Type', $prefix = CCK_MODEL, $config = array( 'ignore_request' => true ) )
	{
		return parent::getModel( $name, $prefix, $config );
	}
	
	// version
	public function version()
	{
		Session::checkToken() or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app	=	Factory::getApplication();
		$cid	=	$app->input->get( 'cid', array(), 'array' );
		$n		=	count( $cid );
		
		if ( !is_array( $cid ) || $n < 1 ) {
			$msg	=	Text::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ).'.';
			$type	=	'error';
		} else {
			$cid	=	ArrayHelper::toInteger( $cid );
			$model	=	$this->getModel();

			if ( $model->version( $cid ) ) {
				$msg	=	Text::sprintf( 'COM_CCK_SUCCESSFULLY_ARCHIVED', $n );
				$type	=	'message';
			} else {
				$msg	=	Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
				$type	=	'error';
			}
		}
		
		$this->setRedirect( _C2_LINK, $msg, $type );
	}
}
?>