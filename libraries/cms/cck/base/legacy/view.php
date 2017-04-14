<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class JCckBaseLegacyView extends JViewLegacy
{
	// display
	public function display( $tpl = NULL )
	{
		$app			=	JFactory::getApplication();
		$this->option	=	$app->input->get( 'option', '' );
		if ( defined( 'CCK_ADDON' ) ) {
			$this->params	=	JComponentHelper::getParams( CCK_ADDON );
		}
		
		$this->prepareSidebar();
		$this->prepareToolbar();
		$this->prepareUI();
		$this->completeUI();

		parent::display( $tpl );
	}
	
	// prepareSidebar
	protected function prepareSidebar()
	{
		$this->sidebar	=	JHtmlSidebar::render();

		if ( strlen( $this->sidebar ) < 100 ) {
			$sidebar    =   str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $this->sidebar );
			if ( $sidebar == '<div id="sidebar"><div class="sidebar-nav"></div></div>' ) {
				$this->sidebar	=	'';
			}
		}
	}

	// prepareToolbar
	protected function prepareToolbar()
	{
		$canDo	=	Helper_Admin::getActions();
		
		JToolBarHelper::title( CCK_LABEL, 'cck-seblod' );
		
		if ( $canDo->get( 'core.admin' ) ) {
			JToolBarHelper::preferences( CCK_ADDON, 560, 840, 'JTOOLBAR_OPTIONS' );
		}
		
		Helper_Admin::addToolbarSupportButton();
	}
	
	// completeUI
	public function completeUI()
	{
	}

	// prepareUI
	protected function prepareUI()
	{
		$this->css	=	array( 'items'=>'seblod-manager',
							   'table'=>'table table-striped',
							   'w33'=>'span4',
							   'w50'=>'span6',
							   'w66'=>'span8',
							   'w100'=>'span12',
							   'wrapper'=>'row-fluid'
						);
	}
}
?>