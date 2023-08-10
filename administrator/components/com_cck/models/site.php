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

JLoader::register( 'JTableAsset', JPATH_PLATFORM.'/joomla/database/table/asset.php' );
JLoader::register( 'JTableUsergroup', JPATH_PLATFORM.'/joomla/database/table/usergroup.php' );
JLoader::register( 'JTableViewlevel', JPATH_PLATFORM.'/joomla/database/table/viewlevel.php' );

// Model
class CCKModelSite extends JCckBaseLegacyModelAdmin
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'site';
	
	// populateState
	protected function populateState()
	{
		$app	=	JFactory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		if ( ( $type = $app->getUserState( CCK_COM.'.edit.site.type' ) ) != '' ) {
			$this->setState( 'type', $type );
		}
		
		$this->setState( 'site.id', $pk );
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
	public function getTable( $type = 'Site', $prefix = CCK_TABLE, $config = array() )
	{
		return JTable::getInstance( $type, $prefix, $config );
	}
	
	// loadFormData
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data	=	JFactory::getApplication()->getUserState( CCK_COM.'.edit.'.$this->vName.'.data', array() );

		if ( empty( $data ) ) {
			$data	=	$this->getItem();
		}

		return $data;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// prepareData
	protected function prepareData()
	{
		$app					=	JFactory::getApplication();
		$data					=	$app->input->post->getArray();
		$data['description']	=	$app->input->post->get( 'description', '', 'raw' );
		
		if ( $data['context'] != '' ) {
			$data['name']	.=	'@'.$data['context'];
		}
		
		if ( isset( $data['aliases'] ) && is_array( $data['aliases'] ) && count( $data['aliases'] ) ) {
			$data['aliases']	=	implode( '||', $data['aliases'] );
		}
		if ( isset( $data['exclusions'] ) && is_array( $data['exclusions'] ) && count( $data['exclusions'] ) ) {
			$data['json']['configuration']['exclusions']	=	implode( '||', $data['exclusions'] );
			
			unset( $data['exclusions'] );
		}
		
		/* TODO#SEBLOD: call generic->store = JSON */
		if ( isset( $data['json'] ) && is_array( $data['json'] ) ) {
			foreach ( $data['json'] as $k => $v ) {
				if ( is_array( $v ) ) {
					$data[$k]	=	JCckDev::toJSON( $v );
				}
			}
		}
		
		/* TODO#SEBLOD: call plugins->prepareStore() */
		$data['usergroups']	=	$this->_implodeValues( $data['usergroups'], $data['guest_only_group'] );
		$data['viewlevels']	=	$this->_implodeValues( $data['viewlevels'], $data['guest_only_viewlevel'] );
		
		return $data;
	}

	// preStore
	public function preStore( $data )
	{
	}

	// postStore
	public function postStore( $pk )
	{
	}
	
	// _implodeValues
	protected function _implodeValues( $values, $excluded = '' )
	{
		if ( !is_array( $values ) ) {
			return '';
		}
		if ( !$excluded ) {
			return implode( ',', $values );
		}

		$str	=	'';
		foreach ( $values as $i=>$v ) {
			if ( $v != $excluded ) {
				$str	.=	','.$v;
			}
		}

		return substr( $str, 1 );
	}
}
?>