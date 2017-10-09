<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: controller.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

// Controller
class CCKController extends JControllerLegacy
{
	protected $default_view	=	'cck';
	
	// display
	public function display( $cachable = false, $urlparams = false )
	{
		$app		=	JFactory::getApplication();
		$id			=	$app->input->getInt( 'id' );
		$layout		=	$app->input->get( 'layout', 'default' );
		$view		=	$app->input->get( 'view', $this->default_view );
		
		// _setUIX
		$this->_setUIX( $view, $layout );
		
		if ( !( $view == 'box' || $view == 'form' || $view == 'list' ) ) {
			require_once JPATH_COMPONENT.'/helpers/helper_admin.php';
			require_once JPATH_COMPONENT.'/helpers/helper_folder.php';
			
			if ( !( $layout == 'edit' || $layout == 'edit2' ) ) {
				if ( $view != $this->default_view ) {
					Helper_Admin::addSubmenu( $this->default_view, $view );
				}
			}
			
			if ( ( $view == 'template' && $layout == 'edit' && ! $this->checkEditId( CCK_COM.'.edit.template', $id ) ) ||
				 ( $view == 'type' && $layout == 'edit' && ! $this->checkEditId( CCK_COM.'.edit.type', $id ) ) ||
				 ( $view == 'field' && $layout == 'edit' && ! $this->checkEditId( CCK_COM.'.edit.field', $id ) ) ||
				 ( $view == 'search' && $layout == 'edit' && ! $this->checkEditId( CCK_COM.'.edit.search', $id ) ) ||
				 ( $view == 'folder' && $layout == 'edit' && ! $this->checkEditId( CCK_COM.'.edit.folder', $id ) ) ||
				 ( $view == 'site' && $layout == 'edit' && ! $this->checkEditId( CCK_COM.'.edit.site', $id ) ) ||
				 ( $view == 'version' && $layout == 'edit' && ! $this->checkEditId( CCK_COM.'.edit.version', $id ) ) ) {
				// $this->setError( JText::sprintf( 'JLIB_APPLICATION_ERROR_UNHELD_ID', $id ) );
				// $this->setMessage( $this->getError(), 'error' );
				// $this->setRedirect( JRoute::_( CCK_LINK.'&view='.$view.'s', false ) );
				// return false;
			}
		}
		
		parent::display();
		
		return $this;
	}
	
	// ajax
	public function ajax()
    {
		$app	=	JFactory::getApplication();
		$file	=	$app->input->getString( 'file', '' );
		$file	=	JPATH_SITE.'/'.$file;
		
		jimport('joomla.filesystem.file');
		
		if ( is_file( $file ) && JFile::getExt( $file ) == 'php' ) {
			include_once $file;
		}
	}
	
	// ajaxAddType
	public function ajaxAddType()
    {
		$app		=	JFactory::getApplication();
		$client		=	$app->input->get( 'client', '' );
		$fields		=	$app->input->getString( 'fields', '' );
		$folder		=	$app->input->getInt( 'folder_id', 1 );
		$title		=	$app->input->getString( 'title', '' );
		$type_id	=	$app->input->getInt( 'type_id', 0 );

		if ( !$title ) {
			return;
		}

		// -- Type
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/type.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/helper_workshop.php';
		
		$prefix						=	JCck::getConfig_Param( 'development_prefix', '' );
		$style						=	Helper_Workshop::getDefaultStyle();
		
		$table						=	JTable::getInstance( 'Type', 'CCK_Table' );
		$table->title				=	$title;
        $table->folder				=	$folder;
		$table->template_admin		=	$style->id;
		$table->template_site		=	$style->id;
		$table->template_content	=	$style->id;
		$table->template_intro		=	$style->id;
		$table->published			=	1;
		$table->access				=	3;
		$table->indexed				=	'intro';
		$table->location			=	'none';
		$table->storage_location	=	JCckDatabase::loadResult( 'SELECT storage_location FROM #__cck_core_types WHERE id = '.(int)$type_id );
		
		if ( !$table->storage_location ) {
			$table->storage_location	=	'';
		}

		$rules	=	array( 'core.create'=>array(),
						   'core.create.max.parent'=>array( '8'=>"0" ),
						   'core.create.max.parent.author'=>array( '8'=>"0" ),
						   'core.create.max.author'=>array( '8'=>"0" ),
						   'core.delete'=>array(),
						   'core.delete.own'=>array(),
						   'core.edit'=>array(),
						   'core.edit.own'=>array() );
		$rules	=	new JAccessRules( $rules );
		$table->setRules( $rules );
		$table->check();
		
		if ( $prefix ) {
			$table->name			=	$prefix.'_'.$table->name;
		}
		
		$table->store();
		// --

		// -- Field
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/field.php';
		$table2						=	JTable::getInstance( 'Field', 'CCK_Table' );
		$table2->title				=	$title;
		$table2->name				=	$table->name;
		$table2->folder				=	$folder;
		$table2->type				=	'group';
		$table2->published			=	1;
		$table2->label				=	'clear';
		$table2->display			=	3;
		$table2->extended			=	$table->name;
		$table2->rows				=	1;
		$table2->storage			=	'none';
		$table2->storage_field		=	$table->name;
		$table2->check();
		$table2->store();
		// --

		if ( $fields && $client && $type_id ) {
			if ( $client == 'list' ) {
				$client	=	'intro';
				/*
				TODO
				*/
				return;
			} else {
				$query	=	'UPDATE #__cck_core_type_field'
						.	' SET typeid = '.(int)$table->id.', computation = "", computation_options = "", conditional = "", conditional_options = ""'
						.	' WHERE typeid = '.$type_id.' AND client = "'.$client.'" AND fieldid IN ('.$fields.')';
				JCckDatabase::execute( $query );
			}
		}

		if ( is_object( $table2 ) ) {
			echo $this->ajax_field_li( $table2, $client );
		}
	}

