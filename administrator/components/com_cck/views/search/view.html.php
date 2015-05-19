<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

ini_set( 'memory_limit', '512M' );
require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_workshop.php';

// View
class CCKViewSearch extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	protected $p		=	1;
	protected $vName	=	'search';
	protected $vTitle	=	_C4_TEXT;
	
	// display
	function display( $tpl = null )
	{
		switch ( $this->getlayout() ) {
			case 'delete':
				$this->prepareDelete();
				break;
			case 'edit':
			case 'error':
				$this->prepareDisplay();
				break;
			case 'edit2':
				$this->prepareDisplay();
				$this->prepareDisplay_Ajax();
				break;
			default:
				break;
		}
		
		if ( JCck::on() ) {
			$this->css	=	array( '_'=>'',
								   'panel_height'=>'89px',
								   'w30'=>'span4',
								   'w70'=>'span8',
								   'wrapper'=>'container',
								   'wrapper2'=>'row-fluid',
								   'wrapper_tmpl'=>'span'
							);
			$this->js	=	array( '_'=>'',
								   'tooltip'=>'$(".hasTooltip").tooltip({});'
							);
		} else {
			$this->css	=	array( '_'=>'',
								   'panel_height'=>'65px',
								   'w30'=>'width-30',
								   'w70'=>'width-70 fltlft',
								   'wrapper'=>'sebwrapper',
								   'wrapper2'=>'seb-wrapper workshop',
								   'wrapper_tmpl'=>'width-100 bg-dark fltlft'
							);
			$this->js	=	array( '_'=>'',
								   'tooltip'=>''
							);
		}
		$this->uix	=	'full';
		
		parent::display( $tpl );
	}
	
	// prepareDelete
	function prepareDelete()
	{		
		Helper_Admin::addToolbarDelete( $this->vName, 'COM_CCK_'.$this->vTitle );
	}
	
	// prepareDisplay
	function prepareDisplay()
	{
		$app			=	JFactory::getApplication();
		$this->form		=	$this->get( 'Form' );
		$this->item		=	$this->get( 'Item' );
		$this->option	=	$app->input->get( 'option', '' );
		$this->state	=	$this->get( 'State' );
		
		// Check Errors
		if ( count( $errors	= $this->get( 'Errors' ) ) ) {
			JError::raiseError( 500, implode( "\n", $errors ) );
			return false;
		}
		
		$this->item->cck_type	=	$this->state->get( 'content_type', '' );
		$this->item->skip		=	$this->state->get( 'skip' );
		if ( @$this->item->id > 0 ) {
			$this->isNew		=	0;
			$this->panel_class	=	'closed';
			$this->panel_style	=	'display:none; ';
			$name				=	$this->item->name;
			$app->setUserState( CCK_COM.'.edit.search.client', NULL );
		} else {
			$this->isNew		=	1;
			$this->panel_class	=	'open';
			$this->panel_style	=	'';
			$name				=	'';
			if ( $this->item->cck_type != '' ) {
				$this->item->storage_location	=	JCckDatabase::loadResult( 'SELECT storage_location FROM #__cck_core_types WHERE name = "'.$this->item->cck_type.'"' );
				if ( $this->item->storage_location == 'none' ) {
					$this->item->storage_location	=	'';
				}
			}
			$this->tpl_list		=	$this->state->get( 'tpl.list' );
		}
		$this->item->folder		=	Helper_Admin::getSelected( $this->vName, 'folder', $this->item->folder, 1 );
		$this->item->published	=	Helper_Admin::getSelected( $this->vName, 'state', $this->item->published, 1 );
		if ( $this->item->skip != '' ) {
			$this->item->client	=	$this->item->skip;
			$this->item->master	=	( $this->item->client == 'list' || $this->item->client == 'item' ) ? 'content' : ( ( $this->item->client == 'order' ) ? 'order' : 'search' );
			$this->item->layer	=	$app->input->getString( 'layer', 'fields' );
			$P					=	'template_'.$this->item->client;
			$force_template		=	( $this->item->client == 'list' ) ? $this->state->get( 'tpl.list' ) : Helper_Workshop::getDefaultTemplate();
		} else {
			$this->item->client	=	( $this->isNew ) ? 'search' : $this->state->get( 'client', $app->input->cookie->getString( 'cck_search'.$name.'_client', 'search' ) );
			$this->item->master	=	( $this->item->client == 'list' || $this->item->client == 'item' ) ? 'content' : ( ( $this->item->client == 'order' ) ? 'order' : 'search' );
			$this->item->layer	=	$app->input->getString( 'layer', 'fields' );
			$P					=	'template_'.$this->item->client;
			$force_template		=	( $this->item->client == 'list' ) ? '' : Helper_Workshop::getDefaultTemplate();
		}
		$this->style			=	( $this->item->client != 'order' ) ? Helper_Workshop::getTemplateStyle( $this->vName, $this->item->$P, $this->state->get( 'tpl.'.$this->item->client, $force_template ) ) : '';
		$this->item->template	=	( isset( $this->style->template ) ) ? $this->style->template : '';
		$this->insidebox		=	Helper_Admin::addInsidebox( $this->isNew );
		
		Helper_Admin::addToolbarEdit( $this->vName, 'COM_CCK_'._C4_TEXT, array( 'isNew'=>$this->isNew, 'folder'=>$this->state->get( 'filter.folder' ), 'checked_out'=>$this->item->checked_out ), array( 'template' => $this->item->template ) );
	}
}
?>