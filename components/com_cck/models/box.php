<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: box.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modelitem' );

// Model
class CCKModelBox extends JModelItem
{
	// getForm
	public function getForm( $data = array(), $loadData = true )
	{
		return;
	}
	
	// populateState
	protected function populateState()
	{
		$app	=	JFactory::getApplication();
		
		if ( $validation = (string)$app->getUserState( 'com_cck.add.box.validation' ) ) {
			$this->setState( 'validation', $validation );
		}
		if ( $file = (string)$app->getUserState( 'com_cck.add.box.file' ) ) {
			$this->setState( 'file', $file );
		}
		if ( $function = (string)$app->getUserState( 'com_cck.add.box.function' ) ) {
			$this->setState( 'function', $function );
		}
		if ( $box_id = (string)$app->getUserState( 'com_cck.add.box.bx.id' ) ) {
			$this->setState( 'bx.id', $box_id );
		}
		if ( $box_name = (string)$app->getUserState( 'com_cck.add.box.bx.name' ) ) {
			$this->setState( 'bx.name', $box_name );
		}
		if ( $box_title = (string)$app->getUserState( 'com_cck.add.box.bx.title' ) ) {
			$this->setState( 'bx.title', $box_title );
		}
		if ( $box_type = (string)$app->getUserState( 'com_cck.add.box.bx.type' ) ) {
			$this->setState( 'bx.type', $box_type );
		}
		if ( $box_params = (string)$app->getUserState( 'com_cck.add.box.bx.params' ) ) {
			$this->setState( 'bx.params', $box_params );
		}
	}
}
?>
