<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: version.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_COMPONENT.'/helpers/helper_version.php';

// Model
class CCKModelVersion extends JCckBaseLegacyModelAdmin
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'version';
	
	// populateState
	protected function populateState()
	{
		$app	=	JFactory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		$this->setState( 'version.id', $pk );
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
	public function getTable( $type = 'Version', $prefix = CCK_TABLE, $config = array() )
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
		$data	=	JRequest::get( 'post' );
		
		return $data;
	}
	
	// revert
	public function revert( $pk, $type )
	{
		$db		=	$this->getDbo();
		$table	=	$this->getTable();
		
		if ( !$pk || !$type ) {
			return false;
		}
		
		$table->load( $pk );
		
		if ( JCck::getConfig_Param( 'version_revert', 1 ) == 1 ) {
			Helper_Version::createVersion( $type, $table->e_id, JText::sprintf( 'COM_CCK_VERSION_AUTO_BEFORE_REVERT', $table->e_version ) );

			if ( JCck::getConfig_Param( 'version_remove', 1 ) ) {
				Helper_Version::removeVersion( $type, $table->e_id );
			}
		}
		
		$row	=	JTable::getInstance( ucfirst( $type ), 'CCK_Table' );
		$row->load( $table->e_id );
		$core	=	JCckDev::fromJSON( $table->e_core );
		
		if ( isset( $row->asset_id ) && $row->asset_id && isset( $core['rules'] ) ) {
			JCckDatabase::execute( 'UPDATE #__assets SET rules = "'.$db->escape( $core['rules'] ).'" WHERE id = '.(int)$row->asset_id );
		}
		
		// More
		if ( $type == 'search' ) {
			$clients	=	array( 1=>'search', 2=>'filter', 3=>'list', 4=>'item', 5=>'order' );
		} else {
			$clients	=	array( 1=>'admin', 2=>'site', 3=>'intro', 4=>'content' );			
		}
		foreach ( $clients as $i=>$client ) {
			$name				=	'e_more'.$i;
			$this->_revert_more( $type, $client, $table->e_id, $table->{$name}, $core );
		}

		// Override
		if ( $row->version && ( $row->version != $table->e_version ) ) {
			$core['version']		=	++$row->version;
		}
		$core['checked_out']		=	0;
		$core['checked_out_time']	=	'0000-00-00 00:00:00';

		$row->bind( $core );
		$row->check();
		$row->store();
		
		return true;
	}
	
	// _revert_more
	public function _revert_more( $type, $client, $pk, $json, $core = null )
	{
		$data	=	json_decode( $json );
		
		$table	=	JCckTableBatch::getInstance( '#__cck_core_'.$type.'_field' );
		$table->delete( $type.'id = '.$pk.' AND client = "'.$client.'"' );
		$table->save( $data->fields, array(), array(), array( 'markup'=>'', 'restriction'=>'', 'restriction_options'=>'' ) );
		
		$table	=	JCckTableBatch::getInstance( '#__cck_core_'.$type.'_position' );
		$table->delete( $type.'id = '.$pk.' AND client = "'.$client.'"' );
		$table->save( $data->positions );

		if ( isset( $data->template_style ) && is_object( $data->template_style ) && is_array( $core ) ) {
			$target	=	'template_'.$client;
			if ( isset( $core[$target] ) && $core[$target] ) {
				JCckDatabase::execute( 'UPDATE #__template_styles SET params = "'.JCckDatabase::escape( json_encode( $data->template_style ) ).'" WHERE id = '.(int)$core[$target] );
			}
		}
	}
}
?>