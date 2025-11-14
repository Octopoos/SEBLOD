<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: version.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

// Model
class CCKModelSession extends JCckBaseLegacyModelAdmin
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'session';
	
	// populateState
	protected function populateState()
	{
		$app	=	Factory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		$this->setState( 'session.id', $pk );
	}
	
	// getForm
	public function getForm( $data = array(), $loadData = true )
	{
		$form	=	$this->loadForm( CCK_COM.'.'.$this->vName, $this->vName, array( 'control' => 'jform', 'load_data' => $loadData ) );
		if ( empty( $form ) ) {
			return false;
		}
		
		return $form;
	}
	
	// getItem
	public function getItem( $pk = null )
	{
		if ( $item = parent::getItem( $pk ) ) {
			//
		}
		
		return $item;
	}
	
	// getTable
	public function getTable( $type = 'Session', $prefix = CCK_TABLE, $config = array() )
	{
		return Table::getInstance( $type, $prefix, $config );
	}
	
	// loadFormData
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data	=	Factory::getApplication()->getUserState( CCK_COM.'.edit.'.$this->vName.'.data', array() );

		if ( empty( $data ) ) {
			$data	=	$this->getItem();
		}

		return $data;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// prepareData
	protected function prepareData()
	{
		$data	=	Factory::getApplication()->input->post->getArray();
		
		return $data;
	}
}
?>