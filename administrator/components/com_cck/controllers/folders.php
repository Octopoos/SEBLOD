<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: folders.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

jimport( 'joomla.application.component.controlleradmin' );

// Controller
class CCKControllerFolders extends JControllerAdmin
{
	protected $text_prefix	=	'COM_CCK';
			
	// __construct
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}

	// getModel
	public function getModel( $name = 'Folder', $prefix = CCK_MODEL, $config = array( 'ignore_request' => true ) )
	{
		return parent::getModel( $name, $prefix, $config );
	}

	// rebuild
	public function rebuild()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app		=	JFactory::getApplication();
		$cid		=	$app->input->get( 'cid', array(), 'array' );
		$cid		=	ArrayHelper::toInteger( $cid );

		$recordId	= (int) (count($cid) ? $cid[0] : 2 );
		$model		=	$this->getModel();
		if ( $model->rebuild( $recordId ) ) {
			$msg	=	JText::_( 'COM_CCK_SUCCESSFULLY_REBUILT' );
			$type	=	'message';
		} else {
			$msg	=	JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
			$type	=	'error';
		}
		
		$this->setRedirect( _C0_LINK, $msg, $type );
	}
}
?>