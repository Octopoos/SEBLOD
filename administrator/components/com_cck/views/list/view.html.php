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

require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/helper_admin.php';

// View
class CCKViewList extends JViewLegacy
{
	protected $vName	=	'list';
	
	// display
	public function display( $tpl = NULL )
	{
		$app						=	JFactory::getApplication();
		$layout						=	$app->input->get( 'tmpl' );
		$uniqId						=	'';

		if ( $layout == 'component' || $layout == 'raw' ) {
			$uniqId					=	'_'.$layout;
		}

		$preconfig					=	array();
		$preconfig['action']		=	'';
		$preconfig['client']		=	'search';
		$preconfig['search']		=	$app->input->get( 'search', '' );
		$preconfig['itemId']		=	0;
		$preconfig['task']			=	$app->input->get( 'task', 'search' );
		$preconfig['doPagination']	=	1;
		$preconfig['formId']		=	'seblod_form'.$uniqId;
		$preconfig['submit']		=	'JCck.Core.submit'.$uniqId;
		
		JCck::loadjQuery();
		$this->prepareDisplay( $preconfig );
		
		parent::display( $tpl );
	}
	
	// prepareDisplay
	protected function prepareDisplay( $preconfig )
	{
		$app						=	JFactory::getApplication();
		$this->option				=	$app->input->get( 'option', '' );
		$this->state				=	$this->get( 'State' );
		$option						=	$this->option;
		$params						=	new JRegistry;
		$view						=	$this->getName();
		
		$limitstart					=	$this->state->get( 'limitstart' );
		$live						=	'';
		$order_by					=	'';
		$variation					=	'';

		$preconfig['show_form']		=	'';
		$preconfig['auto_redirect']	=	'';
		$preconfig['limit2']		=	0;
		$preconfig['ordering']		=	'';
		$preconfig['ordering2']		=	'';
		
		// Prepare
		jimport( 'cck.base.list.list' );
		include JPATH_SITE.'/libraries/cck/base/list/list_inc.php';
		$pagination					=	$this->getModel()->_getPagination( $total_items );
		
		// Set
		if ( !is_object( @$options ) ) {
			$options	=	new JRegistry;
		}
		$this->show_list_title		=	$options->get( 'show_list_title', '1' );
		$this->tag_list_title		=	$options->get( 'tag_list_title', 'h2' );
		$this->class_list_title		=	$options->get( 'class_list_title' );
		$this->show_list_desc		=	$options->get( 'show_list_desc', '1' );
		$this->description			=	@$search->description;
		$this->show_items_number	=	$options->get( 'show_items_number', 0 );
		$this->label_items_number	=	$options->get( 'label_items_number', 'Results' );
		$this->class_items_number	=	$options->get( 'class_items_number', 'total' );
		$this->show_pages_number	=	$options->get( 'show_pages_number', 1 );
		$this->show_pagination		=	(int)$options->get( 'show_pagination', 0 );
		$this->class_pagination		=	$options->get( 'class_pagination', 'pagination' );
		$this->pageclass_sfx		=	'';
		
		$this->config				=	&$config;
		$this->data					=	&$data;
		$this->form					=	&$form;
		$this->form_id				=	$preconfig['formId'];
		$this->items				=	&$items;
		$this->pagination			=	&$pagination;
		$this->params				=	&$params;
		$this->search				=	&$search;
		$this->total				=	&$total;
		
		$this->addToolbar( $search );
	}
	
	// addToolbar
	protected function addToolbar( $search )
	{
		$bar		=	JToolBar::getInstance( 'toolbar' );
		$canDo		=	Helper_Admin::getActions();
		$separator	=	false;
		$title		=	empty( $search->title ) ? 'List' : $search->title;
		$user		=	JFactory::getUser();
		
		require_once JPATH_COMPONENT.'/helpers/toolbar/link.php';
		require_once JPATH_COMPONENT.'/helpers/toolbar/separator.php';
		
		JToolBarHelper::title( $title, 'stack' );
		
		if ( !( is_object( $search ) && $search->id ) ) {
			return;
		}
		$form			=	JCckDatabase::loadResult( 'SELECT live_value FROM #__cck_core_search_field WHERE fieldid = 1 AND searchid = '.$search->id.' AND stage = 0' );
		if ( $canDo->get( 'core.create' ) || $canDo->get( 'core.edit' ) ) {
			$form		=	JCckDatabase::loadObject( 'SELECT id, name, location FROM #__cck_core_types WHERE name = "'.$form.'"' );
			if ( is_object( $form ) ) {
				$canCreate	=	$user->authorise( 'core.create', 'com_cck.form.'.$form->id );
				$creation	=	( !$form->location || $form->location == 'admin' ) ? true : false;
			} else {
				$canCreate	=	false;
				$creation	=	false;
			}
			if ( $canCreate && $creation ) {
				$link		=	'index.php?option=com_cck&view=form&type='.$form->name.'&return_o=cck&return_v=list&return='.base64_encode( JUri::getInstance()->toString() );
				$bar->prependButton( 'CckLink', 'new', JText::_( 'JTOOLBAR_NEW' ), $link, '_self' );
			}
		}
	}
}
?>