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
		$app	=	JFactory::getApplication();
		
		if ( $this->getlayout() != 'select' ) {
			$layout					=	$app->input->get( 'tmpl' );
			$uniqId					=	'';

			if ( $layout == 'component' || $layout == 'raw' ) {
				$uniqId				=	'_'.$layout;
			}

			$preconfig				=	array();
			$preconfig['action']	=	'';
			$preconfig['client']	=	'admin';
			$preconfig['formId']	=	'seblod_form'.$uniqId;
			$preconfig['submit']	=	'JCck.Core.submit'.$uniqId;
			$preconfig['task']		=	$app->input->get( 'task', '' );
			$preconfig['type']		=	$app->input->get( 'type', '' );
			$preconfig['url']		=	JUri::getInstance()->toString();
			
			JCck::loadjQuery();
			Helper_Include::addStyleSheets( false );
			$this->prepareDisplay( $preconfig );
		}
		
		parent::display( $tpl );
	}
	
	// prepareDisplay
	protected function prepareDisplay( $preconfig )
	{
		if ( JCck::getConfig_Param( 'debug', 0 ) ) {
			jimport( 'joomla.error.profiler' );
			$profiler	=	new JProfiler();
		}
		
		$app			=	JFactory::getApplication();
		$this->form		=	$this->get( 'Form' );
		$this->option	=	$app->input->get( 'option', '' );
		$this->item		=	$this->get( 'Item' );
		$this->state	=	$this->get( 'State' );
		$option			=	$this->option;
		$params			=	new JRegistry;
		$view			=	$this->getName();
		
		$isNew			=	1;
		$live			=	'';
		$lives			=	array();
		$variation		=	'';
		
		jimport( 'cck.base.form.form' );
		include_once JPATH_SITE.'/libraries/cck/base/form/form_inc.php';
		if ( isset( $config['id'] ) ) {
			JFactory::getSession()->set( 'cck_hash_seblod_form', JApplication::getHash( $id.'|'.$type->name.'|'.$config['id'].'|'.$config['copyfrom_id'] ) );
		}
		
		$this->config	=	&$config;
		$this->data		=	&$data;
		$this->form_id	=	$preconfig['formId'];
		$this->id		=	&$id;
		$this->isNew	=	&$isNew;
		$this->params	=	&$params;
		$this->stage	=	&$stage;
		$this->type		=	&$type;
		
		$title			=	( isset( $type->title ) ) ? $type->title : '';
		$name			=	( isset( $type->name ) ) ? $type->name : '';
		$this->addToolbar( $title, $name );
	}
	
	// addToolbar
	protected function addToolbar( $title = '', $name = '' )
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', true );
		
		$bar	=	JToolBar::getInstance( 'toolbar' );
		$lang	=	JFactory::getLanguage();
		
		require_once JPATH_COMPONENT.'/helpers/toolbar/link.php';
		
		if ( $this->isNew )  {
			$key	=	'APP_CCK_FORM_'.$name.'_TITLE_ADD';
			if ( $lang->hasKey( $key ) == 1 ) {
				$title	=	JText::_( $key );
			} else {
				$key	=	'COM_CCK_TITLE_FORM_ADD_'.str_replace( ' ', '_', $title );
				$title	=	( $lang->hasKey( $key ) == 1 ) ? JText::_( $key ) : JText::_( 'COM_CCK_TITLE_ADD' ).' '.$title;
			}
			JToolBarHelper::title( $title, 'pencil-2' );
			$bar->prependButton( 'CckLink', 'cancel', 'JTOOLBAR_CANCEL', 'javascript:JCck.Core.submit(\'form.cancel\');' );
		} else {
			$key	=	'APP_CCK_FORM_'.$name.'_TITLE_EDIT';
			if ( $lang->hasKey( $key ) == 1 ) {
				$title	=	JText::_( $key );
			} else {
				$key	=	'COM_CCK_TITLE_FORM_EDIT_'.str_replace( ' ', '_', $title );
				$title	=	( $lang->hasKey( $key ) == 1 ) ? JText::_( $key ) : JText::_( 'COM_CCK_TITLE_EDIT' ).' '.$title;
			}
			JToolBarHelper::title( $title, 'pencil-2' );
			$bar->prependButton( 'CckLink', 'cancel', 'JTOOLBAR_CLOSE', 'javascript:JCck.Core.submit(\'form.cancel\');' );
		}
		$bar->prependButton( 'CckLink', 'save-new', 'JTOOLBAR_SAVE_AND_NEW', 'javascript:JCck.Core.submit(\'form.save2new\');' );
		$bar->prependButton( 'CckLink', 'save', 'JTOOLBAR_SAVE', 'javascript:JCck.Core.submit(\'form.save\');' );
		$bar->prependButton( 'CckLink', 'apply', 'JTOOLBAR_APPLY', 'javascript:JCck.Core.submit(\'form.apply\');' );
	}
}
?>