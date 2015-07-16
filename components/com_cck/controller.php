<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: controller.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Controller
class CCKController extends JControllerLegacy
{
	protected $text_prefix	=	'COM_CCK';
	
	// __construct
	public function __construct( $config = array() )
	{
		parent::__construct( $config );

		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'save2copy', 'save' );
		$this->registerTask( 'save2new', 'save' );
		$this->registerTask( 'save2redirect', 'save' );
		$this->registerTask( 'save2skip', 'save' );
		$this->registerTask( 'save2view', 'save' );
	}

	// display
	public function display( $cachable = false, $urlparams = false )
	{
		parent::display( true );
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
	
	// cancel
	public function cancel( $key = 'config' )
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app	=	JFactory::getApplication();
		$config	=	$app->input->post->get( $key, array(), 'array' );
		$id		=	(int)$config['id'];
		
		if ( $id > 0 ) {
			$core	=	JCckDatabase::loadObject( 'SELECT pk, storage_location as location FROM #__cck_core WHERE id = '.(int)$id );
			if ( $core->location != '' ) {
				require_once JPATH_SITE.'/plugins/cck_storage_location/'.$core->location.'/'.$core->location.'.php';
				JCck::callFunc( 'plgCCK_Storage_Location'.$core->location, 'checkIn', $core->pk );
			}
		}
		
		$this->setRedirect( $this->_getReturnPage() );
	}
	
	// delete
	public function delete()
	{
		// JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app	=	JFactory::getApplication();
		$model	=	$this->getModel( 'list' );
		$cid	=	$app->input->get( 'cid', array(), 'array' );
		
		jimport('joomla.utilities.arrayhelper');
		JArrayHelper::toInteger( $cid );
		
		if ( $nb = $model->delete( $cid ) ) {
			$msg		=	JText::_( 'COM_CCK_SUCCESSFULLY_DELETED' ); // todo: JText::plural( 'COM_CCK_N_SUCCESSFULLY_DELETED', $nb );
			$msgType	=	'message';
		} else {
			$msg		=	JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
			$msgType	=	'error';
		}
		
		$this->setRedirect( $this->_getReturnPage(), $msg, $msgType );
	}
	
	// download
	public function download()
	{
		$app		=	JFactory::getApplication();
		$id			=	$app->input->getInt( 'id', 0 );
		$fieldname	=	$app->input->getString( 'file', '' );
		$collection	=	$app->input->getString( 'collection', '' );
		$xi			=	$app->input->getString( 'xi', 0 );
		$client		=	$app->input->getString( 'client', 'content' );
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
					$this->setRedirect( JUri::root(), JText::_( 'COM_CCK_ALERT_FILE_NOT_AUTH' ), "error" );
					return;
				}
			} elseif ( strpos( $path, JPATH_ROOT.'/tmp/' ) === false ) {
				$this->setRedirect( JUri::root(), JText::_( 'COM_CCK_ALERT_FILE_NOT_AUTH' ), "error" );
				return;
			}
		} else {
			$field		=	JCckDatabase::loadObject( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name="'.( ( $collection != '' ) ? $collection : $fieldname ).'"' ); //#
			$query		=	'SELECT a.id, a.pk, a.author_id, a.cck as type, a.storage_location, b.'.$field->storage_field.' as value, c.id as type_id'
						.	' FROM #__cck_core AS a'
						.	' LEFT JOIN '.$field->storage_table.' AS b on b.id = a.pk'
						.	' LEFT JOIN #__cck_core_types AS c on c.name = a.cck'
						.	' WHERE a.id ='.(int)$id;
			$core		=	JCckDatabase::loadObject( $query );

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
								'task'=>'download',
								'type'=>$core->type,
								'type_id'=>$core->type_id,
								'xi'=>$xi
							);
			$dispatcher		=	JDispatcher::getInstance();
			$field->value	=	$core->value;
			$pk			=	$core->pk;
			$value			=	'';

			JPluginHelper::importPlugin( 'cck_storage' );
			$dispatcher->trigger( 'onCCK_StoragePrepareDownload', array( &$field, &$value, &$config ) );
			
			// Access
			$clients	=	JCckDatabase::loadObjectList( 'SELECT a.fieldid, a.client, a.access, a.restriction, a.restriction_options FROM #__cck_core_type_field AS a LEFT JOIN #__cck_core_types AS b ON b.id = a.typeid'
														. ' WHERE a.fieldid = '.(int)$field->id.' AND b.name="'.(string)$config['type'].'"', 'client' );
			$access		=	( isset( $clients[$client]->access ) ) ? (int)$clients[$client]->access : 0;
			$autorised	=	$user->getAuthorisedViewLevels();
			$restricted	=	( isset( $clients[$client]->restriction ) ) ? $clients[$client]->restriction : '';
			if ( !( $access > 0 && array_search( $access, $autorised ) !== false ) ) {
				$this->setRedirect( JUri::root(), JText::_( 'COM_CCK_ALERT_FILE_NOT_AUTH' ), "error" );
				return;
			}
			JPluginHelper::importPlugin( 'cck_field' );
			
			$field		=	JCckDatabase::loadObject( 'SELECT a.* FROM #__cck_core_fields AS a WHERE a.name="'.$fieldname.'"' ); //#
			
			if ( $restricted ) {
				JPluginHelper::importPlugin( 'cck_field_restriction' );
				$field->restriction			=	$restricted;
				$field->restriction_options	=	$clients[$client]->restriction_options;
				$allowed	=	JCck::callFunc_Array( 'plgCCK_Field_Restriction'.$restricted, 'onCCK_Field_RestrictionPrepareContent', array( &$field, &$config ) );
				if ( $allowed !== true ) {
					$this->setRedirect( JUri::root(), JText::_( 'COM_CCK_ALERT_FILE_NOT_AUTH' ), "error" );
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
				$task2	=	isset( $field->task ) ? $field->task : 'download';
				if ( $task2 == 'read' ) {
					$this->setRedirect( JURI::root( true ).'/'.$file );
				} else {
					if ( $id ) {
						$event		=	'onCckDownloadSuccess';
						if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
							$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
							if ( isset( $processing[$event] ) ) {
								foreach ( $processing[$event] as $p ) {
									if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
										include_once JPATH_SITE.$p->scriptfile;
									}
								}
							}
						}
						$this->_download_hits( $id, $fieldname, $collection, $xi );
					}
					set_time_limit( 0 );
					@ob_end_clean();
					include JPATH_ROOT.'/components/com_cck/download.php';
				}
			}
		} else {
			$this->setRedirect( JUri::root(), JText::_( 'COM_CCK_ALERT_FILE_DOESNT_EXIST' ), 'error' );
		}
	}
	
	// export
	public function export()
	{
		// JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		if ( !is_file( JPATH_ADMINISTRATOR.'/components/com_cck_exporter/models/cck_exporter.php' ) ) {
			$this->setRedirect( $this->_getReturnPage(), JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
			return;
		}
		
		$app		=	JFactory::getApplication();
		$ids		=	$app->input->get( 'cid', array(), 'array' );
		$task_id	=	$app->input->getInt( 'tid', 0 );
		
		jimport('joomla.utilities.arrayhelper');
		JArrayHelper::toInteger( $ids );

		require_once JPATH_ADMINISTRATOR.'/components/com_cck_exporter/models/cck_exporter.php';
		$model		=	JModelLegacy::getInstance( 'CCK_Exporter', 'CCK_ExporterModel' );
		$params		=	JComponentHelper::getParams( 'com_cck_exporter' );
		$output		=	0; // $params->get( 'output', 0 );
		
		if ( $file = $model->prepareExport( $params, $task_id, $ids ) ) {
			if ( $output > 0 ) {
				$this->setRedirect( $this->_getReturnPage(), JText::_( 'COM_CCK_SUCCESSFULLY_EXPORTED' ), 'message' );
			} else {
				$file	=	JCckDevHelper::getRelativePath( $file, false );
				$this->setRedirect( JUri::base().'index.php?option=com_cck&task=download&file='.$file );
			}
		} else {
			$this->setRedirect( $this->_getReturnPage(), JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}

	// getRoute
	public function getRoute()
	{
		$app		=	JFactory::getApplication();
		$location	=	$app->input->get( 'location', 'joomla_article' );
		$type		=	$app->input->get( 'type', '' );
		$pk			=	$app->input->getInt( 'pk', 0 );
		$itemId		=	$app->input->getInt( 'Itemid', 0 );
		$sef		=	0;
		
		if ( !$pk ) {
			return JUri::root();
		}
		
		if ( $itemId > 0 ) {
			$target	=	JCckDatabase::loadResult( 'SELECT link FROM #__menu WHERE id = '.(int)$itemId );
			if ( $target ) {
				$vars	=	explode( '&', $target );
				foreach ( $vars as $var ) {
					$v	=	explode( '=', $var );
					if ( $v[0] == 'search' ) {
						$target	=	$v[1];
						break;
					}
				}
				$vars	=	JCckDatabase::loadResult( 'SELECT options FROM #__cck_core_searchs WHERE name = "'.(string)$target.'"' );
				if ( $vars ) {
					$vars	=	new JRegistry( $vars );
					$sef	=	$vars->get( 'sef', JCck::getConfig_Param( 'sef', '2' ) );
				}
			}
		}
		
		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$location.'/'.$location.'.php';
		echo JCck::callFunc_Array( 'plgCCK_Storage_Location'.$location, 'getRoute', array( $pk, $sef, $itemId, array( 'type'=>$type ) ) );
	}

	// process
	public function process()
	{
		// JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		if ( !is_file( JPATH_ADMINISTRATOR.'/components/com_cck_toolbox/models/cck_toolbox.php' ) ) {
			$this->setRedirect( $this->_getReturnPage(), JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
			return;
		}
		
		$app		=	JFactory::getApplication();
		$config		=	array();
		$ids		=	$app->input->get( 'cid', array(), 'array' );
		$task_id	=	$app->input->getInt( 'tid', 0 );
		
		jimport('joomla.utilities.arrayhelper');
		JArrayHelper::toInteger( $ids );
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck_toolbox/models/cck_toolbox.php';
		$model		=	JModelLegacy::getInstance( 'CCK_Toolbox', 'CCK_ToolboxModel' );
		$params		=	JComponentHelper::getParams( 'com_cck_toolbox' );
		$output		=	1; // $params->get( 'output', 0 );
		
		$file		=	$model->prepareProcess( $params, $task_id, $ids, $config );
		$link		=	( isset( $config['url'] ) && $config['url'] ) ? $config['url'] : $this->_getReturnPage();
		if ( $file ) {
			if ( $output > 0 ) {
				$this->setRedirect( $link, JText::_( 'COM_CCK_SUCCESSFULLY_PROCESSED' ), 'message' );
			} else {
				$file	=	JCckDevHelper::getRelativePath( $file, false );
				$this->setRedirect( JUri::base().'index.php?option=com_cck&task=download&file='.$file );
			}
		} else {
			$this->setRedirect( $link, JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}

	// saveAjax
	public function saveAjax()
	{
		$config		=	$this->save( true );
		$return		=	array(
							'error'=>0,
							'id'=>@$config['id'],
							'isNew'=>@$config['isNew'],
							'pk'=>$config['pk']
						);
		
		if ( !$return['pk'] ) {
			$return['error']	=	1;
		}
		
		echo json_encode( $return );
	}

	// save	
	public function save( $isAjax = false )
	{
		if ( $isAjax !== true ) {
			JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		}
		
		$app		=	JFactory::getApplication();
		$model		=	$this->getModel( 'form' );
		$preconfig	=	$this->_getPreconfig();
		$task		=	$this->getTask();
		
		$config		=	$model->store( $preconfig, $task );
		$id			=	$config['pk'];
		$itemId		=	$preconfig['itemId'];

		// Return Now for Ajax..
		if ( $isAjax ) {
			return $config;
		}
		
		if ( $config['validate'] == 'retry' ) {
			if ( $app->input->get( 'option', '' ) == 'com_cck' ) {
				$view	=	$app->input->get( 'view', '' );
				if ( $view == 'list' ) {
					$app->input->set( 'task', 'search' );
					$app->input->set( 'retry', $config['type'] );
					parent::display();
					return false;
				} elseif ( $view == 'form' ) {
					$app->input->set( 'retry', $config['type'] );
					parent::display();
					return false;
				}
			}
		}
		
		if ( $id ) {
			if ( $config['message_style'] ) {
				if ( isset( $config['message'] ) ) {
					$msg	=	( $config['doTranslation'] ) ? JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $config['message'] ) ) ) : $config['message'];
				} else {
					$msg	=	JText::_( 'COM_CCK_SUCCESSFULLY_SAVED' );
				}
				$msgType	=	$config['message_style'];
			} else {
				$msg		=	'';
				$msgType	=	'';
			}
			if ( $config['stage'] > -1 && $task != 'save2skip' ) {
				if ( $config['url'] ) {
					$link	=	$config['url'];
				} elseif ( !( isset( $preconfig['skip'] ) && $preconfig['skip'] == '1' ) ) {
					$link	=	'index.php?option=com_cck&view=form&layout=edit&type='.$config['type'].'&id='.$id;
					if ( $itemId > 0 ) {
						$link	.=	'&Itemid='.$itemId;
					}
					if ( $config['stage'] > 0 ) {
						$link	.=	'&stage='.$config['stage'];
					}
					$link	=	JRoute::_( $link );
				}
				if ( $link != '' ) {
					if ( $msg != '' ) {
						$this->setRedirect( htmlspecialchars_decode( $link ), $msg, $msgType );
					} else {
						$this->setRedirect( htmlspecialchars_decode( $link ) );
					}
					return;
				}
			}
		} else {
			$msg		=	JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
			$msgType	= 'error';
		}
		$link		=	$this->_getReturnPage( false );
		$redirect	=	( isset( $config['options']['redirection'] ) ) ? $config['options']['redirection'] : '';
		$return		=	'';
		if ( $task == 'apply' || $task == 'save2copy' ) {
			$link		=	'';
			$redirect	=	'form_edition';
			$return		=	$app->input->getBase64( 'return' );
		} elseif ( $task == 'save2new' ) {
			$link		=	'';
			$redirect	=	'form';
			$return		=	$app->input->getBase64( 'return' );
		} elseif ( $task == 'save2view' ) {
			$link		=	'';
			$redirect	=	'content';
		} elseif ( $task == 'save2redirect' ) {
			$link		=	'';
			$redirect	=	'';
		}
		if ( !$link ) {
			switch ( $redirect ) {
				case 'content':
					$loc		=	JCckDatabase::loadResult( 'SELECT storage_location FROM #__cck_core WHERE id = '.(int)$config['id'] );
					$sef		=	0;
					$itemId2	=	( isset( $config['options']['redirection_itemid'] ) && $config['options']['redirection_itemid'] ) ? (int)$config['options']['redirection_itemid'] : $itemId;
					if ( $itemId2 > 0 ) {
						$target	=	JCckDatabase::loadResult( 'SELECT link FROM #__menu WHERE id = '.(int)$itemId2 );
						if ( $target ) {
							$vars	=	explode( '&', $target );
							foreach ( $vars as $var ) {
								$v	=	explode( '=', $var );
								if ( $v[0] == 'search' ) {
									$target	=	$v[1];
									break;
								}
							}
							$vars	=	JCckDatabase::loadResult( 'SELECT options FROM #__cck_core_searchs WHERE name = "'.(string)$target.'"' );
							if ( $vars ) {
								$vars	=	new JRegistry( $vars );
								$sef	=	$vars->get( 'sef', JCck::getConfig_Param( 'sef', '2' ) );
							}
						}
					}
					if ( $loc ) {
						require_once JPATH_SITE.'/plugins/cck_storage_location/'.$loc.'/'.$loc.'.php';
						$link	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$loc, 'getRoute', array( $config['pk'], $sef, $itemId2, array( 'type'=>$config['type'] ) ) );
					} else {
						$link	=	JUri::root();
					}
					break;
				case 'form':
					$link	=	'index.php?option=com_cck&view=form&layout=edit&type='.$config['type'];
					if ( $itemId > 0 ) {
						$link	.=	'&Itemid='.$itemId;
					}
					if ( $return != '' ) {
						$link	.=	'&return='.$return;
					}
					$link	=	JRoute::_( $link );
					break;
				case 'form_edition':
					$link	=	'index.php?option=com_cck&view=form&layout=edit&type='.$config['type'].'&id='.$id;
					if ( $itemId > 0 ) {
						$link	.=	'&Itemid='.$itemId;
					}
					if ( $return != '' ) {
						$link	.=	'&return='.$return;
					}
					$link	=	JRoute::_( $link );
					break;
				case 'url':
					$link	=	JRoute::_( $config['options']['redirection_url'] );
					break;
				default:
					$link	=	( $config['url'] ) ? $config['url'] : JUri::root();
					break;
			}
		}
		if ( $id ) {
			$char	=	( strpos( $link, '?' ) > 0 ) ? '&' : '?';
			if ( isset( $config['thanks'] ) ) {
				if ( !empty( $config['thanks'] ) ) {
					$thanks			=	( @$config['thanks']->name ) ? $config['thanks']->name : 'thanks';
					$thanks_value	=	( @$config['thanks']->value ) ? $config['thanks']->value : $preconfig['type'];
					$link			.=	$char.$thanks.'='.$thanks_value;
				}
			} else {
				$link			.=	$char.'thanks='.$preconfig['type'];
			}
		}
		if ( $msg != '' ) {
			$this->setRedirect( htmlspecialchars_decode( $link ), $msg, $msgType );
		} else {
			$this->setRedirect( htmlspecialchars_decode( $link ) );
		}
	}
	
	// search
	public function search()
	{
		parent::display( true );
	}

	// saveOrderAjax
	public function saveOrderAjax()
	{
		$app	=	JFactory::getApplication();
		$pks 	= 	$app->input->post->get( 'cid', array(), 'array' );
		$order 	= 	$app->input->post->get( 'order', array(), 'array' );

		// Sanitize the input
		JArrayHelper::toInteger( $pks );
		JArrayHelper::toInteger( $order );

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
	
	// _download_hits
	protected function _download_hits( $id, $fieldname, $collection = '', $x = 0 )
	{
		$where	=	'a.id = '.(int)$id.' AND a.field = "'.(string)$fieldname.'" AND a.collection = "'.(string)$collection.'" AND a.x = '.(int)$x;
		$hits	=	JCckDatabase::loadResult( 'SELECT a.hits FROM #__cck_core_downloads AS a WHERE '.$where );
		
		if ( !$hits ) {
			JCckDatabase::execute( 'INSERT INTO #__cck_core_downloads(`id`, `field`, `collection`, `x`, `hits`) VALUES('.(int)$id.', "'.(string)$fieldname.'", "'.(string)$collection.'", '.(int)$x.', 1)' );
		} else {
			$hits++;
			JCckDatabase::execute( 'UPDATE #__cck_core_downloads AS a SET a.hits = '.(int)$hits.' WHERE '.$where.' AND a.id = '.(int)$id );
		}
		
		return $hits;
	}
	
	// _getPreconfig
	protected function _getPreconfig()
	{
		$data				=	JFactory::getApplication()->input->post->get( 'config', array(), 'array' );

		$data['id']			=	( !isset( $data['id'] ) ) ? 0 : $data['id'];
		$data['itemId']		=	( !isset( $data['itemId'] ) ) ? 0 : $data['itemId'];
		$data['message']	=	( !isset( $data['message'] ) ) ? '' : $data['message'];
		$data['type']		=	( !isset( $data['type'] ) ) ? '' : $data['type'];
		$data['unique']		=	( !isset( $data['unique'] ) ) ? '' : $data['unique'];
		$data['url']		=	( !isset( $data['url'] ) ) ? '' : $data['url'];
		
		return $data;
	}

	// _getReturnPage
	protected function _getReturnPage( $base = true )
	{
		$app	=	JFactory::getApplication();
		$return	=	$app->input->getBase64( 'return' );
		
		/* Joomla! 3.2 FIX */
		$check	=	base64_decode( $return );
		$check	=	( strpos( $check, '?' ) === false && @$check[count($check) - 1] != '/' ) ? $check.'/' : $check;
		/* Joomla! 3.2 FIX */

		if ( empty( $return ) || !JUri::isInternal( $check ) ) {
			return ( $base == true ) ? JURI::base() : '';
		} else {
			return urldecode( base64_decode( $return ) );
		}
	}
}
?>