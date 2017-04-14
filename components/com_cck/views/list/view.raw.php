<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.xml.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
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
		$preconfig					=	array();
		$preconfig['action']		=	'';
		$preconfig['client']		=	'search';
		$preconfig['search']		=	$app->input->get( 'search', '' );
		$preconfig['itemId']		=	'';
		$preconfig['task']			=	$app->input->get( 'task', 'search' );
		$preconfig['doPagination']	=	1;
		$preconfig['formId']		=	'seblod_form';
		$preconfig['submit']		=	'JCck.Core.submit';
		
		JCck::loadjQuery();
		$this->prepareDisplay( $preconfig );
		
		parent::display( $tpl );
	}
	
	// prepareDisplay
	protected function prepareDisplay( $preconfig )
	{
		$app			=	JFactory::getApplication();
		$this->option	=	$app->input->get( 'option', '' );
		$this->state	=	$this->get( 'State' );
		$option			=	$this->option;
		$params			=	$app->getParams();
		$view			=	$this->getName();

		$limitstart		=	$this->state->get( 'limitstart' );
		$live			=	urldecode( $params->get( 'live' ) );
		$variation		=	$params->get( 'variation' );
		
		$preconfig['show_form']		=	$params->get( 'show_form', '' );
		$preconfig['auto_redirect']	=	$params->get( 'auto_redirect', '' );
		$preconfig['limit2']		=	$params->get( 'limit2', 0 );
		$preconfig['ordering']		=	$params->get( 'ordering', '' );
		$preconfig['ordering2']		=	$params->get( 'ordering2', '' );
		
		// Prepare
		jimport( 'cck.base.list.list' );
		include JPATH_SITE.'/libraries/cck/base/list/list_inc.php';
		
		// Set
		$this->data		=	&$data;
		$this->setLayout( 'raw' );
	}
}
?>