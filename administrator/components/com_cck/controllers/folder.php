<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: folder.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

jimport( 'joomla.application.component.controllerform' );

// Controller
class CCKControllerFolder extends FormController
{
	protected $text_prefix	=	'COM_CCK';
		
	// export
	public function export()
	{
		Session::checkToken( 'get' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app			=	Factory::getApplication();
		$model			=	$this->getModel();
		$recordId		=	$app->input->getInt( 'id', 0 );
		$elements		=	$app->input->getString( 'elements', '' );
		$elements		=	array_flip( explode( ',', $elements ) );
		$dependencies	=	array();
		$menu			=	$app->input->getInt( 'dep_menu', 0 );
		$options		=	$app->input->get( 'options', array(), 'array' );
		if ( $app->input->getInt( 'dep_categories', 0 ) ) {
			$dependencies['categories']	=	1;
		}
		if ( $menu ) {
			$dependencies['menu']		=	$menu;
		}
		if ( $file = $model->prepareExport( $recordId, $elements, $dependencies, $options ) ) {
			$file	=	JCckDevHelper::getRelativePath( $file, false );
			$this->setRedirect( Uri::base().'index.php?option=com_cck&task=download&file='.$file );
		} else {
			$this->setRedirect( _C0_LINK, Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}

	// postSaveHook
	protected function postSaveHook( \Joomla\CMS\MVC\Model\BaseDatabaseModel $model, $validData = array() )
	{
		require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_folder.php';
		Helper_Folder::rebuildTree( 2, 1 );
		
		$recordId	=	$model->getState( $this->context.'.id' );
		if ( $recordId ) {
			Helper_Folder::rebuildBranch( $recordId );
		}
	}
}
?>