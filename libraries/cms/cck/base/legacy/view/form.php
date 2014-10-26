<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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
	
	// prepareUI
	public function prepareUI()
	{
		if ( JCck::on() ) {
			$this->css		=	array( 'w30'=>'span4',
									   'w70'=>'span8',
									   'wrapper'=>'container',
									   'wrapper2'=>'row-fluid',
									   'wrapper_tmpl'=>'span'
								);
		} else {
			$this->css		=	array( 'w30'=>'width-30',
									   'w70'=>'width-70 fltlft',
									   'wrapper'=>'sebwrapper',
									   'wrapper2'=>'seb-wrapper',
									   'wrapper_tmpl'=>'width-100 bg-dark fltlft'
								);
		}
	}
	
	// prepareToolbar
	public function prepareToolbar()
	{
	}
}
?>