<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCKViewBox extends JViewLegacy
{
	protected $item;
	protected $state;
	
	// display
	function display( $tpl = NULL )
	{
		$app				=	JFactory::getApplication();
		$this->item			=	new stdClass;
		$this->state		=	$this->get( 'State' );
		$this->option		=	$app->input->get( 'option', '' );
		
		$this->file			=	$this->state->get( 'file', '' );
		$this->item->id		=	$this->state->get( 'bx.id', '' );
		$this->item->name	=	$this->state->get( 'bx.name', '' );
		$this->item->alt	=	$this->state->get( 'alt', 0 );
		
		if ( $this->getLayout() != 'raw' ) {
			$this->function	=	$this->state->get( 'function', '' );
			
			$this->item->title	=	$this->state->get( 'bx.title', '' );
			$this->item->type	=	$this->state->get( 'bx.type', '' );
			$this->item->params	=	$this->state->get( 'bx.params', '' );
		
			$this->doValidation	=	$this->state->get( 'validation', 0 );
		}
		
		$this->css		=	array( 'items'=>'seblod-manager',
								   'table'=>'table table-striped',
								   'wrapper_tmpl'=>'span12'
								);
		
		JFactory::getApplication()->input->set( 'hidemainmenu', true );
		
		parent::display( $tpl );
	}
	
	// onceFile
	function onceFile( $method, &$config = array(), $file = '' )
	{
		if ( ! $file ) {
			$file	=	$this->file;
		}
		
		if ( $file ) {
			$path	=	JPATH_SITE.'/'.$file;
			if ( is_file( $path ) ) {
				include_once $path;
			}
		}	
	}
}
?>