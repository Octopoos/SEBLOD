<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCKViewTemplate extends JCckBaseLegacyViewForm
{
	protected $vName	=	'template';
	protected $vTitle	=	_C1_TEXT;
	
	// prepareDisplay
	protected function prepareDisplay()
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
		$this->item->folder		=	Helper_Admin::getSelected( $this->vName, 'folder', $this->item->folder, 1 );
		$this->item->published	=	Helper_Admin::getSelected( $this->vName, 'state', $this->item->published, 1 );
		$this->item->published	=	(int)$this->item->published < 0 ? 1 : $this->item->published;
		$this->item->mode		=	Helper_Admin::getSelected( $this->vName, 'mode', $this->state->get( 'mode', $this->item->mode ), '0' );
		
		Helper_Admin::addToolbarEdit( $this->vName, _C1_TEXT, array( 'isNew'=>$this->isNew, 'folder'=>$this->state->get( 'filter.folder' ), 'checked_out'=>$this->item->checked_out ) );
	}
}
?>