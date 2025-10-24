<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: joomla_article.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

// Plugin
class plgCCK_Storage_LocationJoomla_Article extends JCckPluginLocation
{
	protected static $type			=	'joomla_article';
	protected static $type_alias	=	'Article';
	protected static $table			=	'#__content';
	protected static $table_object	=	array( 'Content', 'JCckTable' );
	protected static $key			=	'id';
	protected static $key_field		=	'article_pk';

	protected static $access		=	'access';
	protected static $author		=	'created_by';
	protected static $author_object	=	'joomla_user';
	protected static $bridge_object	=	'';
	protected static $child_object	=	'';
	protected static $created_at	=	'created';
	protected static $custom		=	'introtext';
	protected static $modified_at	=	'modified';
	protected static $parent		=	'catid';
	protected static $parent_object	=	'joomla_category';
	protected static $status		=	'state';
	protected static $to_route		=	'a.id as pk, a.title, a.alias, a.catid, a.language';
	
	protected static $context		=	'com_content.article';
	protected static $context2		=	'';
	protected static $contexts		=	array( 'com_content.article' );
	protected static $error			=	false;
	protected static $events		=	array(
											'afterDelete'=>'onContentAfterDelete',
											'afterSave'=>'onContentAfterSave',
											'beforeDelete'=>'onContentBeforeDelete',
											'beforeSave'=>'onContentBeforeSave'
										);
	protected static $ordering		=	array( 'alpha'=>'title ASC', 'newest'=>'created DESC', 'oldest'=>'created ASC', 'ordering'=>'ordering ASC', 'popular'=>'hits DESC' );
	protected static $ordering2		=	array();
	protected static $pk			=	0;
	protected static $routes		=	array();
	protected static $sef			=	array( '1'=>'full',
											   '2'=>'full', '22'=>'id', '23'=>'alias', '24'=>'alias',
											   '3'=>'full', '32'=>'id', '33'=>'alias',
											   '4'=>'full', '42'=>'id', '43'=>'alias',
											   '5'=>'full', '52'=>'id', '53'=>'alias',
											   '8'=>'full', '82'=>'id', '83'=>'alias',
										);
	
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
		$data['core_columns']	=	array( 'associations', 'tags' );
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
			$storage	=	self::_getTable( $pk, true );

