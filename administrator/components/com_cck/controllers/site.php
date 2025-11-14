<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: site.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

jimport( 'joomla.application.component.controllerform' );

// Controller
class CCKControllerSite extends FormController
{
	protected $text_prefix	=	'COM_CCK';
	
	// add
	public function add()
	{
		$app	=	Factory::getApplication();

		// Parent Method
		$result	=	parent::add();

		if ( $result instanceof Exception ) {
			return $result;
		}
		
		// Additional Vars
		$app->setUserState( CCK_COM.'.edit.site.type', $app->input->getString( 'type', '' ) );
	}
	
	// postSaveHook
	protected function postSaveHook( \Joomla\CMS\MVC\Model\BaseDatabaseModel $model, $validData = array() )
	{
		$recordId	=	$model->getState( $this->context.'.id' );
		
		if ( $recordId == 10 || $recordId == 500 || $recordId == 501 ) {
			$db					=	Factory::getDbo();
			$params				=	JCckDatabase::loadResult( 'SELECT params FROM #__extensions WHERE element = "com_cck"' );
			$config				=	JCckDev::fromJSON( $params, 'object' );
			$config->multisite	=	1;
			$params				=	$db->escape( JCckDev::toJSON( $config ) );
			JCckDatabase::execute( 'UPDATE #__extensions SET params = "'.$params.'" WHERE element = "com_cck"' );
		}
	}
}
?>