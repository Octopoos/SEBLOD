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
class CCKViewType extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	protected $p		=	1;
	protected $vName	=	'type';
	protected $vTitle	=	_C2_TEXT;
	
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
								   'panel_height'=>'132px',
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
								   'panel_height'=>'105px',
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
		$this->uix	=	JCck::getUIX();
		
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
		
		if ( @$this->item->id > 0 ) {
			$this->isNew		=	0;
			$this->panel_class	=	'closed';
			$this->panel_style	=	'display:none; ';
			$name				=	$this->item->name;
			$app->setUserState( CCK_COM.'.edit.type.client', NULL );
		} else {
			$this->isNew		=	1;
			$this->panel_class	=	'open';
			$this->panel_style	=	'';
			$name				=	'';
			$featured			=	$this->state->get( 'skeleton_id', 0 );
			$this->item->access	=	3;
			if ( $featured == 11 ) {
				$this->item->storage_location	=	'joomla_category';
			} elseif ( $featured == 13 ) {
				$this->item->storage_location	=	'joomla_user';
			} elseif ( $featured == 14 ) {
				$this->item->storage_location	=	'joomla_user_group';
			}
		}
		$this->item->folder		=	Helper_Admin::getSelected( $this->vName, 'folder', $this->item->folder, 1 );
		$this->item->published	=	Helper_Admin::getSelected( $this->vName, 'state', $this->item->published, 1 );
		$this->item->client		=	( $this->isNew ) ? 'admin' : $this->state->get( 'client', $app->input->cookie->getString( 'cck_type'.$name.'_client', 'admin' ) );
		$this->item->master		=	( $this->item->client == 'content' || $this->item->client == 'intro' ) ? 'content' : 'form';
		$this->item->layer		=	$app->input->getString( 'layer', 'fields' );
		$P						=	'template_'.$this->item->client;
		$this->style			=	Helper_Workshop::getTemplateStyle( $this->vName, $this->item->$P, $this->state->get( 'tpl.'.$this->item->client, Helper_Workshop::getDefaultTemplate() ) );
		$this->item->template	=	$this->style->template;
		$this->insidebox		=	Helper_Admin::addInsidebox( $this->isNew );

		Helper_Admin::addToolbarEdit( $this->vName, 'COM_CCK_'._C2_TEXT, array( 'isNew'=>$this->isNew, 'folder'=>$this->state->get( 'filter.folder' ), 'checked_out'=>$this->item->checked_out ), array( 'template' => $this->style->template ) );
	}
}
?>