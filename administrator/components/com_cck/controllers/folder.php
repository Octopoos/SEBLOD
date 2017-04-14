<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: folder.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.controllerform' );

// Controller
class CCKControllerFolder extends JControllerForm
{
	protected $text_prefix	=	'COM_CCK';
	
	// postSaveHook
	protected function postSaveHook( JModelLegacy $model, $validData = array() )
	{
		require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_folder.php';
		Helper_Folder::rebuildTree( 2, 1 );
		
		$recordId	=	$model->getState( $this->context.'.id' );
		if ( $recordId ) {
			Helper_Folder::rebuildBranch( $recordId );
		}
	}
	
	// export
	public function export()
	{
		$app			=	JFactory::getApplication();
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
			$this->setRedirect( JUri::base().'index.php?option=com_cck&task=download&file='.$file );
		} else {
			$this->setRedirect( _C0_LINK, JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}	
}
?>