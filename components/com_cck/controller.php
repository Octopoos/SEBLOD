<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: controller.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

// Controller
class CCKController extends BaseController
{
	protected $text_prefix	=	'COM_CCK';
	
	// __construct
	public function __construct( $config = array() )
	{
		parent::__construct( $config );

		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'read', 'download' );
		$this->registerTask( 'save2copy', 'save' );
		$this->registerTask( 'save2new', 'save' );
		$this->registerTask( 'save2redirect', 'save' );
		$this->registerTask( 'save2skip', 'save' );
		$this->registerTask( 'save2view', 'save' );
		$this->registerTask( 'save4later', 'save' );
	}

	// ajax
	public function ajax()
	{
		Session::checkToken( 'get' ) or jexit( '<!DOCTYPE html><html><head><meta name="robots" content="noindex, nofollow"></head><body>'.Text::_( 'JINVALID_TOKEN' ).'</body></html>' );

		$app	=	Factory::getApplication();
		$file	=	$app->input->getString( 'file', '' );

		if ( $file != '' ) {
			if ( JCckDevHelper::checkAjaxScript( $file ) ) {
				$file	=	JPATH_ROOT.'/'.$file;

				jimport( 'joomla.filesystem.file' );

				if ( is_file( $file ) && File::getExt( $file ) == 'php' ) {
					include_once $file;
				}
			}
		}
	}

	// cancel
	public function cancel( $key = 'config' )
	{
		Session::checkToken() or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app	=	Factory::getApplication();
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
		Session::checkToken( 'get' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app	=	Factory::getApplication();
		$model	=	$this->getModel( 'list' );
		$cid	=	$app->input->get( 'cid', array(), 'array' );
		$cid	=	ArrayHelper::toInteger( $cid );
		
		if ( $nb = $model->delete( $cid ) ) {
			$msg		=	Text::_( 'COM_CCK_SUCCESSFULLY_DELETED' ); /* TODO#SEBLOD: Text::plural( 'COM_CCK_N_SUCCESSFULLY_DELETED', $nb ); */
			$msgType	=	'message';
		} else {
			$msg		=	Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
			$msgType	=	'error';
		}
		
		$this->setRedirect( $this->_getReturnPage(), $msg, $msgType );
	}
	
	// display
	public function display( $cachable = false, $urlparams = false )
	{
		$cachable	=	true;

		// Disable caching on Forms and in Search & List where search is performed
		if ( $this->input->getCmd( 'view', 'form' ) == "form" || $this->input->getMethod() == 'POST' ) {
			$cachable	=	false;
		} else		if ( $this->task == 'search' ) {
			if ( Uri::getInstance()->getQuery() != '' ) {
				$cachable = false;
			}
		}

		if ( $cachable ) {
			$safeurlparams	=	array(
									'boxchecked' => 'INT',
									'id' => 'INT',
									'Itemid' => 'INT',
									'lang' => 'CMD',
									'return' => 'BASE64',
									'search' => 'STRING',
									'task' => 'CMD',
									'type' => 'STRING'
								);
		} else {
			$safeurlparams	=	false;
		}

		parent::display( $cachable, $safeurlparams );
	}

	// download
	public function download()
	{
		$app			=	Factory::getApplication();
		$id				=	$app->input->getInt( 'id', 0 );
		$fieldname		=	$app->input->getString( 'file', '' );
		$to_be_erased	=	false;
		$task			=	$this->getTask();
		$uri_root		=	Uri::root();

		if ( JCckDevHelper::isMultilingual( true ) ) {
			$uri_root	.=	JCckDevHelper::getLanguageCode();
		}

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
						if ( empty( $p ) ) {
							continue;
						}
						if ( strpos( $path, JPATH_ROOT.'/'.$p ) !== false ) {
							if ( JPATH_RESOURCES != JPATH_SITE && is_dir( JPATH_RESOURCES.'/'.$p ) && ( strpos( $file, $p ) !== false ) ) {
								$config		=	array(
													'file_path'=>JPATH_RESOURCES
												);
							}

							$allowed	=	true;
							break;
						}
					}
				}
				if ( !$allowed ) {
					$this->setRedirect( Uri::root(), Text::_( 'COM_CCK_ALERT_FILE_NOT_AUTH' ), "error" );
					return;
				} else {
					$to_be_erased	=	true;
				}
			} elseif ( strpos( $path, JPATH_ROOT.'/tmp/' ) === false ) {
				$this->setRedirect( Uri::root(), Text::_( 'COM_CCK_ALERT_FILE_NOT_AUTH' ), "error" );
				return;
			} else {
				$to_be_erased	=	true;
			}
			if ( $to_be_erased ) {
				if ( strpos( $path, JPATH_ROOT.'/tmp/' ) === false ) {
					$to_be_erased	=	false;
					$paths			=	JCck::getConfig_Param( 'media_paths_tmp', '' );

					if ( $paths != '' ) {
						$paths			=	strtr( $paths, array( "\r\n"=>'<br />', "\r"=>'<br />', "\n"=>'<br />' ) );
						$paths			=	explode( '<br />', $paths );

						if ( count( $paths ) ) {
							foreach ( $paths as $p ) {
								if ( empty( $p ) ) {
									continue;
								}
								if ( strpos( $path, JPATH_ROOT.'/'.$p ) !== false ) {
									$to_be_erased	=	true;
									break;
								}
							}
						}
					}
				}
			}
		} else {
			$config	=	JCckDevHelper::getDownloadInfo( $id, $fieldname );

			if ( $config === false || isset( $config['error'] ) && $config['error'] ) {
				if ( 1 == 1 ) {
					throw new Exception( $config['message'], $config['error_code'] );
				} else {
					$this->setRedirect( $uri_root, $config['message'], "error" );
					return;
				}
			}

			$file		=	( isset( $config['file'] ) ) ? $config['file'] : '';
			$x_robots	=	( isset( $config['x_robots'] ) && $config['x_robots'] ) ? $config['x_robots'] : '';
		}

		if ( isset( $config['file_path'] ) && $config['file_path'] ) {
			$root_folder	=	$config['file_path'];
		} else {
			$root_folder	=	JPATH_ROOT;
		}

		$path	=	$root_folder.'/'.$file;

		if ( is_file( $path ) && $file ) {
			$ext	=	strtolower( substr ( strrchr( $path, '.' ) , 1 ) );
			$name	=	substr( $path, strrpos( $path, '/' ) + 1, strrpos( $path, '.' ) );

			if ( $ext == 'php' || $file == '.htaccess' ) {
				return;
			}
			if ( $path ) {
				if ( $id ) {
					$event		=	'onCckDownloadSuccess';
					if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
						$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
						if ( isset( $processing[$event] ) ) {
							foreach ( $processing[$event] as $p ) {
								if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
									$options	=	new \Joomla\Registry\Registry( $p->options );
									
									include_once JPATH_SITE.$p->scriptfile;
								}
							}
						}
					}
					$this->_download_hits( $id, $fieldname, $config['collection'], $config['xi'] );

					JCckDatabase::execute( 'UPDATE #__cck_core SET download_hits = download_hits+1 WHERE id = '.(int)$config['id'] );
				}

				@ob_end_clean();
				set_time_limit( 0 );

				if ( $task == 'read' || ( isset( $config['task2'] ) && $config['task2'] == 'read' ) ) {
					include JPATH_ROOT.'/components/com_cck/read.php';
				} else {
					include JPATH_ROOT.'/components/com_cck/download.php';
				}
			}
		} else {
			if ( 1 == 1 ) {
				throw new Exception( Text::_( 'COM_CCK_ALERT_FILE_DOESNT_EXIST' ), 404 );
			} else {
				$this->setRedirect( $uri_root, Text::_( 'COM_CCK_ALERT_FILE_DOESNT_EXIST' ), 'error' );
			}
		}
	}
	
	// export
	public function export()
	{
		if ( !Session::checkToken( 'get' ) ) {
			Session::checkToken( 'post' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );
		}
		
		if ( !is_file( JPATH_ADMINISTRATOR.'/components/com_cck_exporter/models/cck_exporter.php' ) ) {
			$this->setRedirect( $this->_getReturnPage(), Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
			return;
		}
		
		$app		=	Factory::getApplication();
		$ids		=	$app->input->get( 'cid', array(), 'array' );
		$task_id	=	$app->input->getInt( 'tid', 0 );
		$ids		=	ArrayHelper::toInteger( $ids );

		require_once JPATH_ADMINISTRATOR.'/components/com_cck_exporter/models/cck_exporter.php';
		$model		=	BaseDatabaseModel::getInstance( 'CCK_Exporter', 'CCK_ExporterModel' );
		$params		=	ComponentHelper::getParams( 'com_cck_exporter' );
		$output		=	0;
		
		if ( $file = $model->prepareExport( $params, $task_id, $ids ) ) {
			if ( $output > 0 ) {
				$this->setRedirect( $this->_getReturnPage(), Text::_( 'COM_CCK_SUCCESSFULLY_EXPORTED' ), 'message' );
			} else {
				$file	=	JCckDevHelper::getRelativePath( $file, false );
				$this->setRedirect( JCckDevHelper::getAbsoluteUrl( 'auto', 'task=download&file='.$file ) );
			}
		} else {
			$this->setRedirect( $this->_getReturnPage(), Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}
	
	// exportAjax
	public function exportAjax()
	{
		Session::checkToken( 'get' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );

		if ( !is_file( JPATH_ADMINISTRATOR.'/components/com_cck_exporter/models/cck_exporter.php' ) ) {
			$this->setRedirect( $this->_getReturnPage(), Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
			return;
		}
		
		$app		=	Factory::getApplication();
		$config		=	array(
							'uniqid'=>$app->input->get( 'uniqid', '' )
						);
		$ids		=	$app->input->get( 'cid', array(), 'array' );
		$task_id	=	$app->input->getInt( 'tid', 0 );
		$ids		=	ArrayHelper::toInteger( $ids );
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck_exporter/models/cck_exporter.php';
		$model		=	BaseDatabaseModel::getInstance( 'CCK_Exporter', 'CCK_ExporterModel' );
		$params		=	ComponentHelper::getParams( 'com_cck_exporter' );

		$file 		=	$model->export( $params, $task_id, $ids, $config );
		$file		=	JCckDevHelper::getRelativePath( $file, false );
		
		if ( $file ) {
			$error			=	0;
			$output_path	=	JCckDevHelper::getAbsoluteUrl( 'auto', 'task=download&file='.$file );
		} else {
			$error			=	1;
			$output_path	=	'';
		}
		$return		=	array(
							'error'=>$error,
							'output_path'=>$output_path
						);
		
		echo json_encode( $return );
	}

	// getBack
	public function getBack()
	{
		$session	=	Factory::getSession();
		$session_id	=	$session->getId();
		$to_id		=	JCckDatabase::loadResult( 'SELECT userid FROM #__session WHERE session_id = "'.$session_id.'"' );
		$user		=	Factory::getUser();

		// Process
		$hash		=	ApplicationHelper::getHash( $user->id.'|'.$to_id.'|'.$user->password );

		if ( $to_id && $hash == $session->get( 'cck_impersonate' ) ) {
			$to_session	=	true;

			jimport( 'cck.joomla.user.user' );
			
			// Go Back
			if ( isset( $user->from_id ) && $user->from_id && $user->from_id != $user->from_id_session ) {
				$from_user	=	Factory::getUser( $user->from_id_session );

				if ( $from_user->authorise( 'core.admin', 'com_users' ) ) {
					$to_session	=	false;
					$userShadow	=	new CCKUser( $user->from_id, $from_user );

					$userShadow->makeHimLive();

					$session->set( 'cck_impersonate', ApplicationHelper::getHash( $user->from_id.'|'.$user->from_id_session.'|'.Factory::getUser()->password ) );
				}	
			}

			// Go Back more..
			if ( $to_session ) {
				$userShadow	=	new CCKUser( $to_id );

				$userShadow->makeHimLive();

				$session->clear( 'cck_impersonate' );
				$session->clear( 'cck_login_as' );
			}
		}

		$this->setRedirect( Uri::root() );
	}

	// impersonate
	public function impersonate()
	{
		if ( !Session::checkToken( 'get' ) ) {
			Session::checkToken( 'post' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );
		}

		$session	=	Factory::getSession();
		$user		=	Factory::getUser();
		$to_id		=	Factory::getApplication()->input->getInt( 'tid', 0 );

		// Process
		if ( $to_id && $user->authorise( 'core.admin', 'com_users' ) ) {
			jimport( 'cck.joomla.user.user' );

			$as		=	Factory::getSession()->get( 'cck_login_as', '' ); /* Keep this line before new CCKUser */
			$str	=	$to_id.'|'.$user->id;

			if ( $as != '' ) {
				$as	=	json_decode( $as, true );

				if ( isset( $as['from_id'] ) && $as['from_id'] ) {
					$str	=	$to_id.'|'.$as['from_id'];
				}
			}

			$userShadow	=	new CCKUser( $to_id, $user );

			$userShadow->makeHimLive();

			$session->set( 'cck_impersonate', ApplicationHelper::getHash( $str.'|'.Factory::getUser()->password ) );
		}

		$this->setRedirect( Uri::root() );
	}

	// outputMessage
	public function outputMessage()
	{
		Session::checkToken( 'get' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );

		$app	=	Factory::getApplication();
		$link	=	$this->_getReturnPage();

		$msgType	=	$app->input->get( 'type', 'message' );

		if ( $msgType == 'error' ) {
			$msg	=	Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
		} else {
			$msg	=	Text::_( 'COM_CCK_SUCCESSFULLY_PROCESSED' );
		}

		$this->setRedirect( $link, $msg, $msgType );
	}

	// process
	public function process()
	{
		if ( !Session::checkToken( 'get' ) ) {
			Session::checkToken( 'post' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );
		}
		
		if ( !is_file( JPATH_ADMINISTRATOR.'/components/com_cck_toolbox/models/cck_toolbox.php' ) ) {
			$this->setRedirect( $this->_getReturnPage(), Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
			return;
		}
		
		$app		=	Factory::getApplication();
		$config		=	array();
		$ids		=	$app->input->get( 'cid', array(), 'array' );
		$task_cid	=	'int';
		$task_id	=	$app->input->getInt( 'tid', 0 );
		
		if ( $task_id ) {
			$processing	=	JCckDatabase::loadObject( 'SELECT options FROM #__cck_more_processings WHERE published = 1 AND id = '.(int)$task_id );
			
			if ( is_object( $processing ) && $processing->options != '' ) {
				$processing->options	=	new \Joomla\Registry\Registry( $processing->options );
				$task_cid				=	$processing->options->get( 'input_cid', 'int' );
			}
		}
		if ( $task_cid == 'int' ) {
			$ids		=	ArrayHelper::toInteger( $ids );
		}

		require_once JPATH_ADMINISTRATOR.'/components/com_cck_toolbox/models/cck_toolbox.php';

		$model		=	BaseDatabaseModel::getInstance( 'CCK_Toolbox', 'CCK_ToolboxModel' );
		$params		=	ComponentHelper::getParams( 'com_cck_toolbox' );
		
		$file		=	$model->prepareProcess( $params, $task_id, $ids, $config );
		$link		=	( isset( $config['url'] ) && $config['url'] ) ? $config['url'] : $this->_getReturnPage();
		
		if ( $file ) {
			$output	=	$params->get( 'output', '' );

			if ( $output == '' ) {
				$output	=	-1;
			}
			if ( $output == -1 ) {
				if ( $config['message_style'] ) {
					if ( isset( $config['message'] ) && $config['message'] != '' ) {
						$msg	=	( $config['doTranslation'] ) ? Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $config['message'] ) ) ) : $config['message'];
					} else {
						$msg	=	Text::_( 'COM_CCK_SUCCESSFULLY_PROCESSED' );
					}
					$msgType	=	$config['message_style'];
				} else {
					$msg		=	'';
					$msgType	=	'';
				}

				if ( $msg != '' ) {
					$this->setRedirect( $link, $msg, $msgType );
				} else {
					$this->setRedirect( $link );
				}
			} else {
				$file	=	JCckDevHelper::getRelativePath( $file, false );
				$this->setRedirect( JCckDevHelper::getAbsoluteUrl( 'auto', 'task=download&file='.$file ) );
			}
		} else {
			if ( $config['message_style'] ) {
				$msg		=	Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
				$msgType	=	'error';
			} else {
				$msg		=	'';
				$msgType	=	'';
			}
			if ( $msg != '' ) {
				$this->setRedirect( $link, $msg, $msgType );
			} else {
				$this->setRedirect( $link );
			}
		}
	}

	// processAjax
	public function processAjax()
	{
		Session::checkToken( 'get' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );

		if ( !is_file( JPATH_ADMINISTRATOR.'/components/com_cck_toolbox/models/cck_toolbox.php' ) ) {
			$this->setRedirect( $this->_getReturnPage(), Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
			return;
		}
		
		$app		=	Factory::getApplication();
		$config		=	array(
							'uniqid'	=>	$app->input->get( 'uniqid', '' ),
							'url'		=> ''
						);
		$ids		=	$app->input->get( 'cid', array(), 'array' );
		$task_cid	=	'int';
		$task_id	=	$app->input->getInt( 'tid', 0 );

		if ( $task_id ) {
			$processing	=	JCckDatabase::loadObject( 'SELECT options FROM #__cck_more_processings WHERE published = 1 AND id = '.(int)$task_id );
			
			if ( is_object( $processing ) && $processing->options != '' ) {
				$processing->options	=	new \Joomla\Registry\Registry( $processing->options );
				$task_cid				=	$processing->options->get( 'input_cid', 'int' );
			}
		}
		if ( $task_cid == 'int' ) {
			$ids		=	ArrayHelper::toInteger( $ids );
		}

		require_once JPATH_ADMINISTRATOR.'/components/com_cck_toolbox/models/cck_toolbox.php';
		
		$model		=	BaseDatabaseModel::getInstance( 'CCK_Toolbox', 'CCK_ToolboxModel' );
		$params		=	ComponentHelper::getParams( 'com_cck_toolbox' );

		$result		=	$model->process( $params, $task_id, $ids, $config );
		$return		=	array(
							'error'=>0,
							'id'=>@$config['id'],
							'isNew'=>1,
							'pk'=>@$config['pk']
						);

		if ( isset( $config['url'] ) && $config['url'] ) {
			$return['url']	=	$config['url'];
		}

		if ( $result === false ) {
			$return['error']	=	1;
			$return['message']	=	$processing->options->get( 'message_error', '' );
		} elseif ( !$return['pk'] ) { /* TODO#SEBLOD: this shouldn't be executed for standalones */
			$task_input	=	0;

			if ( isset( $processing ) && is_object( $processing->options ) ) {
				$task_input			=	(int)$processing->options->get( 'input', '0' );
			}
			if ( !$task_input ) {
				$return['error']	=	1;
			}
		}
		
		$output	=	$params->get( 'output', '' );

		if ( $output == '' ) {
			$output	=	-1;
		}
		if ( $output == 1 ) {
			if ( isset( $config['path'] ) && $config['path'] ) {
				$config['path']			=	JCckDevHelper::getRelativePath( $config['path'], false );
				$config['path']			=	JCckDevHelper::getAbsoluteUrl( 'auto', 'task=download&file='.$config['path'] );
				$return['output_path']	=	$config['path'];
			} else {
				$return['output_path']	=	'';
			}
		} elseif ( isset( $config['url'] ) && $config['url'] ) {
			$return['return']		=	base64_encode( Uri::getInstance()->toString( array( 'scheme', 'host' ) ).$config['url'] );
		}
		
		echo json_encode( $return );
	}

	// route
	public function route()
	{
		$url	=	Factory::getApplication()->input->getBase64( 'link', '' );
		$url	=	htmlspecialchars_decode( base64_decode( $url ) );
		
		if ( $url != '' ) {
			if ( $url[0] == '/' ) {
				$url	=	substr( $url, 1 );
			}
		}
		echo Route::_( $url );
	}

	// save	
	public function save( $isAjax = false )
	{
		Session::checkToken() or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app		=	Factory::getApplication();
		$model		=	$this->getModel( 'form' );
		$preconfig	=	$this->_getPreconfig();
		$task		=	$this->getTask();
		
		$config		=	$model->store( $preconfig, $task );
		$id			=	$config['pk'];
		$itemId		=	$preconfig['itemId'];
		
		if ( $task == 'save4later' ) {
			$task	=	'save';
		}
		
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
		
		if ( (int)$id > 0 || $id === -1 ) {
			if ( $config['message_style'] ) {
				if ( isset( $config['message'] ) && $config['message'] != '' ) {
					$msg	=	( $config['doTranslation'] ) ? Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $config['message'] ) ) ) : $config['message'];
				} else {
					$msg	=	Text::_( 'COM_CCK_SUCCESSFULLY_SAVED' );
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
					$link	=	$this->_getRoute( $link );
				} else {
					$link	=	'';
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
			if ( isset( $config['message_style'] ) && $config['message_style'] === 0 ) {
				$msg		=	'';
			} else {
				$msg		=	Text::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
				$msgType	= 'error';
			}
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
		} elseif ( $task == 'save' ) {
			if ( !$link ) {
				/* Inherited */
			}
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
								$vars	=	new \Joomla\Registry\Registry( $vars );
								$sef	=	$vars->get( 'sef', JCck::getConfig_Param( 'sef', '23' ) );
							}
						}
					}
					if ( $loc ) {
						require_once JPATH_SITE.'/plugins/cck_storage_location/'.$loc.'/'.$loc.'.php';
						$link	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$loc, 'getRoute', array( $config['pk'], $sef, $itemId2, array( 'type'=>$config['type'] ) ) );
					} else {
						$link	=	Uri::root();
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
					$link	=	$this->_getRoute( $link );
					break;
				case 'form_edition':
					$link	=	'index.php?option=com_cck&view=form&layout=edit&type='.$config['type'].'&id='.$id;
					if ( $itemId > 0 ) {
						$link	.=	'&Itemid='.$itemId;
					}
					if ( $return != '' ) {
						$link	.=	'&return='.$return;
					}
					$link	=	$this->_getRoute( $link );
					break;
				case 'url':
					$link	=	$this->_getRoute( $config['options']['redirection_url'] );
					break;
				default:
					$link	=	( $config['url'] ) ? $config['url'] : Uri::root();
					break;
			}
		}
		if ( $id ) {
			$char		=	( strpos( $link, '?' ) > 0 ) ? '&' : '?';
			$hash		=	'';
			if ( strpos( $link, '#' ) !== false ) {
				$parts	=	explode( '#', $link );
				$link	=	$parts[0];
				$hash	=	'#'.$parts[1];
			}
			if ( isset( $config['thanks'] ) ) {
				if ( !empty( $config['thanks'] ) ) {
					$thanks			=	( @$config['thanks']->name ) ? $config['thanks']->name : 'thanks';
					$thanks_value	=	( @$config['thanks']->value ) ? $config['thanks']->value : $preconfig['type'];
					$link			.=	$char.$thanks.'='.$thanks_value.$hash;
				} else {
					$link			.=	$hash;
				}
			} else {
				if ( strpos( $link, '?thanks=' ) === false && strpos( $link, '&thanks=' ) === false ) {
					$link			.=	$char.'thanks='.$preconfig['type'];
				} else {
					$vars			=	JCckDevHelper::getUrlVars( $link );
					$thanks			=	$vars->get( 'thanks', '' );
					$link			=	str_replace( '?thanks='.$thanks, '?thanks='.$preconfig['type'], $link );
					$link			=	str_replace( '&thanks='.$thanks, '&thanks='.$preconfig['type'], $link );
				}
				$link				.=	$hash;
			}
		}
		if ( $msg != '' ) {
			$this->setRedirect( htmlspecialchars_decode( $link ), $msg, $msgType );
		} else {
			$this->setRedirect( htmlspecialchars_decode( $link ) );
		}
	}

	// saveAjax
	public function saveAjax()
	{
		Session::checkToken() or jexit( Text::_( 'JINVALID_TOKEN' ) );

		$config		=	$this->save( true );
		$return		=	array(
							'error'=>0,
							'id'=>@$config['id'],
							'isNew'=>@$config['isNew'],
							'pk'=>$config['pk']
						);
		$session	=	Factory::getSession();

		if ( $page_data = $session->get( 'cck.data_layer', null ) ) {
			$return['data_layer']	=	$page_data;

			$session->clear( 'cck.data_layer' );
		}

		if ( isset( $config['html'] ) ) {
			$return['html']	=	$config['html'];
		}
		if ( !$return['pk'] ) {
			$return['error']	=	1;
		}
		
		echo json_encode( $return );
	}

	// saveFieldAjax
	public function saveFieldAjax()
	{
		// JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app		=	Factory::getApplication();
		$model		=	$this->getModel( 'form' );
		$preconfig	=	array(
							'id'=>$app->input->post->getInt( 'id', 0 ),
							'target'=>$app->input->post->get( 'target', '' ),
							'value'=>$app->input->post->getString( 'value', '' )
						);
		
		$config		=	$model->storeField( $preconfig );
		$return		=	array(
							'error'=>0,
							'id'=>$config['id'],
							'pk'=>$config['pk']
						);
		
		if ( !$return['pk'] ) {
			$return['error']	=	1;
		}
		
		echo json_encode( $return );
	}

	// saveOrderAjax
	public function saveOrderAjax()
	{
		Session::checkToken( 'get' ) or jexit( Text::_( 'JINVALID_TOKEN' ) );
		
		$app	=	Factory::getApplication();
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
	
	// toggleAjax
	public function toggleAjax()
	{

	}

	// _download_hits
	protected function _download_hits( $id, $fieldname, $collection = '', $x = 0 )
	{
		$where	=	'a.id = '.(int)$id.' AND a.field = "'.JCckDatabase::escape( (string)$fieldname ).'" AND a.collection = "'.JCckDatabase::escape( (string)$collection ).'" AND a.x = '.(int)$x;
		$hits	=	JCckDatabase::loadResult( 'SELECT a.hits FROM #__cck_core_downloads AS a WHERE '.$where );
		
		if ( !$hits ) {
			JCckDatabase::execute( 'INSERT INTO #__cck_core_downloads(`id`, `field`, `collection`, `x`, `hits`) VALUES('.(int)$id.', "'.JCckDatabase::escape( (string)$fieldname ).'", "'.JCckDatabase::escape( (string)$collection ).'", '.(int)$x.', 1)' );
		} else {
			$hits++;
			JCckDatabase::execute( 'UPDATE #__cck_core_downloads AS a SET a.hits = '.(int)$hits.' WHERE '.$where.' AND a.id = '.(int)$id );
		}
		
		return $hits;
	}
	
	// _getPreconfig
	protected function _getPreconfig()
	{
		$data	=	Factory::getApplication()->input->post->get( 'config', array(), 'array' );

		$data['copyfrom_id']	=	( !isset( $data['copyfrom_id'] ) ) ? 0 : (int)$data['copyfrom_id'];
		$data['id']				=	( !isset( $data['id'] ) ) ? 0 : (int)$data['id'];
		$data['itemId']			=	( !isset( $data['itemId'] ) ) ? 0 : (int)$data['itemId'];
		$data['message']		=	( !isset( $data['message'] ) ) ? '' : $data['message'];
		$data['tmpl']			=	( !isset( $data['tmpl'] ) ) ? '' : $data['tmpl'];
		$data['type']			=	( !isset( $data['type'] ) ) ? '' : $data['type'];
		$data['unique']			=	( !isset( $data['unique'] ) ) ? '' : $data['unique'];
		$data['url']			=	( !isset( $data['url'] ) ) ? '' : $data['url'];
		
		return $data;
	}

	// _getReturnPage
	protected function _getReturnPage( $base = true )
	{
		$app	=	Factory::getApplication();
		$return	=	$app->input->getBase64( 'return' );
		
		/* Joomla! 3.2 FIX */
		$check	=	base64_decode( $return );

		if ( strpos( $check, '?' ) === false && is_array( $check ) && @$check[count($check) - 1] != '/' ) {
			$check	=	$check.'/';
		}
		/* Joomla! 3.2 FIX */

		if ( empty( $return ) || !Uri::isInternal( $check ) ) {
			return ( $base == true ) ? Uri::base() : '';
		} else {
			return urldecode( base64_decode( $return ) );
		}
	}

	// _getRoute
	protected function _getRoute( $link )
	{
		$route	=	Route::_( $link );

		if ( JCck::isSite() ) {
			if ( !(int)JCck::getConfig_Param( 'multisite_context', '1' ) ) {
				$site	=	JCck::getSite();

				if ( $site->context != '' ) {
					$exclusions	=	JCck::getSite()->exclusions;

					if ( isset( $site->exclusions ) && count( $site->exclusions ) ) {
						foreach ( $site->exclusions as $excl ) {
							$length	=	strlen( $excl );

							if ( $excl[$length - 1 ] != '/' ) {
								$excl	.=	'/';
							}
							if ( $excl[0] != '/' ) {
								$excl	=	'/'.$excl;
							}
							if ( $site->context != '' ) {
								// $excl	=	'/' . $site->context . $excl;
							}
							$pos	=	strpos( $route, $excl );

							if ( $pos !== false && $pos == 0 ) {
								$route	=	'/' . $site->context . $route;
								break;
							}
						}
					}
				}
			}
		}

		return $route;
	}
}
?>