	// ajaxSaveIntegration
	public function ajaxSaveIntegration()
	{
		$app		=	JFactory::getApplication();
		$json		=	$app->input->json->getRaw();
		$objects	=	json_decode( $json );
		
		if ( count( $objects ) ) {
			$query	=	'UPDATE #__cck_core_objects SET options = CASE name';
			foreach ( $objects as $k=>$v ) {
				$query	.=	' WHEN "'.$k.'" THEN "'.JCckDatabase::escape( json_encode( $v ) ).'"';
				$in		.=	'"'.$k.'",';
			}
			$in		=	substr( $in, 0, -1 );
			$query	.=	' ELSE options END WHERE name IN ('.$in.')';
			JCckDatabase::execute( $query );
		}
	}

	// ajax_field_li
	public function ajax_field_li( $field = NULL, $client = '' )
	{
		$app		=	JFactory::getApplication();
		$lang		=	JFactory::getLanguage();

		if ( is_object( $field ) ) {
			$return		=	true;
			$element	=	'type';
			$master		=	( $client == 'content' || $client == 'intro' ) ? 'content' : 'form';
			
			require_once JPATH_COMPONENT.'/helpers/helper_admin.php';
		} else {
			$return		=	false;
			$fieldname	=	$app->input->get( 'fieldname', '' );
			$element	=	$app->input->get( 'element', 'type' );
			$client		=	$app->input->get( 'client', 'admin' );
			if ( $element == 'search' ) {
				$master	=	( $client == 'order' ) ? 'order' : ( ( $client == 'list' || $client == 'item' ) ? 'content' : 'search' );
				$lang->load( 'plg_cck_field_field_x' );
				$lang->load( 'plg_cck_field_group_x' );
			} else {
				$master	=	( $client == 'content' || $client == 'intro' ) ? 'content' : 'form';
			}
			
			$field		=	JCckDatabase::loadObject( 'SELECT a.id, a.title, a.name, a.folder, a.type, a.label, a.storage_table, a.storage_field FROM #__cck_core_fields AS a WHERE a.name="'.$fieldname.'"' );
			if ( !is_object( $field ) ) {
				return;
			}
			require_once JPATH_COMPONENT.'/helpers/helper_admin.php';
			require_once JPATH_COMPONENT.'/helpers/helper_workshop.php';
		}
		require_once JPATH_SITE.'/plugins/cck_field/'.$field->type.'/'.$field->type.'.php';
		$lang->load( 'plg_cck_field_'.$field->type );
		
		$style		=	array( '1'=>'', '2'=>' hide', '3'=>' hide', '4'=>' hide', '5'=>' hide', '6'=>' hide', '7'=>' hide' );
		$prefix		=	JFactory::getDbo()->getPrefix();
		$data		=	Helper_Workshop::getParams( $element, $master, $client );
		$data2      =   array(
							'construction'=>array(
												'access'=>array( '_' ),
												'link'=>array( '_' ),
												'live'=>array( '_' ),
												'markup'=>array( '_' ),
												'match_mode'=>array( '_' ),
												'restriction'=>array( '_' ),
												'stage'=>array( '_' ),
												'typo'=>array( '_' ),
												'variation'=>array( '_' )
											),
							'task'=>'ajax_field_li'
						);

		if ( $master == 'search' && @$field->storage_table != '' ) {
			$tables	=	JCckDatabase::getTableList( true );
			
			if ( isset( $tables[str_replace( '#__', $prefix, $field->storage_table )] ) ) {
				$columns	=	JCckDatabase::loadObjectList( 'SHOW COLUMNS FROM `'.$field->storage_table.'`', 'Field' );

	            if ( isset( $columns[$field->storage_field] ) ) {
	                $pos    =   strpos( $columns[$field->storage_field]->Type, 'int(' );
	                
	                if ( $pos !== false ) {
	                    $field->match_mode      =   'exact';
	                    $field->match_options   =   '{"var_type":"0"}';
	                }
	            }
	        }
		}
		JCck::callFunc_Array( 'plgCCK_Field'.$field->type, 'onCCK_FieldConstruct_'.$element.$master, array( &$field, $style, $data, &$data2 ) );
		
		$attr		=	array( 'class'=>' b', 'span'=>'<span class="icon-pencil-2"></span>' );
		$json		=	array();
		ob_start();
		Helper_Workshop::displayField( $field, '', $attr );
		$json["construction"]	=	'';
		$json["id"]				=	(int)$field->id;
		$json["html"]			=	ob_get_clean();
		
		if ( isset( $data2['construction'] ) && count( $data2['construction'] ) ) {
			foreach ( $data2['construction'] as $k=>$v ) {
				if ( count( $v ) ) {
					foreach ( $v as $k2=>$v2 ) {
						if ( $k2 != '_' ) {
							if ( count( $v2 ) ) {
								$json["construction"]	.=	JHtml::_( 'select.genericlist', $v2, '_wk_'.$k.'-'.$k2, 'size="1" class="thin hide" data-type="'.$k.'"', 'value', 'text', '' );
							}
						}
					}
				}
			}
		}
		if ( $return !== false ) {
			return JCckDev::toJSON( $json );
		}
		echo JCckDev::toJSON( $json );
	}
	
