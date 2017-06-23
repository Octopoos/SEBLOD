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

require_once JPATH_COMPONENT.'/helpers/helper_session.php';

// View
class CCKViewSession extends JCckBaseLegacyViewForm
{
	protected $vName	=	'session';
	protected $vTitle	=	_C8_TEXT;
	
	// prepareDisplay
	function prepareDisplay()
	{
		$app			=	JFactory::getApplication();
		$model 			=	$this->getModel();
		$this->form		=	$this->get( 'Form' );
		$this->item		=	$this->get( 'Item' );
		$this->option	=	$app->input->get( 'option', '' );
		
		Helper_Session::loadExtensionLang( $this->item->extension );
		Helper_Session::loadExtensionLang( $this->item->type );
		
		// Check Errors
		if ( count( $errors	= $this->get( 'Errors' ) ) ) {
			throw new Exception( implode( "\n", $errors ), 500 );
		}
		
		$this->isNew			=	( @$this->item->id > 0 ) ? 0 : 1;
		
		Helper_Admin::addToolbarEdit( $this->vName, _C8_TEXT, array( 'isNew'=>$this->isNew, 'folder'=>0, 'checked_out'=>'' ) );
	}
}
?>