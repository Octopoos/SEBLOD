<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: box.php sebastienheraud $
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

jimport( 'joomla.application.component.controllerform' );

// Controller
class CCKControllerBox extends FormController
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
		$app->setUserState( CCK_COM.'.add.box.alt', $app->input->getInt( 'alt', 0 ) );
		$app->setUserState( CCK_COM.'.add.box.validation', $app->input->getInt( 'validation', 0 ) );
		$app->setUserState( CCK_COM.'.add.box.file', $app->input->getString( 'file', '' ) );
		$app->setUserState( CCK_COM.'.add.box.function', $app->input->getString( 'function', '' ) );
		$app->setUserState( CCK_COM.'.add.box.bx.id', $app->input->get( 'id', '' ) );
		$app->setUserState( CCK_COM.'.add.box.bx.title', $app->input->getString( 'title', '' ) );
		$app->setUserState( CCK_COM.'.add.box.bx.name', $app->input->get( 'name', '' ) );
		$app->setUserState( CCK_COM.'.add.box.bx.type', $app->input->getString( 'type', '' ) );
		$app->setUserState( CCK_COM.'.add.box.bx.params', $app->input->getString( 'params', '' ) );
	}

	// allowAdd
	protected function allowAdd( $data = array() )
	{
		return true;
	}

	// save
	public function save( $key = null, $urlVar = null )
	{
		jexit( Text::_( 'JINVALID_TOKEN' ) );
	}
}
?>