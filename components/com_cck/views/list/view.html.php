<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCKViewList extends JViewLegacy
{
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
		$preconfig['itemId']		=	'';
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
		$app			=	JFactory::getApplication();
		$config			=	JFactory::getConfig();
		$this->option	=	$app->input->get( 'option', '' );
		$this->state	=	$this->get( 'State' );
		$option			=	$this->option;
		$params			=	$app->getParams();
		$view			=	$this->getName();
		
		$limitstart		=	$this->state->get( 'limitstart' );
		$live			=	urldecode( $params->get( 'live' ) );
		$variation		=	$params->get( 'variation' );
		
		if ( $params->get( 'show_list', '' ) != '' ) {
			$preconfig['show_list']			=	(int)$params->get( 'show_list' );
		}
		$preconfig['search2']				=	$params->get( 'search2', '' );
		$preconfig['show_form']				=	$params->get( 'show_form', '' );
		$preconfig['auto_redirect']			=	$params->get( 'auto_redirect', '' );
		$preconfig['auto_redirect_vars']	=	$params->get( 'auto_redirect_vars', '' );
		$preconfig['limit']					=	$params->get( 'limit', 0 );
		$preconfig['limit2']				=	$params->get( 'limit2', 0 );
		$preconfig['limitend']				=	$params->get( 'pagination2', '' );
		$preconfig['ordering']				=	$params->get( 'ordering', '' );
		$preconfig['ordering2']				=	$params->get( 'ordering2', '' );
		
		// Page
		$menus	=	$app->getMenu();
		$menu	=	$menus->getActive();
		$home	=	( isset( $menu->home ) && $menu->home ) ? true : false;
		if ( is_object( $menu ) ) {
			$menu_params	=	new JRegistry;
			$menu_params->loadString( $menu->params );
			if ( ! $menu_params->get( 'page_title' ) ) {
				$params->set( 'page_title', $menu->title );
			}
		} else {
			$params->set( 'page_title', 'List' );
		}
		$title	=	$params->get( 'page_title' );
		
		if ( empty( $title ) ) {
			$title	=	$config->get( 'sitename' );
		} elseif ( $config->get( 'sitename_pagetitles', 0 ) == 1 ) {
			$title	=	JText::sprintf( 'JPAGETITLE', $config->get( 'sitename' ), $title );
		} elseif ( $config->get( 'sitename_pagetitles', 0 ) == 2 ) {
			$title	=	JText::sprintf( 'JPAGETITLE', $title, $config->get( 'sitename' ) );
		}
		$config		=	NULL;
		$this->document->setTitle( $title );
		
		if ( $params->get( 'menu-meta_description' ) ) {
			$this->document->setDescription( $params->get( 'menu-meta_description' ) );
		}
		if ( $params->get( 'menu-meta_keywords' ) ) {
			$this->document->setMetadata( 'keywords', $params->get('menu-meta_keywords' ) );
		}
		if ( $params->get( 'robots' ) ) {
			$this->document->setMetadata( 'robots', $params->get( 'robots' ) );
		}
		$this->pageclass_sfx	=	htmlspecialchars( $params->get( 'pageclass_sfx' ) );
		$this->raw_rendering	=	$params->get( 'raw_rendering', 0 );
		
		// Pagination
		$pagination	=	$params->get( 'show_pagination' );
		
		// Prepare
		jimport( 'cck.base.list.list' );
		include JPATH_LIBRARIES_CCK.'/base/list/list_inc.php';
		$pagination	=	$this->getModel()->_getPagination( $total_items );
		
		// Set
		if ( !is_object( @$options ) ) {
			$options	=	new JRegistry;
		}
		$this->show_form				=	$preconfig['show_form'];
		$this->show_list_title			=	$params->get( 'show_list_title' );
		if ( $this->show_list_title == '' ) {
			$this->show_list_title		=	$options->get( 'show_list_title', '1' );
			$this->tag_list_title		=	$options->get( 'tag_list_title', 'h2' );
			$this->class_list_title		=	$options->get( 'class_list_title' );
		} elseif ( $this->show_list_title ) {
			$this->tag_list_title		=	$params->get( 'tag_list_title', 'h2' );
			$this->class_list_title		=	$params->get( 'class_list_title' );
		}
		if ( $params->get( 'display_list_title', '' ) == '1' ) {
			$this->title				=	$params->get( 'title_list_title', '' );
		} elseif ( $params->get( 'display_list_title', '' ) == '0' ) {
			$this->title				=		$menu->title;
		} else {
			$this->title				=		@$search->title;
		}

		$this->show_list_desc			=	$params->get( 'show_list_desc' );
		if ( $this->show_list_desc == '' ) {
			$this->show_list_desc		=	$options->get( 'show_list_desc', '1' );
			$this->description			=	@$search->description;
		} elseif ( $this->show_list_desc ) {
			$this->description			=	$params->get( 'list_desc', @$search->description );
		} else {
			$this->description			=	'';
		}
		if ( !$total_items && !$options->get( 'show_list_desc_no_result', '1' ) ) {
			$this->show_list_desc		=	0;
			$this->description			=	'';
		}
		if ( $this->description != '' ) {
			$this->description	=	str_replace( '[title]', $menu->title, $this->description );
			$this->description	=	str_replace( '[note]', $menu->note, $this->description );
			$this->description	=	str_replace( '$cck->get', '$cck-&gt;get', $this->description );
			$this->description	=	JCckDevHelper::replaceLive( $this->description );
			if ( strpos( $this->description, '$cck-&gt;get' ) !== false ) {
				$matches	=	'';
				$regex		=	'#\$cck\-\&gt;get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_]*)\' ?\)(;)?#';
				preg_match_all( $regex, $this->description, $matches );
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $k=>$v ) {
						$fieldname			=	$matches[2][$k];
						$target				=	strtolower( $v );
						if ( count( @$doc->list ) ) {
							$this->description	=	str_replace( $matches[0][$k], current( $doc->list )->fields[$fieldname]->{$target}, $this->description );
						} else {
							$this->description	=	str_replace( $matches[0][$k], '', $this->description );
						}
					}
				}
			}
		}
		
		$this->show_items_number		=	$params->get( 'show_items_number' );
		if ( $this->show_items_number == '' ) {
			$this->show_items_number	=	$options->get( 'show_items_number', 0 );
			$this->label_items_number	=	$options->get( 'label_items_number', 'Results' );
			$this->class_items_number	=	$options->get( 'class_items_number', 'total' );
		} elseif ( $this->show_items_number ) {
			$this->label_items_number	=	$params->get( 'show_items_number_label', 'Results' );
			$this->class_items_number	=	$params->get( 'class_items_number', 'total' );
		}
		$this->show_pages_number		=	$params->get( 'show_pages_number', $options->get( 'show_pages_number', 1 ) );
		$this->show_pagination			=	$params->get( 'show_pagination' );
		$this->class_pagination			=	$params->get( 'class_pagination', 'pagination' );
		if ( $this->show_pagination == '' ) {
			$this->show_pagination		=	$options->get( 'show_pagination', 0 );
			$this->class_pagination		=	$options->get( 'class_pagination', 'pagination' );
			$this->callback_pagination	=	$options->get( 'callback_pagination', '' );
		} else {
			$this->callback_pagination	=	'';
		}
		
		// Force Titles to be hidden
		if ( $app->input->get( 'tmpl' ) == 'raw' ) {
			$params->set( 'show_page_heading', 0 );
			$this->show_list_title	=	false;
		}
		
		$this->config					=	&$config;
		$this->data						=	&$data;
		$this->filter_ajax				=	( isset( $hasAjax ) && $hasAjax ) ? true : false;
		$this->form						=	&$form;
		$this->form_id					=	$preconfig['formId'];
		$this->home						=	&$home;
		$this->items					=	&$items;
		$this->limitend					=	$config['limitend'];
		$this->pagination				=	&$pagination;
		$this->params					=	&$params;
		$this->search					=	&$search;
		$this->total					=	&$total_items;
	}
}
?>