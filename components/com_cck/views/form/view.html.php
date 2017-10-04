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
class CCKViewForm extends JViewLegacy
{
	// display
	public function display( $tpl = NULL )
	{
		$app					=	JFactory::getApplication();
		$layout					=	$app->input->get( 'tmpl' );
		$uniqId					=	'';

		if ( $layout == 'component' || $layout == 'raw' ) {
			$uniqId				=	'_'.$layout;
		}
		
		$preconfig				=	array();
		$preconfig['action']	=	'';
		$preconfig['client']	=	'site';
		$preconfig['formId']	=	'seblod_form'.$uniqId;
		$preconfig['submit']	=	'JCck.Core.submit'.$uniqId;
		$preconfig['task']		=	$app->input->get( 'task', '' );
		$preconfig['type']		=	$app->input->get( 'type', '' );
		$preconfig['url']		=	'';
		
		JCck::loadjQuery();
		$this->prepareDisplay( $preconfig );
		
		parent::display( $tpl );
	}
	
	// prepareDisplay
	protected function prepareDisplay( $preconfig )
	{
		$app				=	JFactory::getApplication();
		$config				=	JFactory::getConfig();
		$this->form			=	$this->get( 'Form' );
		$this->option		=	$app->input->get( 'option', '' );
		$this->item			=	$this->get( 'Item' );
		$this->state		=	$this->get( 'State' );
		$this->return_page	=	$app->input->getBase64( 'return' );
		$option				=	$this->option;
		$params				=	$app->getParams();
		$view				=	$this->getName();
		
		$live				=	urldecode( $params->get( 'live' ) );
		$variation			=	$params->get( 'variation' );
		
		// Page
		$menus	=	$app->getMenu();
		$menu	=	$menus->getActive();
		if ( is_object( $menu ) ) {
			$menu_params	=	new JRegistry;
			$menu_params->loadString( $menu->params );
			if ( ! $menu_params->get( 'page_title' ) ) {
				$params->set( 'page_title', $menu->title );
			}
		} else {
			$params->set( 'page_title', 'Form' );
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

		// Prepare
		jimport( 'cck.base.form.form' );
		include JPATH_SITE.'/libraries/cck/base/form/form_inc.php';
		$unique	=	$preconfig['formId'].'_'.@$type->name;
		
		if ( isset( $config['id'] ) ) {
			JFactory::getSession()->set( 'cck_hash_'.$unique, JApplication::getHash( $id.'|'.$type->name.'|'.$config['id'].'|'.$config['copyfrom_id'] ) );
		}
		JFactory::getSession()->set( 'cck_hash_'.$unique.'_context', json_encode( $config['context'] ) );

		// Set
		if ( !is_object( @$options ) ) {
			$options	=	new JRegistry;
		}
		if ( $params->get( 'display_form_title', '' ) == '2' ) {
			$this->title			=	'';

			if ( is_object( $type ) ) {
				$this->title		=	JText::_( 'APP_CCK_FORM_'.$type->name.'_TITLE_'.( ( isset( $config['isNew'] ) && $config['isNew'] ) ? 'ADD' : 'EDIT' ) );
			}
		} elseif ( $params->get( 'display_form_title', '' ) == '3' ) {
			$this->title				=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $params->get( 'title_form_title', '' ) ) ) );
		} elseif ( $params->get( 'display_form_title', '' ) == '1' ) {
			$this->title			=	$params->get( 'title_form_title', '' );
		} elseif ( $params->get( 'display_form_title', '' ) == '0' ) {
			$this->title			=		$menu->title;
		} else {
			$this->title			=	( isset( $type->title ) ) ? $type->title : '';
		}
		$this->show_form_title		=	$params->get( 'show_form_title' );
		if ( $this->show_form_title == '' ) {
			$this->show_form_title	=	$options->get( 'show_form_title', '1' );
			$this->tag_form_title	=	$options->get( 'tag_form_title', 'h1' );
			$this->class_form_title	=	$options->get( 'class_form_title', JCck::getConfig_Param( 'title_class', '' ) );
		} elseif ( $this->show_form_title ) {
			$this->tag_form_title	=	$params->get( 'tag_form_title', 'h1' );
			$this->class_form_title	=	$params->get( 'class_form_title', JCck::getConfig_Param( 'title_class', '' ) );
		}
		$this->show_form_desc		=	$params->get( 'show_form_desc' );
		if ( $this->show_form_desc == '' ) {
			$this->show_form_desc	=	$options->get( 'show_form_desc', '1' );
			$this->description		=	@$type->description;
		} elseif ( $this->show_form_desc ) {
			$this->description		=	$params->get( 'form_desc', @$type->description );
		} else {
			$this->description		=	'';
		}
		if ( $this->description != '' ) {
			if ( is_object( $menu ) ) {
				$this->description		=	str_replace( '[note]', $menu->note, $this->description );
			} else {
				$this->description		=	str_replace( '[note]', '', $this->description );
			}
		}

		// Force Titles to be hidden
		if ( $app->input->get( 'tmpl' ) == 'raw' ) {
			$params->set( 'show_page_heading', 0 );
			$this->show_form_title	=	false;
		}
		
		$this->config				=	&$config;
		$this->data					=	&$data;
		$this->form_id				=	$preconfig['formId'];
		$this->id					=	&$id;
		$this->params				=	&$params;
		$this->skip					=	( $app->input->get( 'skip' ) ) ? '1' : '0';
		$this->stage				=	&$stage;
		$this->type					=	&$type;
		$this->unique				=	&$unique;
	}
}
?>