			if ( 1 == 1 ) {
				$storage->author_alias	=	JCckDatabaseCache::loadResult( 'SELECT b.alias as author_alias FROM #__cck_core AS a'
																		 . ' LEFT JOIN #__content AS b ON b.id = a.pkb'
																		 . ' WHERE a.storage_location = "joomla_user" AND a.pk = '.(int)$storage->{self::$author} );
			}
			$config['author']	=	$storage->{self::$author};
		} else {
			$storage			=	parent::g_onCCK_Storage_LocationPrepareContent( $table, $pk );
			if ( ! isset( $config['storages'][self::$table] ) ) {
				$config['storages'][self::$table]	=	self::_getTable( $pk, true );
				$config['author']					=	$config['storages'][self::$table]->{self::$author};
			}
		}
		if ( isset( $config['doSEF'] ) && $config['doSEF'] && isset( $row->readmore_link ) ) {
			$row->readmore_link	=	self::getRouteByStorage( $config['storages'], $config['doSEF'], $config['Itemid'], $config );
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
		if ( isset( $config['primary'] ) && $config['primary'] != self::$type ) {
			$pk	=	$config['pkb'];
		}
		
		// Set
		if ( $table == self::$table ) {
			$storage			=	self::_getTable( $pk );
			$config['asset']	=	'com_content';
			if ( $config['copyfrom_id'] ) {
				$empty						=	array( self::$key, 'alias', 'created', 'created_by', 'hits', 'modified', 'modified_by', 'version' );
				$config['language']			=	JFactory::getApplication()->input->get( 'translate' );
				$config['translate']		=	$storage->language;
				$config['copiedfrom_id']	=	$config['copyfrom_id'].':'.$storage->alias;
				foreach ( $empty as $k ) {
					$storage->$k	=	'';
				}
			} else {
				$config['asset_id']	=	(int)$storage->asset_id;
				$config['author']	=	$storage->{self::$author};
				$config['custom']	=	( ! $config['custom'] ) ? self::$custom : $config['custom'];
				$config['language']	=	$storage->language;
			}
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
				$sef_slug			=	false;
				$select				=	( $config['doSEF'][0] == '5' ) ? ', d.alias AS author_alias' : '';

				if ( JCckDevHelper::isMultilingual() ) {
					$lang_tag		=	JFactory::getLanguage()->getTag();

					if ( isset( $config['sef_aliases'] ) && ( $config['sef_aliases'] == 2 || $config['sef_aliases'] == 1 && $lang_tag != JComponentHelper::getParams( 'com_languages' )->get( 'site', 'en-GB' ) ) ) {
						$legacy		=	(int)JCck::getConfig_Param( 'core_legacy_routing', '2018' );
						$sef_slug	=	true;

						if ( $legacy == 2018 ) {
							$languages	=	JLanguageHelper::getLanguages( 'lang_code' );
							$lang_sef	=	isset( $languages[$lang_tag] ) ? $languages[$lang_tag]->sef : substr( $lang_tag, 0, 2 );
							$select		.=	', f.alias_'.$lang_sef.' AS alias_slug, g.alias_'.$lang_sef.' AS category_alias_slug';
						} else {
							$select		.=	', JSON_UNQUOTE(JSON_EXTRACT(f.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS alias_slug'
										.	', JSON_UNQUOTE(JSON_EXTRACT(g.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS category_alias_slug'
										.	', JSON_UNQUOTE(JSON_EXTRACT(g2.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS category_parent_alias';
						}
					}
				}

				$query				=	'SELECT a.*, b.title AS category_title, b.alias AS category_alias'.$select
									.	' FROM '.$table.' AS a'
									.	' LEFT JOIN #__categories AS b ON b.id = a.catid';
				
				if ( $config['doSEF'][0] == '5' ) {
					$query			.=	' LEFT JOIN #__cck_core AS c ON (c.storage_location = "joomla_user" AND c.pk = a.created_by)'
									.	' LEFT JOIN #__content AS d ON d.id = c.pkb';
				}
				
				if ( $sef_slug ) {
					$query			.=	' LEFT JOIN #__cck_store_item_content AS f on f.id = a.id'
									.	' LEFT JOIN #__cck_store_item_categories AS g on g.id = a.catid'
									.	' LEFT JOIN #__categories AS b2 on b2.id = b.parent_id'
									.	' LEFT JOIN #__cck_store_item_categories AS g2 on g2.id = b2.id'
									;
				}

				$query				.=	' WHERE a.'.self::$key.' IN ('.$config['pks'].')';

				try {
					$storages[$table]	=	JCckDatabase::loadObjectList( $query, self::$key );
				} catch ( Exception $e ) {
					if ( $legacy == 2018 ) {
						if ( $sef_slug && strpos( $e->getMessage(), 'Unknown column' ) !== false ) {
							throw new Exception( JText::sprintf( 'COM_CCK_SEF_ALIASES_EXCEPTION', 'alias_'.$lang_sef, implode( ' '.JText::_( 'COM_CCK_AND' ).' ', array( '#__cck_store_item_content', '#__cck_store_item_categories' ) ) ), 500 );
						}
					}

					$config['error']	=	true;
					$storages[$table]	=	array();
				}

				foreach ( $storages[self::$table] as $s ) {
					if ( $sef_slug && $s->alias_slug ) {
						$s->slug		=	$s->id.':'.$s->alias_slug;
					} else {
						$s->slug		=	( $s->alias ) ? $s->id.':'.$s->alias : $s->id;
					}
					if ( $sef_slug && $s->category_alias_slug ) {
						$s->category_alias	=	$s->category_alias_slug;
					}
				}
			} else {
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE id IN ('.$config['pks'].')', 'id' );
				
				if ( !isset( $storages[self::$table] ) ) {
					$storages['_']	=	self::$table;

					$select			=	( $config['doSEF'][0] == '5' ) ? ', d.alias AS author_alias' : '';
					$query			=	'SELECT a.*, b.title AS category_title, b.alias AS category_alias'.$select
									.	' FROM '.self::$table.' AS a LEFT JOIN #__categories AS b ON b.id = a.catid';
					
					if ( $config['doSEF'][0] == '5' ) {
						$query		.=	' LEFT JOIN #__cck_core AS c ON (c.storage_location = "joomla_user" AND c.pk = a.created_by)'
									.	' LEFT JOIN #__content AS d ON d.id = c.pkb';
					}
					$query			.=	' WHERE a.'.self::$key.' IN ('.$config['pks'].')';

					$storages[self::$table]	=	JCckDatabase::loadObjectList( $query, self::$key );

					foreach ( $storages[self::$table] as $s ) {
						$s->slug	=	( $s->alias ) ? $s->id.':'.$s->alias : $s->id;
					}
				}
			}
			if ( empty( $storages[self::$table] ) ) {
				$config['error']	=	true;
			}
		}
		if ( $config['error'] ) {
			return;
		}

		$config['author']	=	(int)$storages[self::$table][$config['pk']]->{self::$author};
	}
	
	// onCCK_Storage_LocationPrepareList
	public static function onCCK_Storage_LocationPrepareList( &$params )
	{
		if ( !JCck::on( '4.0' ) ) {
			require_once JPATH_SITE.'/components/com_content/helpers/route.php';
			require_once JPATH_SITE.'/components/com_content/router.php';
		}
		
		JPluginHelper::importPlugin( 'content' );
		$params	=	JComponentHelper::getParams( 'com_content' );
	}
	
	// onCCK_Storage_LocationPrepareOrder
	public function onCCK_Storage_LocationPrepareOrder( $type, &$order, &$tables, &$config = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		$order	=	( isset( self::$ordering[$order] ) ) ? $tables[self::$table]['_'] .'.'. self::$ordering[$order] : '';
	}
	
	// onCCK_Storage_LocationPrepareSearch
	public function onCCK_Storage_LocationPrepareSearch( $type, &$query, &$tables, &$t, &$config, &$inherit, $user )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		// Init
		$db		=	JFactory::getDbo();
		$now	=	substr( JFactory::getDate()->toSql(), 0, -3 );
		
		// Prepare
		if ( ! isset( $tables[self::$table] ) ) {
			$tables[self::$table]	=	array( '_'=>'t'.$t++,
											   'fields'=>array(),
											   'join'=>1,
											   'location'=>self::$type
										);
		}
		
		// Set
		$t_pk	=	$tables[self::$table]['_'];
		if ( ! isset( $tables[self::$table]['fields']['state'] ) ) {
			$query->where( $t_pk.'.state = 1' );
		}
		if ( ! isset( $tables[self::$table]['fields']['access'] ) ) {
			$access	=	implode( ',', $user->getAuthorisedViewLevels() );
			$query->where( $t_pk.'.access IN ('.$access.')' );
		}
		if ( JCckDevHelper::isMultilingual() && ! isset( $tables[self::$table]['fields']['language'] ) ) {
			$query->where( $t_pk.'.language IN ("'.JFactory::getLanguage()->getTag().'","*")' );
		}
		if ( ! isset( $tables[self::$table]['fields']['publish_up'] ) ) {
			$query->where( '( '.$t_pk.'.publish_up '.JCckDatabase::null().' OR '.$t_pk.'.publish_up <= '.$db->quote( $now ).' )' );
		}
		if ( ! isset( $tables[self::$table]['fields']['publish_down'] ) ) {
			$query->where( '( '.$t_pk.'.publish_down '.JCckDatabase::null().' OR '.$t_pk.'.publish_down >= '.$db->quote( $now ).' )' );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// onCCK_Storage_LocationDelete
	public static function onCCK_Storage_LocationDelete( $pk, &$config = array() )
	{
		$app	=	JFactory::getApplication();
		$table	=	self::_getTable( $pk );
		
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
		$result	=	$app->triggerEvent( 'onContentBeforeDelete', array( self::$context, $table ) );
		if ( in_array( false, $result, true ) ) {
			return false;
		}
		if ( !$table->delete( $pk ) ) {
			return false;
		}
		$app->triggerEvent( 'onContentAfterDelete', array( self::$context, $table ) );
		
		return true;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected
	
	// _core
	protected function _core( $data, &$config = array(), $pk = 0 )
	{
		if ( ! $config['id'] ) {
			$isNew			=	true;
			$config['id']	=	parent::g_onCCK_Storage_LocationPrepareStore();
		} else {
			$isNew		=	false;
		}
		
		// Init
		$app		=	JFactory::getApplication();
		$isNew		=	( $pk > 0 ) ? false : true;
		$rules		=	'{}';
		$table		=	self::_getTable( $pk );
		
		if ( isset( $table->tags ) ) {
			unset( $table->tags );
		}
		if ( isset( $data['tags'] ) ) {
			if ( !empty( $data['tags'] ) && $data['tags'][0] != '' ) {
				$table->newTags	=	$data['tags'];
			}
			unset( $data['tags'] );
		}

		$config['params']	=	$this->params->toArray();

		self::_initTable( $table, $data, $config );
		
		// Check Error
		if ( self::$error === true ) {
			$config['error']	=	true;

			return false;
		}
		
		// Prepare
		if ( is_array( $data ) ) {
			if ( isset( $data['rules'] ) && $data['rules'] ) {
				if ( !is_array( $data['rules'] ) ) {
					$data['rules']	=	json_decode( $data['rules'] );
				}
				$rules	=	new JAccessRules( JCckDevHelper::getRules( $data['rules'] ) );
				
				unset( $data['rules'] );
			}

			if ( $config['task'] == 'save2copy' ) {
				$empty		=	array( self::$key, 'alias', 'created', 'created_by', 'hits', 'modified', 'modified_by', 'version' );
				foreach ( $empty as $k ) {
					$data[$k]	=	'';
				}
			}
			$table->bind( $data );
		}
		$table->setRules( $rules );
		$table->check();
		self::_completeTable( $table, $data, $config );
		
		// Store
		JPluginHelper::importPlugin( 'content' );
		$app->triggerEvent( 'onContentBeforeSave', array( self::$context, &$table, $isNew, $data ) );
		if ( $isNew === true && parent::g_isMax( $table->{self::$author}, $table->{self::$parent}, $config ) ) {
			$config['error']	=	true;

			return false;
		}
		
		if ( !$table->store() ) {
			$error		=	true;

			if ( $isNew ) {
				$i		=	2;
				$alias	=	$table->alias.'-'.$i;
				$test	=	JTable::getInstance( 'Content' );
				
				while ( $test->load( array( 'alias'=>$alias, 'catid'=>$table->catid ) ) ) {
					$alias		=	$table->alias.'-'.$i++;
				}
				$table->alias	=	$alias;

				if ( $table->store() ) {
					$error		=	false;
				}
			}
			if ( $error ) {
				JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );

				if ( $isNew ) {
					parent::g_onCCK_Storage_LocationRollback( $config['id'] );
				}
				$config['error']	=	true;

				return false;
			}
		}
		
		// Featured
		self::_setFeatured( $table, $isNew );
		
		// Checkin
		parent::g_checkIn( $table );
		
		self::$pk			=	$table->{self::$key};
		if ( !$config['pk'] ) {
			$config['pk']	=	self::$pk;
		}
		
		$config['author']	=	$table->{self::$author};
		$config['parent']	=	$table->{self::$parent};
		
		parent::g_onCCK_Storage_LocationStore( $data, self::$table, self::$pk, $config );
		$app->triggerEvent( 'onContentAfterSave', array( self::$context, &$table, $isNew ) );

		// Associations
		if ( JCckDevHelper::hasLanguageAssociations() ) {
			self::_setAssociations( $table, $data, $isNew, $config );
		}
	}
	
	// _getTable
	protected static function _getTable( $pk = 0, $join = false )
	{
		$table	=	JTable::getInstance( 'Content', 'JCckTable' );
		
		if ( $pk > 0 ) {
			$table->load( $pk );
			if ( $table->id ) {
				if ( $join ) { /* TODO#SEBLOD: join */
					$join		=	'';
					$select		=	'';
					$sef_slug	=	false;

					if ( JCckDevHelper::isMultilingual() ) {
						$lang_tag		=	JFactory::getLanguage()->getTag();

						if ( self::$sef_aliases == 2 || ( self::$sef_aliases == 1 && $lang_tag != JComponentHelper::getParams( 'com_languages' )->get( 'site', 'en-GB' ) ) ) {
							$legacy		=	(int)JCck::getConfig_Param( 'core_legacy_routing', '2018' );
							$sef_slug	=	true;

							if ( $legacy && $legacy <= 2018 ) {
								$languages	=	JLanguageHelper::getLanguages( 'lang_code' );
								$lang_sef	=	isset( $languages[$lang_tag] ) ? $languages[$lang_tag]->sef : substr( $lang_tag, 0, 2 );
								$select		.=	' , c.alias_'.$lang_sef.' AS alias_slug, d.alias_'.$lang_sef.' AS category_alias_slug';
							} else {
								$select		.=	', JSON_UNQUOTE(JSON_EXTRACT(c.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS alias_slug'
											.	', JSON_UNQUOTE(JSON_EXTRACT(d.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS category_alias_slug'
											.	', JSON_UNQUOTE(JSON_EXTRACT(d2.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS category_parent_alias';
							}
							
							$join		.=	' LEFT JOIN #__cck_store_item_content AS c ON c.id = a.id'
										.	' LEFT JOIN #__cck_store_item_categories AS d ON d.id = a.catid'
										.	' LEFT JOIN #__categories AS b2 on b2.id = b.parent_id'
										.	' LEFT JOIN #__cck_store_item_categories AS d2 on d2.id = b2.id'
										;
						}
					}

					$query				=	'SELECT b.title AS category_title, b.alias AS category_alias'.$select
										.	' FROM #__content AS a'
										.	' LEFT JOIN #__categories AS b ON b.id = a.catid'
										.	$join
										.	' WHERE a.id = '.$table->id;
					$join				=	JCckDatabaseCache::loadObject( $query );	//@

					if ( is_object( $join ) ) {
						if ( $sef_slug && $join->alias_slug ) {
							$table->slug	=	$table->id.':'.$join->alias_slug;
						} else {
							$table->slug	=	( $table->alias ) ? $table->id.':'.$table->alias : $table->id;
						}
						if ( $sef_slug && $join->category_alias_slug ) {
							$table->category_alias	=	$join->category_alias_slug;
						} else {
							$table->category_alias	=	$join->category_alias;
						}
						$table->category_title			=	$join->category_title;
						$table->category_parent_alias	=	$join->category_parent_alias ?? '';
					} else {
						$table->category_title	=	'';
						$table->category_alias	=	'';
						$table->slug			=	'';
					}
				}

				// Tags
				$table->tags	=	new JHelperTags;

				// if ( (int)JCckDatabaseCache::loadResult( 'SELECT COUNT(id) FROM #__tags' ) > 1 ) {
				$table->tags->getTagIds( $table->id, 'com_content.article' );
				// }
			}
		}
		
		return $table;
	}
	
	// _initTable
	protected static function _initTable( &$table, &$data, &$config, $force = false )
	{
		$user	=	JFactory::getUser();
		
		if ( ! $table->{self::$key} ) {
			$table->access	=	'';
			parent::g_initTable( $table, $config['params'], $force );
			$table->{self::$author}		=	$table->{self::$author} ? $table->{self::$author} : JCck::getConfig_Param( 'integration_user_default_author', 42 );
			if ( ( $user->id > 0 && @$user->guest != 1 ) && !isset( $data[self::$author] ) && !$force ) {
				$data[self::$author]	=	$user->id;
			}

			$table->attribs			=	'';
			$table->fulltext		=	'';
			$table->images			=	'';
			$table->metadata		=	'';
			$table->metadesc		=	'';
			$table->urls			=	'';
		}
		$table->{self::$custom}	=	'';
	}
	
	// _completeTable
	protected static function _completeTable( &$table, &$data, &$config )
	{
		if ( $table->state == 1 && (int)$table->publish_up == 0 ) {
			$table->publish_up	=	substr( JFactory::getDate()->toSql(), 0, -3 );
		}
		if ( ! $table->{self::$key} ) {
			$table->modified_by	=	0;
			if ( isset( $config['params'] ) ) {
				$ordering	=	( isset( $config['params']['ordering'] ) ) ? $config['params']['ordering'] : 0;
			} else {
				$ordering	=	$this->params->get( 'ordering', 0 );
			}
			if ( $ordering == 1 ) {
				$max				=	JCckDatabase::loadResult( 'SELECT MAX(ordering) FROM #__content WHERE catid = '.(int)$table->catid );
				$table->ordering	=	(int)$max + 1;
			} elseif ( $ordering == -1 ) {
				if ( !isset( $config['tasks']['reorder'] ) ) {
					$config['tasks']['reorder']	=	array();
				}
				$idx							=	(int)$table->catid;
				if ( !isset( $config['tasks']['reorder'][$idx] ) ) {
					$config['tasks']['reorder'][$idx]	=	'catid = '.(int)$table->catid.' AND state >= 0';	
				}
			} elseif ( $ordering > -1 ) {
				$table->reorder( 'catid = '.(int)$table->catid.' AND state >= 0' );
			}
		}
		if ( ! $table->title ) {
			$table->title	=	JFactory::getDate()->format( 'Y-m-d-H-i-s' );
			$table->alias	=	$table->title;
		}
		$table->version++;
		
		// Readmore
		if ( isset( $config['type_fields_intro'] ) ) {
			$intro	=	(int)$config['type_fields_intro'];
		} else {
			$query	=	'SELECT COUNT(a.fieldid) FROM #__cck_core_type_field AS a LEFT JOIN #__cck_core_types AS b ON b.id = a.typeid'
					.	' WHERE b.name="'.(string)$config['type'].'" AND a.client="intro"';
			$intro	=	(int)JCckDatabase::loadResult( $query );
		}
		if ( $intro > 0 ) {
			if ( isset( $config['params'] ) ) {
				$auto_readmore	=	( isset( $config['params']['auto_readmore'] ) ) ? $config['params']['auto_readmore'] : 1;
			} else {
				$auto_readmore	=	$this->params->get( 'auto_readmore', 1 );
			}
			$table->fulltext	=	'';
			if ( $auto_readmore == 1 ) {
				$table->fulltext	=	'::cck::'.$config['id'].'::/cck::';
			} elseif ( $auto_readmore == 2 ) {	// Legacy
				if ( strlen( strstr( $data['introtext'], '::fulltext::' ) ) > 0 ) {
					$fulltext_exists	=	true;
					if ( strlen( strstr( $data['introtext'], '::fulltext::::/fulltext::' ) ) > 0 ) {
						$fulltext_exists	=	false;
					}
				} else {
					$fulltext_exists	=	false;
				}
				if ( $fulltext_exists ) {
					$table->fulltext	=	'::cck::'.$config['id'].'::/cck::';
				}
			}
		}
		if ( empty( $table->language ) ) {
			$table->language	=	'*';
		}
		parent::g_completeTable( $table, self::$custom, $config );
	}
	
	// _getReorderConditions
	protected static function _getReorderConditions( $table )
	{
		$conditions	=	array(
							'catid = '.(int)$table->catid
						);

		return $conditions;
	}

	// _setAssociations
	protected function _setAssociations( $table, $data, $isNew, $config )
	{
		if ( !( isset( $data['associations'] ) && is_array( $data['associations'] ) ) ) {
			return;
		}
		$app	=	JFactory::getApplication();
		$db		=	JFactory::getDbo();

		$associations	=	$data['associations'];
		foreach ( $associations as $tag=>$id ) {
			if ( empty( $id ) ) {
				unset( $associations[$tag] );
			} else {
				$associations[$tag]	=	(int) $id;
			}
		}

		// Detecting all associations
		$all_language	=	$table->language == '*';

		if ( $all_language && !empty( $associations ) ) {
			JError::raiseNotice( 403, JText::_( 'COM_CONTENT_ERROR_ALL_LANGUAGE_ASSOCIATED' ) );
		}
		$associations[$table->language]	=	$table->{self::$key};

		// Deleting old association for these items
		$query	=	$db->getQuery( true )
				->delete( '#__associations' )
				->where( 'context=' . $db->quote( 'com_content.item' ) )
				->where( 'id IN (' . implode(',', $associations ) . ')' );
		$db->setQuery( $query );
		
		try
        {
            $db->execute();
        }
        catch (RuntimeException $e)
        {
            $app->enqueueMessage( $e->getMessage(), 'error' );
            return false;
        }

		if ( !$all_language && count( $associations ) ) {
			// Adding new association for these items
			$key	=	md5( json_encode( $associations ) );
			$query->clear()->insert( '#__associations' );
			foreach ( $associations as $tag=>$id ) {
				$query->values( (int) $id . ',' . $db->quote( 'com_content.item' ) . ',' . $db->quote( $key ) );
			}
			$db->setQuery( $query );

			try
	        {
	            $db->execute();
	        }
	        catch ( RuntimeException $e )
	        {
	            $app->enqueueMessage( $e->getMessage(), 'error' );
	            return false;
	        }
		}
	}

	// _setFeatured
	protected static function _setFeatured( $table, $isNew )
	{
		JLoader::register( 'ContentTableFeatured', JPATH_ADMINISTRATOR.'/components/com_content/tables/featured.php' );

		if ( !class_exists( 'ContentTableFeatured' ) ) {
			return;
		}
		
		$featured	=	JTable::getInstance( 'Featured', 'ContentTable' );
		
		if ( $isNew ) {
 			if ( $table->featured == 1 ) {
 				JCckDatabase::execute( 'INSERT INTO #__content_frontpage (`content_id`, `ordering`) VALUES ( '.$table->id.', 0)' );
 				$featured->reorder();
 			}
		} else {
			if ( $table->featured == 0 ) {
 				JCckDatabase::execute( 'DELETE FROM #__content_frontpage WHERE content_id = '.(int)$table->id );
 				$featured->reorder();
			} else {
				$id		=	JCckDatabase::loadResult( 'SELECT content_id FROM #__content_frontpage WHERE content_id = '.(int)$table->id );
				if ( ! $id ) {
 					JCckDatabase::execute( 'INSERT INTO #__content_frontpage (`content_id`, `ordering`) VALUES ( '.$table->id.', 0)' );
	 				$featured->reorder();
				}
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // SEF

	// buildRoute
	public static function buildRoute( &$query, &$segments, $config, $menuItem = null )
	{
		if ( isset( $query['userid'] ) ) {
			$segments[]	=	$query['userid'];
			unset( $query['userid'] );
		} elseif ( isset( $query['typeid'] ) ) {
			$segments[]	=	$query['typeid'];
			unset( $query['typeid'] );
		} elseif ( isset( $query['catid'] ) ) {
			if ( is_object( $menuItem ) && $menuItem->alias != $query['catid'] ) {
				$segments[]	=	$query['catid'];
			}
			unset( $query['catid'] );
		} elseif ( isset( $query['routeid'] ) ) {
			$segments[]	=	$query['routeid'];
			unset( $query['routeid'] );
		}
		
		if ( isset( $query['id'] ) ) {
			if ( self::$sef[$config['doSEF']] == 'full' ) {
				$id		=	$query['id'];
				
				// Make sure we have the id and the alias
				if ( strpos( $query['id'], ':' ) === false ) {
					$db			=	JFactory::getDbo();
					$dbQuery	=	$db->getQuery( true )
									   ->select( 'alias' )
									   ->from( '#__content' )
									   ->where( 'id='.(int)$query['id'] );
					
					$db->setQuery( $dbQuery );

					$alias	=	$db->loadResult();

					if ( $alias != '' ) {
						$id	=	$id.':'.$alias;
					}
				}
			} else {
				if ( strpos( $query['id'], ':' ) !== false ) {
					$idArray	=	explode( ':', $query['id'], 2 );

					if ( self::$sef[$config['doSEF']] == 'alias' || ( ( $query['view'] == 'categories' || $query['view'] == 'category' ) && (int)JCck::getConfig_Param( 'sef', '2' ) == 23 ) ) {
						$id		=	(string)$idArray[1];
					} else {
						$id		=	(int)$idArray[0];
					}
				} else {
					$id			=	$query['id'];

					if ( $config['doSEF'] == '23' && is_numeric( $id ) ) {
						$db			=	JFactory::getDbo();
						$dbQuery	=	$db->getQuery( true );
						$lang_tag	=	JFactory::getLanguage()->getTag();

						$dbQuery->select( 'JSON_UNQUOTE(JSON_EXTRACT(b.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS alias' )
								->from( $db->quoteName( '#__content', 'a' ) )
								->join( 'LEFT', '#__cck_store_item_content AS b ON b.id = a.id' )
								->where( $db->quoteName( 'b.id' ) . ' = ' . (int)$query['id'] );
						
						if ( $config['sef_aliases'] == 2 ) {
							if ( isset( $query['lang'] ) && $query['lang'] ) {
								$lang_tag	=	$query['lang'];
							} else {
								$lang_tag	=	JFactory::getLanguage()->getTag();
							}
						}

						$db->setQuery( $dbQuery );

						$alias		=	$db->loadResult();

						if ( $alias != '' ) {
							$id		=	$alias;
						} else {
							$id		=	0;
						}
					}
				}
			}
			$segments[]	=	$id;
			unset( $query['id'] );
		}
	}
	
	// getRoute
	public static function getRoute( $item, $sef, $itemId, $config = array(), $lang_tag = '' )
	{
		$route	=	self::_prepareRoute( $item, $sef, $itemId, $config, $lang_tag );

		return JRoute::_( $route, false );
	}
	
	// getRouteLink
	public static function getRouteLink( $item, $sef, $itemId, $config = array(), $lang_tag = '' )
	{
		return self::_prepareRoute( $item, $sef, $itemId, $config, $lang_tag );
	}

	// getRouteById
	public static function getRouteById( $pk )
	{
		$route	=	'';

		if ( $pk ) {
			$query	=	'SELECT id'
					.	' FROM #__menu'
					.	' WHERE link = "index.php?option=com_content&view=article&id='.(int)$pk.'"'
					.	' AND (language = "*" OR language = "'.JFactory::getLanguage()->getTag().'")'
					;

			if ( $itemId = (int)JCckDatabase::loadResult( $query ) ) {
				$route	=	JRoute::_( 'index.php?Itemid='.$itemId, false );
			}
		}

		return $route;
	}

	// getRouteByStorage
	public static function getRouteByStorage( &$storage, $sef, $itemId, $config = array(), $lang_tag = '' )
	{
		$idx	=	md5( $sef.'|'.$itemId.'|'.$lang_tag );
		
		if ( isset( $storage[self::$table]->_route[$idx] ) ) {
			return JRoute::_( $storage[self::$table]->_route[$idx], false );
		}
		if ( !is_object( $storage[self::$table] ) ) {
			return '';
		}

		if ( $sef ) {
			if ( $sef == '0' || $sef == '1' ) {
				$path	=	'&catid='.$storage[self::$table]->catid;
			} elseif ( $sef[0] == '5' ) {
				$path	=	'&userid='.( isset( $storage[self::$table]->author_alias ) ? $storage[self::$table]->author_alias : $storage[self::$table]->created_by );
			} elseif ( $sef[0] == '4' || $sef[0] == '8' ) {
				$path	=	isset( $storage[self::$table]->category_alias ) ? $storage[self::$table]->category_alias : $storage[self::$table]->catid;
				if ( isset( $storage[self::$table]->category_parent_alias ) && $storage[self::$table]->category_parent_alias ) {
					$path	=	$storage[self::$table]->category_parent_alias.'/'.$path;
				}
				$path	=	'&catid='.$path;
			} elseif ( $sef[0] == '3' ) {
				$path	=	'&typeid='.$config['type'];
			} elseif (  $sef == '10') {
				$path	=	'&catid='.( isset( $storage[self::$table]->category_alias ) ? $storage[self::$table]->category_alias : $storage[self::$table]->catid );
			} else {
				$path	=	'';

				if ( isset( $storage['#__cck_store_item_content'] ) && isset( $storage['#__cck_store_item_content']->route_id ) && $storage['#__cck_store_item_content']->route_id ) {
					static $route_ids	=	null;

					if ( $route_ids === null ) {
						if ( JCckDevHelper::isMultilingual() ) {
							if ( !$lang_tag ) {
								$lang_tag	=	JFactory::getLanguage()->getTag();
							}

							$query		=	'SELECT a.id, JSON_UNQUOTE(JSON_EXTRACT(b.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS route_alias'
										.	' FROM #__cck_store_item_content AS a'
										.	' LEFT JOIN #__cck_store_item_content AS b ON b.id = a.route_id'
										.	' WHERE a.route_id > 0';
							$route_ids	=	(array)JCckDatabase::loadObjectList( $query, 'id' );
								
							if ( !count( $route_ids ) ) {
								$route_ids	=	array( 0=>(object)array() );
							}
						}
					}
					if ( isset( $route_ids[$storage[self::$table]->id] ) && $route_ids[$storage[self::$table]->id]->route_alias ) {
						$path	=	'&routeid='.$route_ids[$storage[self::$table]->id]->route_alias;
					}
				}
			}

			if ( is_object( $storage[self::$table] ) ) {
				if ( !isset( $storage[self::$table]->_route ) ) {
					$storage[self::$table]->_route		=	array();
				}
				$storage[self::$table]->_route[$idx]	=	self::_getRoute( $sef, $itemId, $storage[self::$table]->slug, $path, '', $lang_tag );
			}

			// Multilanguage Associations
			if ( JCckDevHelper::hasLanguageAssociations() ) {
				$app		=	JFactory::getApplication();
				$pk			=	$storage[self::$table]->id;
				if ( $app->input->get( 'view' ) == 'article' && $app->input->get( 'id' ) == $storage[self::$table]->id && !count( self::$routes ) ) {
					JLoader::register( 'MenusHelper', JPATH_ADMINISTRATOR.'/components/com_menus/helpers/menus.php' );
					$assoc_c	=	JLanguageAssociations::getAssociations( 'com_content', '#__content', 'com_content.item', $pk );
					$assoc_m	=	MenusHelper::getAssociations( $itemId );
					$languages	=	JLanguageHelper::getLanguages();
					$lang_code	=	JFactory::getLanguage()->getTag();
					foreach ( $languages as $l ) {
						if ( $lang_code == $l->lang_code ) {
							self::$routes[$l->lang_code]	=	$storage[self::$table]->_route[$idx];
						} else {
							$itemId2						=	isset( $assoc_m[$l->lang_code] ) ? $assoc_m[$l->lang_code] : 0;
							$pk2							=	isset( $assoc_c[$l->lang_code] ) ? (int)$assoc_c[$l->lang_code]->id : 0;
							self::$routes[$l->lang_code]	=	'';
							if ( $pk2 && $itemId2 ) {
								self::$routes[$l->lang_code]=	self::getRoute( $pk2, $sef, $itemId2, $config, $l->sef );
							}
						}
					}
				}
			}
		} else {
			if ( !JCck::on( '4.0' ) ) {
				require_once JPATH_SITE.'/components/com_content/helpers/route.php';
			}

			if ( !isset( $storage[self::$table]->_route ) ) {
				$storage[self::$table]->_route		=	array();
			}
			$storage[self::$table]->_route[$idx]	=	ContentHelperRoute::getArticleRoute( $storage[self::$table]->slug, $storage[self::$table]->catid, $storage[self::$table]->language );
		}
		
		return JRoute::_( $storage[self::$table]->_route[$idx], false );
	}

	// parseRoute
	public static function parseRoute( &$vars, &$segments, $n, $config )
	{
		$doSub				=	false;
		$id					=	0;
		$isMultiAlias		=	false;
		$isMultiLanguage	=	JCckDevHelper::isMultilingual();
		$join				=	'';
		$join2				=	'';
		$vars['option']		=	'com_content';
		$vars['view']		=	'article';
		$where				=	'';
		$where2				=	'';

		if ( $isMultiLanguage ) {
			$lang_tag	=	JFactory::getLanguage()->getTag();

			if ( isset( $config['sef_aliases'] ) && ( $config['sef_aliases'] == 2 || $config['sef_aliases'] == 1 && $lang_tag != JComponentHelper::getParams( 'com_languages' )->get( 'site', 'en-GB' ) ) ) {
				self::$sef_aliases	=	$config['sef_aliases'];

				$isMultiAlias		=	true;
				$legacy				=	(int)JCck::getConfig_Param( 'core_legacy_routing', '2018' );

				if ( $legacy == 2018 ) {
					$languages			=	JLanguageHelper::getLanguages( 'lang_code' );
					$lang_sef			=	isset( $languages[$lang_tag] ) ? $languages[$lang_tag]->sef : substr( $lang_tag, 0, 2 );
					$legacy				=	1;
				} else {
					$legacy				=	0;
				}
			}
		}

		if ( isset( $config['sef_types'] ) && $config['sef_types'] != '' && ( $pos = strpos( $config['sef_types'], '+' ) ) !== false ) {
			$doSub	=	true;
			$length	=	strlen( $config['sef_types'] );

			if ( $length !== ( $pos + 1 ) ) {
				// TODO: content type on $doSub
			}

			$config['sef_types']	=	str_replace( '+', '', $config['sef_types'] );
		}

		// Prepare the query
		if ( $doSub ) {
			if ( $n == 2 ) {
				$idx			=	0;
				$segments[$idx]	=	str_replace( ':', '-', $segments[$idx] );

				if ( $isMultiLanguage && $isMultiAlias ) {
					$join2		=	' LEFT JOIN #__cck_store_item_content AS f2 ON f2.id = f.route_id';

					$where2		=	' AND JSON_EXTRACT(f2.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').') = '.JCckDatabase::quote( strtolower( $segments[$idx] ) );
				}
			}
		} elseif ( $n == 2 || $n == 3 ) {
			if ( $config['doSEF'][0] == '5' ) {
				$join				.=	' LEFT JOIN #__cck_core AS c ON (c.storage_location = "joomla_user" AND c.pk = a.created_by)'
									.	' LEFT JOIN #__content AS d ON d.id = c.pkb';
				$where				=	' AND d.alias = '.JCckDatabase::quote( (string)$segments[0] );
			} elseif ( $config['doSEF'][0] == '3' ) {
				$join				=	' LEFT JOIN #__cck_core AS b on b.'.$config['join_key'].' = a.id';
				$where				=	' AND b.cck = '.JCckDatabase::quote( (string)$segments[0] );
			} else {
				$join				=	' LEFT JOIN #__categories AS b on b.id = a.catid';

				if ( $config['doSEF'] == '1'  ) {
					$where			=	' AND b.id = '.(int)$segments[0];
					$vars['catid']	=	$segments[0];
				} else {
					$idx			=	 $n == 3 ? 1 : 0;
					$segments[$idx]	=	str_replace( ':', '-', $segments[$idx] );

					if ( $isMultiLanguage && $isMultiAlias ) {
						$join		=	' LEFT JOIN #__cck_store_item_categories AS b on b.id = a.catid';

						if ( $legacy ) {
							$where		=	' AND b.alias_'.$lang_sef.' = '.JCckDatabase::quote( $segments[$idx] );
						} else {
							$where		=	' AND JSON_EXTRACT(b.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').') = '.JCckDatabase::quote( strtolower( $segments[$idx] ) );
						}
						
					} else {
						$where		=	' AND b.alias = '.JCckDatabase::quote( $segments[$idx] );
					}

					if ( $n == 3 ) {
						$idx			=	0;
						$segments[$idx]	=	str_replace( ':', '-', $segments[$idx] );

						if ( $isMultiLanguage && $isMultiAlias ) {
							$join		.=	' LEFT JOIN #__categories AS b2 on b2.id = a.catid';
							$join		.=	' LEFT JOIN #__categories AS c on c.id = b2.parent_id';
							$join		.=	' LEFT JOIN #__cck_store_item_categories AS d on d.id = c.id';

							if ( $legacy ) {
								$where		.=	' AND d.alias_'.$lang_sef.' = '.JCckDatabase::quote( $segments[$idx] );
							} else {
								$where		.=	' AND JSON_EXTRACT(d.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').') = '.JCckDatabase::quote( strtolower( $segments[$idx] ) );
							}
							
						} else {
							$where		.=	' AND c.alias = '.JCckDatabase::quote( $segments[$idx] );
						}						
					}
				}
			}
		}
		
		// Retrieve Content Type(s)
		if ( isset( $config['sef_types'] ) && $config['sef_types'] != '' ) {
			if ( $config['doSEF'][0] != '3' ) {
				$join	.=	' LEFT JOIN #__cck_core AS e on e.'.$config['join_key'].' = a.id';
				$parts	=	explode( '/', $config['sef_types'] );
				$count	=	count( $parts );
				$where	.=	' AND e.cck IN ("'.str_replace( ',', '","', $parts[($count - 1)] ).'")';

				if ( $join2 && $where2 ) {
					$join2	.=	' LEFT JOIN #__cck_core AS e2 ON (e2.'.$config['join_key'].' = f2.id AND e2.storage_location = "joomla_article")';
					$where2	.=	' AND e2.cck IN ("'.str_replace( ',', '","', $parts[($count - 1)] ).'")';
				}
			}
		}

		// Identity the PK
		if ( self::$sef[$config['doSEF']] == 'full' ) {
			$idArray			=	explode( ':', $segments[$n - 1], 2 );
			$id 				=	(int)$idArray[0];
			$alias				=	substr( $idArray[0], strlen( $id ) + 1 );

			if ( $id ) {
				$where			.=	' AND a.alias = '.JCckDatabase::quote( $alias );
			}
			if ( $where != '' ) {
				$where			=	' WHERE a.id = '.JCckDatabase::clean( $id ).$where;
			}
		} elseif ( self::$sef[$config['doSEF']] == 'id' ) {
			if ( is_numeric( $segments[$n - 1] ) ) {
				$id				=	$segments[$n - 1];

				if ( $where != '' ) {
					$where		=	' WHERE a.id = '.JCckDatabase::clean( $id ).$where;
				}
			}
		} else {
			$segments[$n - 1]	=	str_replace( ':', '-', $segments[$n - 1] );

			if ( $isMultiLanguage && $isMultiAlias ) {
				$join		.=	' LEFT JOIN #__cck_store_item_content AS f on f.id = a.id';

				if ( $legacy ) {
					$where	=	' WHERE f.alias_'.$lang_sef.' = '.JCckDatabase::quote( $segments[$n - 1] ).$where;
				} else {
					$where	=	' WHERE JSON_EXTRACT(f.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').') = '.JCckDatabase::quote( strtolower( $segments[$n - 1] ) ).$where;
				}

				if ( $doSub && $n === 1 ) {
					$where	.=	' AND f.route_id = 0';
				}
			} else {
				$where		=	' WHERE a.alias = '.JCckDatabase::quote( $segments[$n - 1] ).$where;
			}
			if ( $isMultiLanguage ) {
				$where		.=	' AND (a.language = "*" OR a.language = "'.$lang_tag.'")';
			}
		}

		// Set the ID
		if ( $where != '' ) {
			$vars['id']	=	(int)JCckDatabaseCache::loadResult( 'SELECT a.id FROM '.self::$table.' AS a'.$join.$join2.$where.$where2 );
		} else {
			$vars['id']	=	$id;
		}
		if ( ( JCck::on( '4.0' ) || !JCck::on( '4.0' ) && JComponentHelper::getParams( 'com_content' )->get( 'sef_advanced', 0 ) ) && ( $n == 1 || $n == 2 ) ) {
			$segments	=	array();
		}
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
	
	// _getRoute
	public static function _getRoute( $sef, $itemId, $id, $path = '', $option = '', $lang_tag = '' )
	{
		static $isAdmin	=	-1;
		static $itemIds	=	array();

		if ( $isAdmin == -1 ) {
			$isAdmin	=	JFactory::getApplication()->isClient( 'administrator' );
		}
		
		if ( $itemId && !$isAdmin && $sef != '10' ) {
			$mode	=	@$sef[0];
			$index	=	$itemId.'_'.$mode;

			if ( !isset( $itemIds[$index] ) ) {
				$menu				=	JFactory::getApplication()->getMenu(); /* JMenu::getInstance( 'site' ); */
				$item				=	$menu->getItem( $itemId );

				if ( !is_object( $item ) ) {
					$itemIds[$index]	=	'/';
				} else {
					$app		=	JFactory::getApplication();
					$isChild	=	false;

					if ( $item->query['view'] == 'article' ) {
						$item2	=	$menu->getItem( $item->parent_id );

						if ( is_object( $item2 ) && @$item2->query['option'] == 'com_cck' && @$item2->query['view'] == 'list' ) {
							$isChild	=	true;
							$itemId		=	$item2->id;
						}
					}
					if ( !$isChild ) {
						$itemIds[$index]	=	'option='.$item->query['option'].'&view='.$item->query['view'];
					}
				}
			}
			if ( isset( $itemIds[$index] ) ) {
				// Check Query
				if ( $itemIds[$index] == '/' ) {
					return ''; /* No Link */
				} elseif ( $itemIds[$index] == 'option=com_content&view=article' ) {
					return 'index.php?Itemid='.$itemId; /* Direct Link */
				}
			}
		}
		$option	=	( $option != '' ) ? 'option='.$option.'&' : '';
		$link	=	'index.php?'.$option.'view=article'.$path;

		if ( $id ) {
			$link	.=	'&id='.$id; 
		}
		if ( $itemId ) {
			$link	.=	'&Itemid='.$itemId;
		}
		if ( $lang_tag ) {
			$link	.=	'&lang='.$lang_tag;
		}

		return $link;
	}

	// _prepareRoute
	protected static function _prepareRoute( $item, $sef, $itemId, $config = array(), $lang_tag = '' )
	{
		$route		=	'';

		if ( is_numeric( $item ) ) {
			$join		=	'';
			$sef_slug	=	false;
			$select		=	( $sef[0] == '5' ) ? ', a.created_by, dd.alias AS author_alias' : '';

			if ( JCckDevHelper::isMultilingual() ) {
				if ( !$lang_tag ) {
					$lang_tag	=	JFactory::getLanguage()->getTag();
				}
				if ( !isset( $config['sef_aliases'] ) && (int)$itemId ) {
					$menu		=	JFactory::getApplication()->getMenu();
					$menu_item	=	$menu->getItem( $itemId );

					if ( is_object( $menu_item ) && $menu_item->query['search'] ) {
						$params	=	JCckDevHelper::getRouteParams( $menu_item->query['search'], $menu_item->getParams()->get( 'sef', '' ) );

						$config['sef_aliases']	=	$params['sef_aliases'];
					}
				}
				if ( $config['sef_aliases'] == 2 || ( $config['sef_aliases'] == 1 && $lang_tag != JComponentHelper::getParams( 'com_languages' )->get( 'site', 'en-GB' ) ) ) {
					$legacy		=	(int)JCck::getConfig_Param( 'core_legacy_routing', '2018' );
					$sef_slug	=	true;

					if ( $legacy && $legacy <= 2018 ) {
						$languages	=	JLanguageHelper::getLanguages( 'lang_code' );
						$lang_sef	=	isset( $languages[$lang_tag] ) ? $languages[$lang_tag]->sef : substr( $lang_tag, 0, 2 );
						$select		.=	', c.alias_'.$lang_sef.' AS alias, d.alias_'.$lang_sef.' AS category_alias';
					} else {
						$select		.=	', JSON_UNQUOTE(JSON_EXTRACT(c.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS alias'
									.	', JSON_UNQUOTE(JSON_EXTRACT(d.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS category_alias'
									.	', JSON_UNQUOTE(JSON_EXTRACT(g2.aliases, '.JCckDatabase::quote('$."'.$lang_tag.'"').')) AS category_parent_alias';
					}
				
					$join		.=	' LEFT JOIN #__categories AS b ON b.id = a.catid'
								.	' LEFT JOIN #__cck_store_item_content AS c ON c.id = a.id'
								.	' LEFT JOIN #__cck_store_item_categories AS d ON d.id = a.catid';
				}
			}
			if ( !$join ) {
				$join	.=	' LEFT JOIN #__categories AS b ON b.id = a.catid';
				$select	.=	', a.alias, b.alias AS category_alias';
			}

			$query	=	'SELECT a.id, a.catid, a.language'.$select
					.	' FROM #__content AS a'
					.	$join
					;

			if ( $sef[0] == '5' ) {
				$query	.=	' LEFT JOIN #__cck_core AS cc ON (c.storage_location = "joomla_user" AND cc.pk = a.created_by)'
						.	' LEFT JOIN #__content AS dd ON d.id = cc.pkb';
			}

			if ( $sef_slug ) {
				$query			.=	' LEFT JOIN #__categories AS b2 on b2.id = b.parent_id'
								.	' LEFT JOIN #__cck_store_item_categories AS g2 on g2.id = b2.id'
								;
			}

			$query	.=	' WHERE a.id = '.(int)$item;
			
			$item	=	JCckDatabaseCache::loadObject( $query );

			if ( empty( $item ) ) {
				return '';
			}
		}
		if ( !is_object( $item ) ) {
			return '';
		}
		
		$pk			=	( isset( $item->pk ) ) ? $item->pk : $item->id;
		$item->slug	=	( $item->alias ) ? $pk.':'.$item->alias : $pk;

		if ( $sef ) {
			if ( $sef == '0' || $sef == '1' ) {
				$path	=	'&catid='.$item->catid;
			} elseif ( $sef[0] == '5' ) {
				$path	=	'&userid='.( isset( $item->author_alias ) ? $item->author_alias : $item->created_by );
			} elseif ( $sef[0] == '4' || $sef[0] == '8' ) {
				$path	=	isset( $item->category_alias ) ? $item->category_alias : $item->catid;
				if ( isset( $item->category_parent_alias ) && $item->category_parent_alias ) {
					$path	=	$item->category_parent_alias.'/'.$path;
				}
				$path	=	'&catid='.$path;
			} elseif ( $sef[0] == '3' ) {
				$path	=	( $config['type'] ) ? '&typeid='.$config['type'] : '';
			} else {
				$path	=	'';
			}
			$route		=	self::_getRoute( $sef, $itemId, $item->slug, $path, '', $lang_tag );
		} else {
			if ( !JCck::on( '4.0' ) ) {
				require_once JPATH_SITE.'/components/com_content/helpers/route.php';
			}

			$route		=	ContentHelperRoute::getArticleRoute( $item->slug, $item->catid, $item->language );
		}

		return $route;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// access
	public static function access( $pk, $checkAccess = true )
	{
		$db		=	JFactory::getDbo();
		$now	=	substr( JFactory::getDate()->toSql(), 0, -3 );

		$states	=	self::getStaticParams()->get( 'allowed_status', '1,2' );
		$states	=	explode( ',', $states );
		$states	=	ArrayHelper::toInteger( $states );
		$states	=	( count( $states ) > 1 ) ? 'IN ('.implode( ',', $states ).')' : '= '.(int)$states[0];
		$query	=	'SELECT '.self::$key
				.	' FROM '.self::$table
				.	' WHERE '.self::$key.' = '.(int)$pk
				.	' AND '.self::$status.' '.$states
				.	' AND ( publish_up '.JCckDatabase::null().' OR publish_up <= '.$db->quote( $now ).' )'
				.	' AND ( publish_down '.JCckDatabase::null().' OR publish_down >= '.$db->quote( $now ).' )'
				;
		
		if ( $checkAccess ) {
			$query	.=	' AND '.self::$access.' IN ('.implode( ',', JFactory::getUser()->getAuthorisedViewLevels() ).')';
		}

		return (int)JCckDatabaseCache::loadResult( $query );
	}

	// authorise
	public static function authorise( $rule, $pk )
	{
		return JFactory::getUser()->authorise( $rule, 'com_content.article.'.$pk );
	}
}
?>