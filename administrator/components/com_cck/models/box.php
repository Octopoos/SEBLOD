<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: box.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modeladmin' );

// Model
class CCKModelBox extends JModelAdmin
{
	protected $text_prefix	=	'COM_CCK';
	
	// getForm
	public function getForm( $data = array(), $loadData = true )
	{
		return;
	}
	
	// populateState
	protected function populateState()
	{
		$app	=	JFactory::getApplication( 'administrator' );
		
		if ( $alt = (string)$app->getUserState( CCK_COM.'.add.box.alt' ) ) {
			$this->setState( 'alt', $alt );
		}
		if ( $validation = (string)$app->getUserState( CCK_COM.'.add.box.validation' ) ) {
			$this->setState( 'validation', $validation );
		}
		if ( $file = (string)$app->getUserState( CCK_COM.'.add.box.file' ) ) {
			$this->setState( 'file', $file );
		}
		if ( $function = (string)$app->getUserState( CCK_COM.'.add.box.function' ) ) {
			$this->setState( 'function', $function );
		}
		if ( $box_id = (string)$app->getUserState( CCK_COM.'.add.box.bx.id' ) ) {
			$this->setState( 'bx.id', $box_id );
		}
		if ( $box_name = (string)$app->getUserState( CCK_COM.'.add.box.bx.name' ) ) {
			$this->setState( 'bx.name', $box_name );
		}
		if ( $box_title = (string)$app->getUserState( CCK_COM.'.add.box.bx.title' ) ) {
			$this->setState( 'bx.title', $box_title );
		}
		if ( $box_type = (string)$app->getUserState( CCK_COM.'.add.box.bx.type' ) ) {
			$this->setState( 'bx.type', $box_type );
		}
		if ( $box_params = (string)$app->getUserState( CCK_COM.'.add.box.bx.params' ) ) {
			$this->setState( 'bx.params', $box_params );
		}
	}
}
?>
