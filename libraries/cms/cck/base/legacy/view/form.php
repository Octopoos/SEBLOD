<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class JCckBaseLegacyViewForm extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	
	// display
	public function display( $tpl = NULL )
	{
		if ( $this->getlayout() == 'delete' ) {
			$this->prepareDelete();
		} elseif ( $this->getLayout() == 'edit' || $this->getLayout() == 'edit2' || $this->getLayout() == 'process' ) {
			$this->prepareDisplay();
		}

		$this->prepareToolbar();
		$this->prepareUI();
		$this->completeUI();
		
		parent::display( $tpl );
	}
	
	// prepareDelete
	public function prepareDelete()
	{
		Helper_Admin::addToolbarDelete( $this->vName, $this->vTitle );
	}
	
	// prepareDisplay
	public function prepareDisplay()
	{
	}
	
	// completeUI
	public function completeUI()
	{
		$title	=	( ( is_object( $this->item ) && $this->item->title != '' ) ? '"'.$this->item->title.'"' : JText::_( 'COM_CCK_ADD_NEW' ) ).' '.JText::_( 'COM_CCK_'.$this->vName );
		
		$this->document->setTitle( $title );
	}

	// prepareUI
	public function prepareUI()
	{
		$this->css		=	array( 'w30'=>'span4',
								   'w70'=>'span8',
								   'wrapper'=>'container',
								   'wrapper2'=>'row-fluid',
								   'wrapper_tmpl'=>'span12'
							);
	}
	
	// prepareToolbar
	public function prepareToolbar()
	{
	}
}
?>