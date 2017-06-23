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
class CCKViewVersion extends JCckBaseLegacyViewForm
{
	protected $vName	=	'version';
	protected $vTitle	=	_C6_TEXT;
	
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
		$type					=	$this->item->e_type ? $this->item->e_type : 'type';
		$this->item->title		=	JCckDatabase::loadResult( 'SELECT title FROM #__cck_core_'.$type.'s WHERE id = '.(int)$this->item->e_id );
		
		Helper_Admin::addToolbarEdit( $this->vName, _C6_TEXT, array( 'isNew'=>$this->isNew, 'folder'=>0, 'checked_out'=>$this->item->checked_out ) );
	}
}
?>