	// ajax_session
	public function ajax_session()
	{
		$app	=	JFactory::getApplication();
		$user	=	JFactory::getUser();
		if ( !$user->authorise( 'core.admin' ) ) {
			return;
		}
		
		$data	=	array( 'extension'=>$app->input->get( 'extension', '' ),
						   'folder'=>$app->input->getInt( 'folder', 0 ),
						   'type'=>$app->input->get( 'type', '' ),
						   'options'=>$app->input->getString( 'data', '{}' ) );
		
		$table	=	JCckTable::getInstance( '#__cck_more_sessions' );
		$table->bind($data);
		$table->store();
		if ( !$table->title ) {
			$table->title = 'Session'.$table->id;
			$table->store();
		}
	}
	
	// ajax_session_del
	public function ajax_session_del()
	{
		$app	=	JFactory::getApplication();
		$user	=	JFactory::getUser();
		if ( !$user->authorise( 'core.admin' ) ) {
			return;
		}
		
		$session_id	=	$app->input->getInt( 'sid', 0 );
		$table		=	JCckTable::getInstance( '#__cck_more_sessions' );
		$table->load( $session_id );
		$table->delete();
	}
	
	// batch_folder
	public function batch_folder()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );

		$app		=	JFactory::getApplication();
		$pks		=	$app->input->post->get( 'cid', array(), 'array' );
		$n			=	count( $pks );
		$pks_in		=	implode( ',', $pks );
		$view		=	$app->input->getString( 'return_v', '' );
		
		if ( ! $n ) {
			$msg	=	JText::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ).'.';
			$type	=	'error';
		} else {
			if ( $this->getModel()->batchFolder( $pks_in, $view ) ) {
				$msg	=	JText::sprintf( 'COM_CCK_SUCCESSFULLY_UPDATED', $n );
				$type	=	'message';
			} else {
				$msg	=	JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
				$type	=	'error';
			}
		}
		
		$this->setRedirect( 'index.php?option=com_cck&view='.$view , $msg, $type );
	}
	
	// download
	public function download()
	{
		$app		=	JFactory::getApplication();
		$id			=	$app->input->getInt( 'id', 0 );
		$fieldname	=	$app->input->getString( 'file', '' );
		$collection	=	$app->input->get( 'collection', '' );
		$xi			=	$app->input->getInt( 'xi', 0 );
		$client		=	$app->input->get( 'client', 'content' );
		$restricted	=	'';
		$user		=	JFactory::getUser();
		
		if ( ! $id ) {
			$file	=	$fieldname;
			$path	=	JPATH_ROOT.'/'.$file;
			$paths	=	JCck::getConfig_Param( 'media_paths', '' );
			
			if ( $paths != '' ) {
				$allowed	=	false;
				$paths		=	strtr( $paths, array( "\r\n"=>'<br />', "\r"=>'<br />', "\n"=>'<br />' ) );
				$paths		=	explode( '<br />', $paths );

				if ( count( $paths ) ) {
					$paths[]	=	'tmp/';
					foreach ( $paths as $p ) {
						if ( strpos( $path, JPATH_ROOT.'/'.$p ) !== false ) {
							$allowed	=	true;
							break;
						}
					}
				}
				if ( !$allowed ) {
					$this->setRedirect( JUri::base(), JText::_( 'COM_CCK_ALERT_FILE_NOT_AUTH' ), "error" );
					return;
				}
			} elseif ( strpos( $path, JPATH_ROOT.'/tmp/' ) === false ) {
				$this->setRedirect( JUri::base(), JText::_( 'COM_CCK_ALERT_FILE_NOT_AUTH' ), "error" );
				return;
			}
		} else {
			$field		=	JCckDatabase::loadObject( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name="'.JCckDatabase::escape( ( ( $collection != '' ) ? $collection : $fieldname ) ).'"' ); //#
			$query		=	'SELECT a.id, a.pk, a.author_id, a.cck as type, a.storage_location, b.'.$field->storage_field.' as value, c.id as type_id, a.store_id'
						.	' FROM #__cck_core AS a'
						.	' LEFT JOIN '.$field->storage_table.' AS b on b.id = a.pk'
						.	' LEFT JOIN #__cck_core_types AS c on c.name = a.cck'
						.	' WHERE a.id ='.(int)$id;
			$core		=	JCckDatabase::loadObject( $query );

			if ( !is_object( $core ) ) {
				$this->setRedirect( JUri::root(), JText::_( 'COM_CCK_ALERT_FILE_DOESNT_EXIST' ), 'error' );
				return;
			}
			JPluginHelper::importPlugin( 'cck_storage' );
			JPluginHelper::importPlugin( 'cck_field' );

			$config		=	array(
								'author'=>$core->author_id,
								'client'=>$client,
								'collection'=>$collection,
								'fieldname'=>$fieldname,
								'id'=>$core->id,
								'isNew'=>0,
								'location'=>$core->storage_location,
								'pk'=>$core->pk,
								'pkb'=>0,
								'store_id'=>$core->store_id,
								'task'=>'download',
								'type'=>$core->type,
								'type_id'=>$core->type_id,
								'xi'=>$xi
							);
			$dispatcher		=	JEventDispatcher::getInstance();
			$field->value	=	$core->value;
			$pk				=	$core->pk;
			$value			=	'';

			$dispatcher->trigger( 'onCCK_StoragePrepareDownload', array( &$field, &$value, &$config ) );
			
			// Access
			$clients	=	JCckDatabase::loadObjectList( 'SELECT a.fieldid, a.client, a.access, a.restriction, a.restriction_options FROM #__cck_core_type_field AS a LEFT JOIN #__cck_core_types AS b ON b.id = a.typeid'
														. ' WHERE a.fieldid = '.(int)$field->id.' AND b.name="'.(string)$config['type'].'"', 'client' );
			$access		=	( isset( $clients[$client]->access ) ) ? (int)$clients[$client]->access : 0;
			$autorised	=	$user->getAuthorisedViewLevels();
			$restricted	=	( isset( $clients[$client]->restriction ) ) ? $clients[$client]->restriction : '';
			if ( !( $access > 0 && array_search( $access, $autorised ) !== false ) ) {
				$this->setRedirect( JUri::base(), JText::_( 'COM_CCK_ALERT_FILE_NOT_AUTH' ), "error" );
				return;
			}
			$field		=	JCckDatabase::loadObject( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name="'.JCckDatabase::escape( $fieldname ).'"' ); //#
			
			if ( $restricted ) {
				JPluginHelper::importPlugin( 'cck_field_restriction' );
				$field->restriction			=	$restricted;
				$field->restriction_options	=	$clients[$client]->restriction_options;
				$allowed	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$restricted, 'onCCK_Field_RestrictionPrepareContent', array( &$field, &$config ) );
				if ( $allowed !== true ) {
					$this->setRedirect( JUri::base(), JText::_( 'COM_CCK_ALERT_FILE_NOT_AUTH' ), "error" );
					return;
				}
			}
			
			$dispatcher->trigger( 'onCCK_FieldPrepareDownload', array( &$field, $value, &$config ) );
			$file	=	$field->filename;
		}

		$path	=	JPATH_ROOT.'/'.$file;
		if ( is_file( $path ) && $file ) {
			$size	=	filesize( $path ); 
			$ext	=	strtolower( substr ( strrchr( $path, '.' ) , 1 ) );
			if ( $ext == 'php' || $file == '.htaccess' ) {
				return;
			}
			$name	=	substr( $path, strrpos( $path, '/' ) + 1, strrpos( $path, '.' ) );
			if ( $path ) {
				set_time_limit( 0 );
				@ob_end_clean();
				include JPATH_ROOT.'/components/com_cck/download.php';
			}
		} else {
			$this->setRedirect( JUri::base(), JText::_( 'COM_CCK_ALERT_FILE_DOESNT_EXIST' ), 'error' );
		}
	}
	
	// export
	public function export()
	{
		$app	=	JFactory::getApplication();
		$type	=	$app->input->getString( 'extension', 'plugin' );
		$model	=	$this->getModel();
		
		if ( $type == 'languages' ) {
			$lang_tag	=	$app->input->getString( 'lang_tag', 'en-GB' );
			if ( $file = $model->prepareLanguages( $lang_tag ) ) {
				$file	=	JCckDevHelper::getRelativePath( $file, false );
				$this->setRedirect( JUri::base().'index.php?option=com_cck&task=download&file='.$file );
			}
		} else {
			$id		=	$app->input->getInt( 'extension_id', 0 );
			
			if ( $file = $model->prepareExport( $id ) ) {
				$file	=	JCckDevHelper::getRelativePath( $file, false );
				$this->setRedirect( JUri::base().'index.php?option=com_cck&task=download&file='.$file );
			} else {
				switch ( $type ) {
					case 'plugin':
					default:
						$link	=	'index.php?option=com_'.$type.'s&view='.$type.'&layout=edit&extension_id='.$id;
						break;
				}
				$this->setRedirect( $link, 'Plugin not found. Try to download it manually.', 'notice' );
			}
		}
	}
	
	// saveOrderAjax
	public function saveOrderAjax()
	{
		$app	=	JFactory::getApplication();
		$pks 	= 	$app->input->post->get( 'cid', array(), 'array' );
		$order 	= 	$app->input->post->get( 'order', array(), 'array' );

		// Sanitize the input
		$pks	=	ArrayHelper::toInteger( $pks );
		$order	=	ArrayHelper::toInteger( $order );

		// Get the model
		$model 	= 	$this->getModel( 'list' );

		// Save the ordering
		$return	= 	$model->saveOrder( $pks, $order );

		if ( $return ) {
			echo '1';
		}

		// Close the application
		$app->close();
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- //
	
	// _setUIX
	protected function _setUIX( $view, $layout )
	{
		if ( JCck::getUIX() == 'compact' ) {
			define( '_C0_TEXT',		'FOLDER' );
			define( '_C2_TEXT',		'FORM' );
			define( '_C4_TEXT',		'LIST' );
			define( 'CCK_BUILDER',	'FORM_BUILDER' );
			define( 'CCK_LABEL',	'SEBLOD 3.x nano' );
			define( 'CCK_LABEL1',	'SEBLOD' );
			define( 'CCK_LABEL2',	'nano' );
		} else {
			define( '_C0_TEXT',		'APP_FOLDER' );
			define( '_C2_TEXT',		'CONTENT_TYPE' );
			define( '_C4_TEXT',		'SEARCH_TYPE' );
			define( 'CCK_BUILDER',	'APP_BUILDER' );
			define( 'CCK_LABEL',	'SEBLOD 3.x' );
			define( 'CCK_LABEL1',	'SEBLOD' );
			define( 'CCK_LABEL2',	'' );
		}
		
		if ( $view == 'form' || $view == 'list' || ( $view == 'cck' && $layout == 'welcome' ) ) {
			return;
		}
	}
}
?>