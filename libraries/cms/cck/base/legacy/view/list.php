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
class JCckBaseLegacyViewList extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	// display
	public function display( $tpl = NULL )
	{
		if ( $this->getlayout() == 'element' ) {
			$this->prepareDisplay();
		} else {
			$this->prepareDisplay();
			$this->prepareBatch();
		}
		
		if ( count( $errors = $this->get( 'Errors' ) ) ) {
			JError::raiseError( 500, implode( "\n", $errors ) );
			return false;
		}
		
		$this->prepareToolbar();
		$this->prepareUI();
		
		parent::display( $tpl );
	}

	// getSortFields
	protected function getSortFields()
	{
		return array(
					'a.id'=>JText::_( 'COM_CCK_ID' ),
					'title'=>JText::_( 'COM_CCK_TITLE' )
				);
	}
	
	// prepareBatch
	public function prepareBatch()
	{
	}
	
	// prepareDisplay
	public function prepareDisplay()
	{
		$app				=	JFactory::getApplication();
		$this->items		=	$this->get( 'Items' );
		$this->option		=	$app->input->get( 'option', '' );
		$this->pagination	=	$this->get( 'Pagination' );
		$this->state		=	$this->get( 'State' );
	}
	
	// prepareUI
	public function prepareUI()
	{
		if ( JCck::on() ) {
			$this->css		=	array( 'batch'=>'modal modal-small hide fade',
									   'filter'=>'btn-toolbar',
									   'filter_search'=>'filter-search btn-group pull-left hidden-phone input-append',
									   'filter_search_button'=>'tip hasTooltip',
									   'filter_search_buttons'=>'btn-group pull-left hidden-phone',
									   'filter_search_list'=>'pull-right hidden-phone',
									   'filter_select'=>'filter-select hidden-phone hidden-important',
									   'items'=>'seblod-manager clearfix',
									   'joomla3'=>' hide',
									   'table'=>'table table-striped',
									   'w50'=>'span6',
									   'wrapper'=>'row-fluid'
								);
			$this->html		=	array( 'filter_select_header'=>'<h4 class="page-header">'.JText::_( 'JSEARCH_FILTER_LABEL' ).'</h4>',
									   'filter_select_separator'=>'<hr class="hr-condensed" />'
								);
			$this->js		=	array( 'filter'=>'jQuery(document).ready(function($) { $("#sidebar div.sidebar-nav").append("<hr />"); $("div.filter-select").appendTo("#sidebar div.sidebar-nav").removeClass("hidden-important");'
												.'var w = $("div.sidebar-nav").width()-28; $("div.filter-select,div.sidebar-nav div.chzn-container").css("width",w+"px"); $("div.sidebar-nav div.chzn-drop").css("width",(w)+"px");  $("div.sidebar-nav div.chzn-search > input").css("width",(w-10)+"px"); });'
								);
			$this->sidebar	=	JHtmlSidebar::render();
		} else {
			$this->css		=	array( 'batch'=>'seblod',
									   'filter'=>'seblod first',
									   'filter_search'=>'filter-search fltlft',
									   'filter_search_button'=>'inputbutton',
									   'filter_search_buttons'=>'filter-search fltlft',
									   'filter_search_list'=>'hide',
									   'filter_select'=>'filter-select fltrt',
									   'items'=>'seblod',
									   'joomla3'=>'',
									   'table'=>'adminlist',
									   'w50'=>'width-50 fltlft',
									   'wrapper'=>'sebwrapper'
								);
			$this->html		=	array( 'filter_select_header'=>'',
									   'filter_select_separator'=>''
								);
			$this->js		=	array( 'filter'=>''
								);
		}
	}
	
	// prepareToolbar
	public function prepareToolbar()
	{
		Helper_Admin::addToolbar( $this->vName, $this->vTitle );
	}
}
?>