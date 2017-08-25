<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: joomla_user.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JLoader::register( 'JUser', JPATH_PLATFORM.'/joomla/user/user.php' );

// Plugin
class plgCCK_Storage_LocationJoomla_User extends JCckPluginLocation
{
	protected static $type			=	'joomla_user';
	protected static $table			=	'#__users';
	protected static $table_object	=	array( 'User', 'JTable' );
	protected static $key			=	'id';
	
	protected static $access		=	'';
	protected static $author		=	'id';
	protected static $author_object	=	'';
	protected static $bridge_object	=	'joomla_article';
	protected static $child_object	=	'';
	protected static $created_at	=	'registerDate';
	protected static $custom		=	'';
	protected static $modified_at	=	'';
	protected static $parent		=	'';
	protected static $parent_object	=	'';
	protected static $status		=	'';
	protected static $to_route		=	'';
	
	protected static $context		=	'';
	protected static $context2		=	'';
	protected static $contexts		=	array( 'com_content.article' );
	protected static $error			=	false;
	protected static $events		=	array(
											'afterDelete'=>'onUserAfterDelete',
											'afterSave'=>'',
											'beforeDelete'=>'onUserBeforeDelete',
											'beforeSave'=>''
										);
	protected static $ordering		=	array( 'alpha'=>'name ASC' );
	protected static $ordering2		=	array( 'newest'=>'created DESC', 'oldest'=>'created ASC', 'ordering'=>'ordering ASC', 'popular'=>'hits DESC' );
	protected static $pk			=	0;
	protected static $routes		=	array();
	protected static $sef			=	array();
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_Storage_LocationConstruct
	public function onCCK_Storage_LocationConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		if ( empty( $data['storage_table'] ) ) {
			$data['storage_table']	=	self::$table;
		}
		$data['core_table']		=	self::$table;
		$data['core_columns']	=	array( 'groups', 'password2', 'tags' );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Storage_LocationPrepareContent
	public function onCCK_Storage_LocationPrepareContent( &$field, &$storage, $pk = 0, &$config = array(), &$row = null )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		
		// Set
		if ( $table == self::$table ) {
			$storage			=	self::_getTable( $pk );
			$storage->password	=	'';
			$storage->password2	=	'';
			$config['author']	=	$storage->id;
		} else {
			$storage	=	parent::g_onCCK_Storage_LocationPrepareContent( $table, $pk );
			if ( ! isset( $config['storages'][self::$table] ) ) {
				$config['storages'][self::$table]				=	self::_getTable( $pk );
				$config['storages'][self::$table]->password		=	'';
				$config['storages'][self::$table]->password2	=	'';
				$config['author']								=	$config['storages'][self::$table]->id;
			}
		}
	}

	// onCCK_Storage_LocationPrepareForm
	public function onCCK_Storage_LocationPrepareForm( &$field, &$storage, $pk = 0, &$config = array() )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		if ( !isset( $config['primary'] ) ) {
			$config['primary']	=	self::$type;
			$config['pkb']		=	JCckDatabase::loadResult( 'SELECT pkb FROM #__cck_core WHERE storage_location="'.self::$type.'" AND pk='.(int)$config['pk'] ); // todo: move+improve
		}
		
		// Set
		if ( $table == self::$table ) {
			$storage			=	self::_getTable( $pk );
			$storage->password	=	'';
			$storage->password2	=	'';
			$storage->groups	=	( count( $storage->groups ) == 1 ) ? key( $storage->groups ) : $storage->groups;
			$config['asset']	=	'';
			$config['asset_id']	=	0;
			$config['author']	=	parent::g_getBridgeAuthor( 'joomla_article', $pk, self::$type );
		} else {
			$storage			=	parent::g_onCCK_Storage_LocationPrepareForm( $table, $pk );
		}
	}
	
	// onCCK_Storage_LocationPrepareItems
	public function onCCK_Storage_LocationPrepareItems( &$field, &$storages, $pks, &$config = array(), $load = false )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		
		// Prepare
		if ( $load ) {
			if ( $table == self::$table ) {
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE '.self::$key.' IN ('.$config['pks'].')', self::$key );				
				foreach ( $storages[self::$table] as $s ) {
					$query			=	'SELECT a.id FROM #__usergroups AS a INNER JOIN #__user_usergroup_map AS b ON b.group_id = a.id WHERE b.user_id = '.$s->id;
					$s->groups		=	JCckDatabase::loadAssocList( $query, 'id', 'id' );
					$s->guest		=	NULL;
					$s->password	=	'';
					$s->password2	=	'';
				}
			} else {
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE id IN ('.$config['pks'].')', 'id' );
				if ( !isset( $storages[self::$table] ) ) {
					$storages['_']			=	self::$table;
					$storages[self::$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.self::$table.' WHERE '.self::$key.' IN ('.$config['pks'].')', self::$key );
					foreach ( $storages[self::$table] as $s ) {
						$query			=	'SELECT a.id FROM #__usergroups AS a INNER JOIN #__user_usergroup_map AS b ON b.group_id = a.id WHERE b.user_id = '.$s->id;
						$s->groups		=	JCckDatabase::loadAssocList( $query, 'id', 'id' );
						$s->guest		=	NULL;
						$s->password	=	'';
						$s->password2	=	'';
					}
				}
			}
		}
		$config['author']	=	(int)$storages[self::$table][$config['pk']]->{self::$key};
	}
	
	// onCCK_Storage_LocationPrepareList
	public static function onCCK_Storage_LocationPrepareList( &$params )
	{
		require_once JPATH_SITE.'/components/com_content/helpers/route.php';
		require_once JPATH_SITE.'/components/com_content/router.php';
		
		JPluginHelper::importPlugin( 'content' );
		$params	=	JComponentHelper::getParams( 'com_content' );
	}
	
	// onCCK_Storage_LocationPrepareOrder
	public function onCCK_Storage_LocationPrepareOrder( $type, &$order, &$tables, &$config = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		if ( !$this->params->get( 'bridge', 0 ) ) {
			$order	=	'alpha';
		}
		if ( $order == 'alpha' ) {
			$order	=	( isset( self::$ordering[$order] ) ) ? $tables[self::$table]['_'] .'.'. self::$ordering[$order] : '';
		} else {
			$order	=	( isset( self::$ordering2[$order] ) ) ? $tables['#__content']['_'] .'.'. self::$ordering2[$order] : '';
		}
	}
	
	// onCCK_Storage_LocationPrepareSearch
	public function onCCK_Storage_LocationPrepareSearch( $type, &$query, &$tables, &$t, &$config = array(), &$inherit = array(), $user )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		// Init
		$db		=	JFactory::getDbo();
		$now	=	substr( JFactory::getDate()->toSql(), 0, -3 );
		$null	=	$db->getNullDate();
		
		// Prepare
		if ( !$this->params->get( 'bridge', 0 ) ) {
			if ( ! isset( $tables[self::$table] ) ) {
				$tables[self::$table]	=	array( '_'=>'t'.$t++,
												   'fields'=>array(),
												   'join'=>1,
												   'location'=>self::$type
											);
			}
		} else {
			$bridge	=	'#__content';
			if ( ! isset( $tables[$bridge] ) ) {
				$tables[$bridge]	=	array( '_'=>'t'.$t++,
											   'fields'=>array(),
											   'join'=>1,
											   'key'=>'id',
											   'location'=>'joomla_article'
										);
			}
			if ( ! isset( $tables[self::$table] ) ) {
				$tables[self::$table]	=	array( '_'=>'t'.$t++,
												   'fields'=>array(),
												   'join'=>1,
												   'location'=>self::$type
											);
			}
			$t_pk				=	$tables[self::$table]['_'];
			$t_pkb				=	$tables[$bridge]['_'];
			$inherit['bridge']	=	$bridge;
			
			// Set
			if ( ! isset( $tables[$bridge]['fields']['state'] ) ) {
				$query->where( $t_pkb.'.state = 1' );
			}
			if ( ! isset( $tables[$bridge]['fields']['access'] ) ) {
				$access	=	implode( ',', $user->getAuthorisedViewLevels() );
				$query->where( $t_pkb.'.access IN ('.$access.')' );
			}
			if ( ! isset( $tables[$bridge]['fields']['publish_up'] ) ) {
				$query->where( '( '.$t_pkb.'.publish_up = '.$db->quote( $null ).' OR '.$t_pkb.'.publish_up <= '.$db->quote( $now ).' )' );
			}
			if ( ! isset( $tables[$bridge]['fields']['publish_down'] ) ) {
				$query->where( '( '.$t_pkb.'.publish_down = '.$db->quote( $null ).' OR '.$t_pkb.'.publish_down >= '.$db->quote( $now ).' )' );
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// onCCK_Storage_LocationDelete
	public static function onCCK_Storage_LocationDelete( $pk, &$config = array() )
	{
		$app		=	JFactory::getApplication();
		$dispatcher	=	JEventDispatcher::getInstance();
		$table		=	self::_getTable( $pk );	
		
		if ( !$table ) {
			return false;
		}
		
		// Check
		$user 			=	JCck::getUser();
		$canDelete		=	$user->authorise( 'core.delete', 'com_cck.form.'.$config['type_id'] );
		$canDeleteOwn	=	$user->authorise( 'core.delete.own', 'com_cck.form.'.$config['type_id'] );
		if ( ( !$canDelete && !$canDeleteOwn ) ||
			 ( !$canDelete && $canDeleteOwn && $config['author'] != $user->id ) ||
			 ( $canDelete && !$canDeleteOwn && $config['author'] == $user->id ) ) {
			$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_DELETE_NOT_PERMITTED' ), 'error' );
			return;
		}
		
		// Process
		JPluginHelper::importPlugin( 'user' );
		
		$result	=	$dispatcher->trigger( 'onUserBeforeDelete', array( $table->getProperties() ) );
		if ( in_array( false, $result, true ) ) {
			return false;
		}
		if ( !$table->delete() ) {
			return false;
		}
		$dispatcher->trigger( 'onUserAfterDelete', array( $table->getProperties(), true, $table->getError() ) );
		
		return true;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected
	
	// _core
	protected function _core( $data, &$config = array(), $pk = 0 )
	{
		$app			=	JFactory::getApplication();
		$parameters		=	JComponentHelper::getParams( 'com_users' );
		$data['params']	=	( isset( $data['params'] ) ) ? JCckDev::fromJSON( $data['params'] ) : array();	//Fix
		
		if ( ! $config['id'] ) {
			$config['id']	=	parent::g_onCCK_Storage_LocationPrepareStore();
		}
				
		if ( $app->isClient( 'site' ) ) {	// Site
			// Init
			$table	=	self::_getTable_fromSite( $pk );
			$isNew	=	( $pk > 0 ) ? false : true;
			self::_initTable_fromSite( $table, $data, $config );
			
			if ( $isNew ) {
				$activation		=	$parameters->get( 'useractivation' );

				if ( empty( $data['password'] ) ) {
					$data['password']	=	JUserHelper::genRandomPassword( 20 );
					$data['password2']	=	$data['password'];
				}
				if ( ( $activation == 1 ) || ( $activation == 2 ) ) {
					$data['activation']					=	JApplication::getHash( JUserHelper::genRandomPassword() );
					$data['block']						=	1;
					$config['registration_activation']	=	$data['activation'];
				}
			}
			
			// Prepare
			$table->bind( $data );
			JPluginHelper::importPlugin( 'user' );
			self::_completeTable_fromSite( $table, $data, $config, $parameters );
			
			// Store
			if ( ! $table->save() ) {
				$app->enqueueMessage( JText::sprintf( 'COM_CCK_REGISTRATION_SAVE_FAILED', $table->getError() ), 'error' );
				$config['error']	=	true;

				return false;
			}
			
			if ( $isNew ) {
				self::_sendMails( $table, $activation, self::getStaticParams()->get( 'auto_email', 1 ), $parameters->get( 'mail_to_admin' ), $parameters->get( 'sendpassword', 1 ) );
			}
			
			self::$pk	=	$table->{self::$key};
			if ( !$config['pk'] ) {
				$config['pk']	=	self::$pk;
			}
		} else {		// Admin
			// Init
			$table	=	self::_getTable( $pk );
			$isNew	=	( $pk > 0 ) ? false : true;
			self::_initTable( $table, $data, $config );
	
			// Check Error
			if ( self::$error === true ) {
				$config['error']	=	true;

				return false;
			}
			
			// Prepare
			if ( is_array( $data ) ) {
				if ( $isNew && empty( $data['password'] ) ) {
					$data['password']	=	JUserHelper::genRandomPassword( 20 );
					$data['password2']	=	$data['password'];
				}
				$table->bind( $data );
			}
			self::_completeTable( $table, $data, $config, $parameters );
			
			// Store
			if ( !$table->save() ) {
				$config['error']	=	true;
			}
			
			self::$pk	=	$table->{self::$key};
			if ( !$config['pk'] ) {
				$config['pk']	=	self::$pk;
			}
		}
		
		$config['author']	=	$table->id;
		parent::g_onCCK_Storage_LocationStore( $data, self::$table, self::$pk, $config );
	}
	
	// _getTable
	protected static function _getTable( $pk = 0 )
	{
		$table	=	JUser::getInstance( $pk );
		
		return $table;
	}
	
	// _getTable_fromSite
	protected static function _getTable_fromSite( $pk = 0 )
	{
		$table	=	new JUser( $pk );
		
		return $table;
	}
	
	// _initTable
	protected function _initTable( &$table, &$data, &$config, $force = false )
	{
		if ( ! $table->{self::$key} ) {
			parent::g_initTable( $table, ( ( isset( $config['params'] ) ) ? $config['params'] : $this->params->toArray() ), $force );
		} else {
			$my	=	JFactory::getUser();
			
			// You cannot block yourself!
			if ( $data['block'] && $table->{self::$key} == $my->{self::$key} && ! $my->block ) {
				$app	=	JFactory::getApplication();
				$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_CANNOT_BLOCK_SELF' ), 'error' );
				self::$error	=	true;
			}
			
			// You cannot remove your own Super Users permissions!
			$iAmSuperAdmin	=	$my->authorise( 'core.admin' );
			if ( $iAmSuperAdmin && $my->get( self::$key ) == $table->{self::$key} ) {
				$stillSuperAdmin	=	false;
				$myNewGroups		=	$data['groups'];
				foreach ( $myNewGroups as $group ) {
					$stillSuperAdmin	=	( $stillSuperAdmin ) ? ( $stillSuperAdmin ) : JAccess::checkGroup( $group, 'core.admin' );
				}
				if ( ! $stillSuperAdmin ) {
					$app	=	JFactory::getApplication();
					$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_CANNOT_DEMOTE_SELF' ), 'error' );
					self::$error	=	true;
				}
			}
		}
	}
	
	// _initTable_fromSite
	protected function _initTable_fromSite( &$table, &$data, &$config, $force = false )
	{
		if ( ! $table->{self::$key} ) {
			parent::g_initTable( $table, ( ( isset( $config['params'] ) ) ? $config['params'] : $this->params->toArray() ), $force );
		} else {
			if ( !JFactory::getUser()->authorise( 'core.admin', 'com_users' ) ) {
				unset( $data['block'] );
			}
		}
	}
	
	// _completeTable
	protected function _completeTable( &$table, &$data, &$config, $parameters = array() )
	{
		if ( $table->groups ) {
			if ( !is_array( $table->groups ) ) {
				if ( strpos( $table->groups, ',' ) !== false ) {
					$table->groups	=	explode( ',', $table->groups );
				} else {
					$table->groups	=	array( 0=>$table->groups );
				}
			}
			$table->groups	=	array_unique( $table->groups );
		} else {
			$table->groups	=	array( 0=>$parameters->get( 'new_usertype', 2 ) );
		}
	}
	
	// _completeTable_fromSite
	protected function _completeTable_fromSite( &$table, &$data, &$config, $parameters = array() )
	{
		if ( ! $table->{self::$key} ) {
			if ( $table->groups ) {
				if ( !is_array( $table->groups ) ) {
					if ( strpos( $table->groups, ',' ) !== false ) {
						$table->groups	=	explode( ',', $table->groups );
					} else {
						$table->groups	=	array( 0=>$table->groups );
					}
				}
				$table->groups	=	array_unique( $table->groups );
			} else {
				$table->groups	=	array( 0=>$parameters->get( 'new_usertype', 2 ) );
			}
		} else {
			if ( JFactory::getUser()->authorise( 'core.admin', 'com_users' ) ) {
				if ( $table->groups ) {
					if ( !is_array( $table->groups ) ) {
						if ( strpos( $table->groups, ',' ) !== false ) {
							$table->groups	=	explode( ',', $table->groups );
						} else {
							$table->groups	=	array( 0=>$table->groups );
						}
					}
					$table->groups	=	array_unique( $table->groups );
				}
			} else {
				$table->groups	=	JUserHelper::getUserGroups( $table->{self::$key} );
			}
		}
	}
	
	// _sendMail
	protected static function _sendMails( $table, $activation, $auto_email, $admin_emails, $sendpassword )
	{
		$config				=	JFactory::getConfig();
		$data				=	$table->getProperties();
		$data['fromname']	=	$config->get( 'fromname' );
		$data['mailfrom']	=	$config->get( 'mailfrom' );
		$data['sitename']	=	$config->get( 'sitename' );
		$data['siteurl']	=	JUri::root();
		
		if ( $auto_email ) {
			switch ( $activation ) {
				case 2:
					$base				=	JUri::getInstance()->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );
					$data['activate']	=	$base.JRoute::_( 'index.php?option=com_users&task=registration.activate&token='.$data['activation'], false );
					$subject			=	JText::sprintf( 'COM_CCK_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename'] );
					if ( $sendpassword ) {
						$body			=	JText::sprintf( 'COM_CCK_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
															$data['name'],
															$data['sitename'],
															$data['activate'],
															$data['siteurl'],
															$data['username'],
															$data['password_clear']
											);
					} else {
						$body			=	JText::sprintf( 'COM_CCK_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
															$data['name'],
															$data['sitename'],
															$data['activate'],
															$data['siteurl'],
															$data['username']
											);
					}
					break;
				case 1:
					$base				=	JUri::getInstance()->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) );
					$data['activate']	=	$base.JRoute::_( 'index.php?option=com_users&task=registration.activate&token='.$data['activation'], false );
					$subject			=	JText::sprintf( 'COM_CCK_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename'] );
					if ( $sendpassword ) {
						$body			=	JText::sprintf( 'COM_CCK_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
															$data['name'],
															$data['sitename'],
															$data['activate'],
															$data['siteurl'],
															$data['username'],
															$data['password_clear']
											);
					} else {
						$body			=	JText::sprintf( 'COM_CCK_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
															$data['name'],
															$data['sitename'],
															$data['activate'],
															$data['siteurl'],
															$data['username']
											);
					}
					break;
				default:
					$subject	=	JText::sprintf( 'COM_CCK_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename'] );
					if ( $sendpassword ) {
						$body	=	JText::sprintf(	'COM_CCK_EMAIL_REGISTERED_BODY',
													$data['name'],
													$data['sitename'],
													$data['username'],
													$data['password_clear']
									);
					} else {
						$body	=	JText::sprintf(	'COM_CCK_EMAIL_REGISTERED_BODY_NOPW',
													$data['name'],
													$data['sitename'],
													$data['siteurl']
									);
					}
					break;
			}
			JFactory::getMailer()->sendMail( $data['mailfrom'], $data['fromname'], $data['email'], $subject, $body );
		}
		
		if ( ( $activation < 2 ) && ( $admin_emails == 1 ) ) {
			$subject	=	JText::sprintf( 'COM_CCK_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename'] );
			$body 		=	JText::sprintf( 'COM_CCK_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY', $data['name'], $data['username'], $data['siteurl'] );
			
			$rows		=	JCckDatabase::loadObjectList( 'SELECT name, email, sendEmail FROM #__users WHERE sendEmail = 1' );
			if ( count( $rows ) ) {
				foreach ( $rows as $row ) {
					$return	=	JFactory::getMailer()->sendMail( $data['mailfrom'], $data['fromname'], $row->email, $subject, $body );
					if ( $return !== true ) {
						JFactory::getApplication()->enqueueMessage( JText::_( 'COM_CCK_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED' ), 'error' );
						
						return false;
					}
				}
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // SEF

	// buildRoute
	public static function buildRoute( &$query, &$segments, $config, $menuItem = NULL )
	{
		require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_article/joomla_article.php';
		plgCCK_Storage_LocationJoomla_Article::buildRoute( $query, $segments, $config, $menuItem );
	}
	
	// getRoute	//todo: make a parent::getBridgeRoute..
	public static function getRoute( $item, $sef, $itemId, $config = array() )
	{
		if ( is_numeric( $item ) ) {
			$core	=	JCckDatabase::loadObject( 'SELECT cck, pkb FROM #__cck_core WHERE storage_location = "'.self::$type.'" AND pk = '.(int)$item );
			if ( !is_object( $core ) ) {
				return '';
			}
			$pk				=	$core->pkb;
			$config['type']	=	$core->cck;
		} else {
			$pk		=	( isset( $item->pk ) ) ? $item->pk : $item->id;
			$pk		=	JCckDatabase::loadResult( 'SELECT pkb FROM #__cck_core WHERE storage_location = "'.self::$type.'" AND pk = '.(int)$pk );
			if ( !$pk ) {
				return '';
			}
		}
		
		require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_article/joomla_article.php';
		return plgCCK_Storage_LocationJoomla_Article::getRoute( $pk, $sef, $itemId, $config );
	}
	
	// getRouteByStorage //todo: make a parent::getBridgeRoute.. + optimize ($storage->)
	public static function getRouteByStorage( &$storage, $sef, $itemId, $config = array() )
	{
		if ( isset( $storage[self::$table]->_route ) ) {
			return JRoute::_( $storage[self::$table]->_route, false );
		}
		
		$bridge			=	JCckDatabase::loadObject( 'SELECT a.id, a.title, a.alias, a.catid, b.title AS category_title, b.alias AS category_alias'
													. ' FROM #__content AS a LEFT JOIN #__categories AS b ON b.id = a.catid'
													. ' WHERE a.id='.(int)$config['pkb'] );
		if ( !is_object( $bridge ) ) {
			$storage[self::$table]->_route	=	'';
			return $storage[self::$table]->_route;
		}
		$bridge->slug	=	( $bridge->alias ) ? $bridge->id.':'.$bridge->alias : $bridge->id;

		if ( $sef ) {
			if ( $sef == '0' || $sef == '1' ) {
				$path	=	'&catid='.$bridge->catid;
			} elseif ( $sef[0] == '4' ) {
				$path	=	'&catid='.( isset( $bridge->category_alias ) ? $bridge->category_alias : $bridge->catid );
			} elseif ( $sef[0] == '3' ) {
				$path	=	'&typeid='.$config['type'];
			} else {
				$path	=	'';
			}
			$storage[self::$table]->_route	=	plgCCK_Storage_LocationJoomla_Article::_getRoute( $sef, $itemId, $bridge->slug, $path );
		} else {
			require_once JPATH_SITE.'/components/com_content/helpers/route.php';
			$storage[self::$table]->_route	=	ContentHelperRoute::getArticleRoute( $bridge->slug, $bridge->catid );
		}
		
		return JRoute::_( $storage[self::$table]->_route, false );
	}
	
	// parseRoute
	public static function parseRoute( &$vars, $segments, $n, $config )
	{
		$config['join_key']	=	'pkb';
		require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_article/joomla_article.php';
		plgCCK_Storage_LocationJoomla_Article::parseRoute( $vars, $segments, $n, $config );
	}
	
	// setRoutes
	public static function setRoutes( $items, $sef, $itemId )
	{
		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				$item->link	=	self::getRoute( $item, $sef, $itemId );
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff

	// checkIn
	public static function checkIn( $pk = 0 )
	{
		return true;
	}
	
	// getId
	public static function getId( $config )
	{
		return JCckDatabase::loadResult( 'SELECT id FROM #__cck_core WHERE storage_location="'.self::$type.'" AND pk='.(int)$config['pk'] );
	}
}
?>