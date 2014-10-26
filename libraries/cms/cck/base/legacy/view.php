<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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
		
		parent::display( $tpl );
	}
	
	// prepareSidebar
	protected function prepareSidebar()
	{
		if ( JCck::on() ) {
			$this->sidebar	=	JHtmlSidebar::render();
			if ( strlen( $this->sidebar ) < 100 ) {
				$sidebar    =   str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $this->sidebar );
				if ( $sidebar == '<div id="sidebar"><div class="sidebar-nav"></div></div>' ) {
					$this->sidebar	=	'';
				}
			}
		}
	}

	// prepareToolbar
	protected function prepareToolbar()
	{
		$canDo	=	Helper_Admin::getActions();
		
		if ( JCck::on() ) {
			JToolBarHelper::title( CCK_LABEL, 'cck-seblod' );
		} else {
			JToolBarHelper::title( '&nbsp;', 'seblod.png' );
		}
		if ( $canDo->get( 'core.admin' ) ) {
			JToolBarHelper::preferences( CCK_ADDON, 560, 840, 'JTOOLBAR_OPTIONS' );
		}
		
		Helper_Admin::addToolbarSupportButton();
	}
	
	// prepareUI
	protected function prepareUI()
	{
		if ( JCck::on() ) {
			$this->css		=	array( 'items'=>'seblod-manager',
									   'table'=>'table table-striped',
									   'w33'=>'span4',
									   'w50'=>'span6',
									   'w66'=>'span8',
									   'w100'=>'span12',
									   'wrapper'=>'row-fluid'
								);
		} else {
			$this->css		=	array( 'items'=>'seblod',
									   'table'=>'adminlist',
									   'w33'=>'width-30',
									   'w50'=>'width-50 fltlft',
									   'w66'=>'width-100',
									   'w100'=>'width-100',
									   'wrapper'=>'sebwrapper'
								);
		}
	}
}
?>