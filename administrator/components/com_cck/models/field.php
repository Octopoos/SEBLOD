<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: field.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Model
class CCKModelField extends JCckBaseLegacyModelAdmin
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'field';
	
	// canDelete
	protected function canDelete( $record )
	{
		$user	=	JFactory::getUser();
		
		if ( ! empty( $record->folder ) ) {
			// Folder Permissions
			return $user->authorise( 'core.delete', CCK_COM.'.folder.'.(int)$record->folder );
		} else {
			// Component Permissions
			return parent::canDelete( $record );
		}
	}

	// canEditState
	protected function canEditState( $record )
	{
		$user	=	JFactory::getUser();

		if ( ! empty( $record->folder ) ) {
			// Folder Permissions
			return $user->authorise( 'core.edit.state', CCK_COM.'.folder.'.(int)$record->folder );
		} else {
			// Component Permissions
			return parent::canEditState( $record );
		}
	}
	
	// populateState
	protected function populateState()
	{
		$app	=	JFactory::getApplication( 'administrator' );
		$pk		=	$app->input->getInt( 'id', 0 );
		
		if ( $ajaxState	=	(int)$app->getUserState( CCK_COM.'.add.field.ajax_state' ) != '' ) {
			$this->setState( 'ajax.state', $ajaxState );
		} else {
			$this->setState( 'ajax.state', NULL );
		}
		if ( $ajaxType	=	(string)$app->getUserState( CCK_COM.'.edit.field.ajax_type' ) ) {
			$this->setState( 'ajax.type', $ajaxType );
		}
		
		$this->setState( 'field.id', $pk );
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
	public function getTable( $type = 'Field', $prefix = CCK_TABLE, $config = array() )
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
		$data					=	JRequest::get( 'post' );
		$data['description']	=	JRequest::getVar( 'description', '', '', 'string', JREQUEST_ALLOWRAW );
		$data['storage_table']	=	str_replace( JFactory::getConfig()->get( 'dbprefix' ), '#__', $data['storage_table'] );
		
		JPluginHelper::importPlugin( 'cck_field' );
		JPluginHelper::importPlugin( 'cck_storage_location' );
		$dispatcher	=	JEventDispatcher::getInstance();
		$dispatcher->trigger( 'onCCK_Storage_LocationConstruct', array( @$data['storage_location'], &$data ) );
		$dispatcher->trigger( 'onCCK_FieldConstruct', array( $data['type'], &$data ) );
		
		return $data;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Display
	
	// saveorder
	function saveorder( $pks = null, $order = null )
	{
		$orders	=	array();
		$table	=	$this->getTable();
		$user	=	JFactory::getUser();
		
		if ( empty( $pks ) ) {
			return JError::raiseWarning( 500, JText::_($this->text_prefix.'_ERROR_NO_ITEMS_SELECTED' ) );
		}
		
		foreach ( $pks as $i => $pk ) {
			$table->load( (int)$pk );
			if ( ! $this->canEditState( $table ) ) {
				unset( $pks[$i] );
				JError::raiseWarning( 403, JText::_( 'JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED' ) );
			} elseif ( $table->ordering != $order[$i] || $order[$i] <= 0 || isset( $orders[$order[$i]] ) ) {
				if ( $order[$i] <= 0 || isset( $orders[$order[$i]] ) ) {
					$order[$i]		=	$i + 1;
				}
				$table->ordering	=	$order[$i];
				$orders[$order[$i]]	=	true;
				if ( ! $table->store() ) {
					$this->setError( $table->getError() );
					return false;
				}
			}
			$orders[$order[$i]]	=	true;
		}
		
		$this->cleanCache();
		
		return true;
	}
}
?>
