<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: versions.php sebastienheraud $
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
class CCKControllerSessions extends AdminController
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
		Session::checkToken() or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app	=	Factory::getApplication();
		$cid	=	$app->input->get( 'cid', array(), 'array' );
		
		if ( !is_array( $cid ) || count( $cid ) < 1 ) {
			Factory::getApplication()->enqueueMessage( Text::_( $this->text_prefix . '_NO_ITEM_SELECTED' ), 'warning' );
		} else {
			// Get the model.
			$model	=	$this->getModel();
			
			// Make sure the item ids are integers
			$cid	=	ArrayHelper::toInteger( $cid );
			
			// Remove the items.
			if ( $model->delete( $cid ) ) {
				$this->setMessage(Text::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			} else {
				$this->setMessage( $model->getError() );
			}
		}

		$vars	=	'';
		$extension	=	$app->input->get( 'extension', '' );
		if ( $extension ) {
			$vars	=	'&extension='.$extension;
		}
		
		$this->setRedirect( Route::_( 'index.php?option=' . $this->option . '&view=' . $this->view_list . $vars, false ) );
	}

	// getModel
	public function getModel( $name = 'Session', $prefix = CCK_MODEL, $config = array( 'ignore_request' => true ) )
	{
		return parent::getModel( $name, $prefix, $config );
	}
}
?>