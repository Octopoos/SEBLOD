<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: template.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Model
class CCKModelTemplate extends JCckBaseLegacyModelAdmin
{
	protected $text_prefix	=	'COM_CCK';
	protected $vName		=	'template';
	
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
		
		if ( ( $mode = $app->getUserState( CCK_COM.'.edit.template.mode' ) ) != '' ) {
			$this->setState( 'mode', $mode );
		}
		
		$this->setState( 'template.id', $pk );
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
	public function getTable( $type = 'Template', $prefix = CCK_TABLE, $config = array() )
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
		
		if ( $data['mode'] ) {
			$data['featured']	=	0;
		} else {
			if ( $data['featured'] ) {
				JCckDatabase::execute( 'UPDATE #__cck_core_templates SET featured = 0 WHERE id' );
			} else {
				if ( !JCckDatabase::loadResult( 'SELECT COUNT(id) FROM #__cck_core_templates WHERE featured = 1 AND id != '.(int)$data['id'] ) ) {
					$data['featured']	=	1;
				}
			}
		}

		// JSON
		if ( isset( $data['json'] ) && is_array( $data['json'] ) ) {
			foreach ( $data['json'] as $k=>$v ) {
				if ( is_array( $v ) ) {
					$data[$k]	=	JCckDev::toJSON( $v );
				}
			}
		}

		return $data;
	}
	
	// prepareExport_Variation
	public function prepareExport_Variation( $name, $folder )
	{
		$config		=	JFactory::getConfig();
		$tmp_path	=	$config->get( 'tmp_path' );
		$tmp_dir 	=	uniqid( 'cck_' );
		$path 		= 	$tmp_path.'/'.$tmp_dir;
		
		$filename	=	'var_cck_'.$name;
		$path_zip	=	$tmp_path.'/'.$filename.'.zip';
		$src		=	JPATH_SITE.$folder.$name;
		
		// Variation
		jimport( 'cck.base.install.export' );
		if ( JFolder::exists( $src ) ) {
			JFolder::copy( $src, $path.'/'.$name );
		}
		
		// Manifest
		$manifest	=	JPATH_ADMINISTRATOR.'/manifests/files/'.$filename.'.xml';
		
		jimport( 'joomla.filesystem.file' );
		if ( JFile::exists( $manifest ) ) {
			JFile::copy( $manifest, $path.'/'.$filename.'.xml' );
		} else {
			$xml		=	CCK_Export::prepareFile( (object)array( 'title'=>$filename ) );
			$fileset	=	$xml->addChild( 'fileset' );
			$files		=	$fileset->addChild( 'files' );
			$files->addAttribute( 'target', 'libraries/cck/rendering/variations' );
			$file		=	$files->addChild( 'folder', $name );
			
			CCK_Export::createFile( $path.'/'.$filename.'.xml', '<?xml version="1.0" encoding="utf-8"?>'.$xml->asIndentedXML() );
		}
		
		CCK_Export::clean( $path );
		CCK_Export::exportLanguage( $path.'/'.$filename.'.xml', JPATH_SITE, $path );
		
		return CCK_Export::zip( $path, $path_zip );
	}
}
?>
