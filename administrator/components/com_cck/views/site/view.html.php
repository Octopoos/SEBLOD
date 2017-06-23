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
class CCKViewSite extends JCckBaseLegacyViewForm
{
	protected $vName	=	'site';
	protected $vTitle	=	_C5_TEXT;
	
	// prepareDisplay
	function prepareDisplay()
	{
		$app			=	JFactory::getApplication();
		$model 			=	$this->getModel();
		$this->form		=	$this->get( 'Form' );
		$this->item		=	$this->get( 'Item' );
		$this->option	=	$app->input->get( 'option', '' );
		$this->state	=	$this->get( 'State' );
		
		// Check Errors
		if ( count( $errors	= $this->get( 'Errors' ) ) ) {
			throw new Exception( implode( "\n", $errors ), 500 );
		}
		
		$this->isNew			=	( @$this->item->id > 0 ) ? 0 : 1;
		$this->item->published	=	Helper_Admin::getSelected( $this->vName, 'state', $this->item->published, 1 );
		$this->item->type		=	$this->state->get( 'type', '2,7' );
		$this->item->fields		=	JCck::getConfig_Param( 'multisite_options', array() );
		$this->item->options	=	( $this->item->options ) ? JCckDev::fromJSON( $this->item->options ) : array();

		Helper_Admin::addToolbarEdit( $this->vName, _C5_TEXT, array( 'isNew'=>$this->isNew, 'folder'=>0, 'checked_out'=>$this->item->checked_out ) );
	}
}
?>