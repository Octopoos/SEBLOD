<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: box.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.controllerform' );

// Controller
class CCKControllerBox extends JControllerForm
{
	// allowAdd
	protected function allowAdd( $data = array() )
	{
		return true;
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
		$app->setUserState( 'com_cck.add.box.validation', $app->input->getInt( 'validation', 0 ) );
		$app->setUserState( 'com_cck.add.box.file', $app->input->getString( 'file', '' ) );
		$app->setUserState( 'com_cck.add.box.function', $app->input->getString( 'function', '' ) );
		$app->setUserState( 'com_cck.add.box.bx.id', $app->input->get( 'id', '' ) );
		$app->setUserState( 'com_cck.add.box.bx.title', $app->input->getString( 'title', '' ) );
		$app->setUserState( 'com_cck.add.box.bx.name', $app->input->get( 'name', '' ) );
		$app->setUserState( 'com_cck.add.box.bx.type', $app->input->getString( 'type', '' ) );
		$app->setUserState( 'com_cck.add.box.bx.params', $app->input->getString( 'params', '' ) );
	}
}
?>