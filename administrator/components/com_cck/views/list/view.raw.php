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

//JLoader::register( 'CCKControllerForm', JPATH_ADMINISTRATOR.'/components/com_cck/controllers/form.php' );

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
		$variation					=	'';

		$preconfig['show_form']		=	'';
		$preconfig['auto_redirect']	=	'';
		$preconfig['limit2']		=	0;
		$preconfig['ordering']		=	'';
		$preconfig['ordering2']		=	'';
		
		// Prepare
		jimport( 'cck.base.list.list' );
		include JPATH_SITE.'/libraries/cck/base/list/list_inc.php';
		
		// Set
		$this->data					=	&$data;

		$this->setLayout( 'raw' );
	}
}
?>