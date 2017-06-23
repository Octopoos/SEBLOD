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
class CCKViewFolder extends JCckBaseLegacyViewForm
{
	protected $vName	=	'folder';
	protected $vTitle	=	_C0_TEXT;
	
	// prepareDisplay
	function prepareDisplay()
	{
		$app			=	JFactory::getApplication();
		$this->form		=	$this->get( 'Form' );
		$this->option	=	$app->input->get( 'option', '' );
		$this->item		=	$this->get( 'Item' );
		$this->state	=	$this->get( 'State' );
		
		// Check Errors
		if ( count( $errors	= $this->get( 'Errors' ) ) ) {
			throw new Exception( implode( "\n", $errors ), 500 );
		}
		
		$this->isNew			=	( @$this->item->id > 0 ) ? 0 : 1;
		$this->item->parent_id	=	Helper_Admin::getSelected( $this->vName, 'folder', Helper_Folder::getParent( $this->item->id ), 2 );
		$this->item->parent_db	=	( ! $this->isNew ) ? $this->item->parent_id : null;
		$this->insidebox		=	Helper_Admin::addInsidebox( $this->isNew );
				
		Helper_Admin::addToolbarEdit( $this->vName, 'COM_CCK_'._C0_TEXT, array( 'isNew'=>$this->isNew, 'folder'=>$this->state->get( 'filter.folder' ), 'checked_out'=>$this->item->checked_out ) );
	}
	
	// prepareDelete
	function prepareDelete()
	{
		Helper_Admin::addToolbarDelete( $this->vName, 'COM_CCK_'.$this->vTitle );
	}
}
?>