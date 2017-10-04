<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgContentCCK extends JPlugin
{
	protected $cache	=	false;
	protected $loaded	=	array();
	protected $title	=	'';
	
	// onContentAfterSave
	public function onContentAfterSave( $context, $article, $isNew )
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event	=	'onContentAfterSave';

			$this->_process( array( 'event', 'context', 'article', 'isNew' ), $event, $context, $article, $isNew );
		}
	}

	// onContentBeforeSave
	public function onContentBeforeSave( $context, $article, $isNew )
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event	=	'onContentBeforeSave';

			$this->_process( array( 'event', 'context', 'article', 'isNew' ), $event, $context, $article, $isNew );
		}
	}

	// onCckConstructionAfterDelete
	public function onCckConstructionAfterDelete( $context, $item )
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event	=	'onCckConstructionAfterDelete';

			$this->_process( array( 'event', 'context', 'item' ), $event, $context, $item );
		}
	}

	// onCckConstructionBeforeDelete
	public function onCckConstructionBeforeDelete( $context, $item )
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event	=	'onCckConstructionBeforeDelete';

			$this->_process( array( 'event', 'context', 'item' ), $event, $context, $item );
		}
	}

	// onCckConstructionAfterSave
	public function onCckConstructionAfterSave( $context, $item, $isNew )
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event	=	'onCckConstructionAfterSave';

			$this->_process( array( 'event', 'context', 'item', 'isNew' ), $event, $context, $item, $isNew );
		}
	}

	// onCckConstructionBeforeSave
	public function onCckConstructionBeforeSave( $context, $item, $isNew )
	{
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event	=	'onCckConstructionBeforeSave';
			
			$this->_process( array( 'event', 'context', 'item', 'isNew' ), $event, $context, $item, $isNew );
		}
	}

	// onContentAfterDelete
	public function onContentAfterDelete( $context, $item )
	{
		if ( empty( $context ) ) {
			return false;
		}

		$db		=	JFactory::getDbo();
		$query	=	$db->getQuery( true )->select( 'name AS object' )
										 ->from( '#__cck_core_objects' )
										 ->where( 'context = '. $db->quote( $context ) );

		$db->setQuery( $query );
		$object	=	$db->loadResult();
		
		if ( ! $object ) {
			return true;
		}
		
		$table_key	=	$item->getKeyName();
		$table_name	=	$item->getTableName();
		$pk			= 	$item->$table_key;
		$base 		= 	str_replace( '#__', '', $table_name );
		
		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$object.'/'.$object.'.php';
		$properties		= 	array( 'bridge_object', 'custom' );
		$properties		= 	JCck::callFunc( 'plgCCK_Storage_Location'.$object, 'getStaticProperties', $properties );
		$bridge_object 	= 	$properties['bridge_object'];
		$custom 		= 	$properties['custom'];
		$parent			=	'';
		$pkb			=	0;
		$type			=	'';

		// Core
		if ( $custom ) {
			preg_match( '#::cck::(\d+)::/cck::#U', $item->$custom, $matches );
			$id		=	$matches[1];

			if ( ! $id ) {
				return true;
			}

			$table	=	JCckTable::getInstance( '#__cck_core', 'id', $id );
			$type	=	$table->cck;
			$pkb	=	(int)$table->pkb;
		} else {
			$table	=	JCckTable::getInstance( '#__cck_core' );
			if ( $table->load( array( 'pk'=>$pk, 'storage_location'=>$object ) ) ) {
				$type	=	$table->cck;
				$pkb	=	(int)$table->pkb;
			}
		}

		if ( $table->pk > 0 ) {
			// -- Leave nothing behind
			if ( $type != '' ) {
				require_once JPATH_LIBRARIES.'/cck/base/form/form.php';

				JPluginHelper::importPlugin( 'cck_field' );
				JPluginHelper::importPlugin( 'cck_storage' );
				JPluginHelper::importPlugin( 'cck_storage_location' );

				$config		=	array(
									'pk'=>$table->pk,
									'storages'=>array(),
									'type'=>$table->cck
								);
				$dispatcher	=	JEventDispatcher::getInstance();
				$parent		=	JCckDatabase::loadResult( 'SELECT parent FROM #__cck_core_types WHERE name = "'.$type.'"' );
				$fields		=	CCK_Form::getFields( array( $type, $parent ), 'all', -1, '', true );
				
				if ( count( $fields ) ) {
					foreach ( $fields as $field ) {
						$Pt		=	$field->storage_table;
						$value	=	'';
						
						/* Yes but, .. */

						if ( $Pt && ! isset( $config['storages'][$Pt] ) ) {
							$config['storages'][$Pt]	=	'';
							
							if ( $Pt == $table_name ) {
								$config['storages'][$Pt]	=	$item;
							} else {
								$dispatcher->trigger( 'onCCK_Storage_LocationPrepareDelete', array( &$field, &$config['storages'][$Pt], $pk, &$config ) );	
							}
						}
						$dispatcher->trigger( 'onCCK_StoragePrepareDelete', array( &$field, &$value, &$config['storages'][$Pt], &$config ) );
						$dispatcher->trigger( 'onCCK_FieldDelete', array( &$field, $value, &$config, array() ) );
					}
				}
			}
			// -- Leave nothing behind

			$table->delete();

			if ( $pkb > 0 ) {
				if ( $bridge_object == 'joomla_category' ) {
					JLoader::register( 'JTableCategory', JPATH_PLATFORM.'/joomla/database/table/category.php' );

					$bridge	=	JTable::getInstance( 'Category' );
					$bridge->load( $pkb );
					$bridge->delete( $pkb );
				} elseif ( $bridge_object == 'joomla_article' ) {
					JLoader::register( 'JTableContent', JPATH_PLATFORM.'/joomla/database/table/content.php' );

					$bridge	=	JTable::getInstance( 'Content' );
					$bridge->load( $pkb );
					$bridge->delete( $pkb );
				}
			}
		}

		// Processing
		JLoader::register( 'JCckToolbox', JPATH_PLATFORM.'/cms/cck/toolbox.php' );
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$event		=	'onContentAfterDelete';
			$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
			if ( isset( $processing[$event] ) ) {
				$data	=	$item;	/* Avoid B/C issue */

				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );

						include JPATH_SITE.$p->scriptfile;	/* Variables: $id, $item, $pk, $type */
					}
				}
			}
		}
		
		$tables	=	JCckDatabase::loadColumn( 'SHOW TABLES' );
		$prefix	= 	JFactory::getConfig()->get( 'dbprefix' );
		
		if ( in_array( $prefix.'cck_store_item_'.$base, $tables ) ) {
			$table	=	JCckTable::getInstance( '#__cck_store_item_'.$base, 'id', $pk );
			if ( $table->id ) {
				$table->delete();
			}
		}
		
		if ( in_array( $prefix.'cck_store_form_'.$type, $tables ) ) {
			$table	=	JCckTable::getInstance( '#__cck_store_form_'.$type, 'id', $pk );
			if ( $table->id ) {
				$table->delete();
			}
		}

		if ( $parent != '' && in_array( $prefix.'cck_store_form_'.$parent, $tables ) ) {
			$table	=	JCckTable::getInstance( '#__cck_store_form_'.$parent, 'id', $pk );
			if ( $table->id ) {
				$table->delete();
			}
		}
		
		return true;
	}

	// onContentBeforeDisplay
	public function onContentBeforeDisplay( $context, &$article, &$params, $limitstart = 0 )
	{
		if ( JCck::getConfig_Param( 'hide_edit_icon', 0 ) ) {
			if ( isset( $article->params ) && is_object( $article->params ) ) {
				$article->params->set( 'access-edit', false );
			}
		}
		
		return '';
	}
	
	// onContentPrepare
	public function onContentPrepare( $context, &$article, &$params, $limitstart = 0 )
	{
		if ( strpos( $article->text, '/cck' ) === false ) {
			return true;
		}
		
		$this->_prepare( $context, $article, $params, $limitstart );
	}
	
	// _prepare
	protected function _prepare( $context, &$article, &$params, $page = 0 )
	{
		$property	=	'text';
		preg_match( '#::cck::(\d+)::/cck::#U', $article->$property, $matches );
	  	if ( ! @$matches[1] ) {
			return;
		}

		$join			=	' LEFT JOIN #__cck_core_folders AS f ON f.id = b.folder';
		$join_select	=	', f.app as folder_app';
		$query			=	'SELECT a.id, a.pk, a.pkb, a.cck, a.storage_location, a.store_id, a.author_id AS author, b.id AS type_id, b.alias AS type_alias, b.indexed, b.parent, b.stylesheets,'
						.	' b.options_content, b.options_intro, c.template AS content_template, c.params AS content_params, d.template AS intro_template, d.params AS intro_params'.$join_select
						.	' FROM #__cck_core AS a'
						.	' LEFT JOIN #__cck_core_types AS b ON b.name = a.cck'
						.	' LEFT JOIN #__template_styles AS c ON c.id = b.template_content'
						.	' LEFT JOIN #__template_styles AS d ON d.id = b.template_intro'
						.	$join
						.	' WHERE a.id = "'.(string)$matches[1].'"'
						;
		$cck			=	JCckDatabase::loadObject( $query );

		if ( !is_object( $cck ) ) {
			return;
		}
		$contentType	=	(string)$cck->cck;
		$parent_type	=	(string)$cck->parent;
		
		if ( ! $contentType ) {
			return;
		}
		
		JPluginHelper::importPlugin( 'cck_storage_location' );
		if ( $context == 'text' ) {
			$client	=	'intro';
		} elseif ( $context == 'com_finder.indexer' ) {
			if ( $cck->indexed == 'none' ) {
				$article->$property		=	'';
				return;
			}
			$client	=	( empty( $cck->indexed ) ) ? 'intro' : $cck->indexed;
		} else {
			if ( $cck->storage_location != '' ) {
				$properties	=	array( 'contexts' );
				$properties	=	JCck::callFunc( 'plgCCK_Storage_Location'.$cck->storage_location, 'getStaticProperties', $properties );
				$client		=	( in_array( $context, $properties['contexts'] ) ) ? 'content' : 'intro';
			} else {
				$client		=	'intro';
			}
		}
		
		// Fields
		$app 	=	JFactory::getApplication();
		$fields	=	array();
		$lang	=	JFactory::getLanguage();
		$user	=	JFactory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );
		
		if ( $client == 'intro' && $this->cache ) {
			$query		=	'SELECT cc.*, c.ordering, c.label as label2, c.variation, c.link, c.link_options, c.markup, c.markup_class, c.typo, c.typo_label, c.typo_options, c.access, c.restriction, c.restriction_options, c.position'
						.	' FROM #__cck_core_type_field AS c'
						.	' LEFT JOIN #__cck_core_types AS sc ON sc.id = c.typeid'
						.	' LEFT JOIN #__cck_core_fields AS cc ON cc.id = c.fieldid'
						.	' WHERE sc.name = "'.$contentType.'" AND sc.published = 1 AND c.client = "'.$client.'" AND c.access IN ('.$access.')'
						.	' ORDER BY c.ordering ASC'
						;
			$fields		=	JCckDatabaseCache::loadObjectList( $query, 'name' );	//#
			if ( ! count( $fields ) && $client == 'intro' ) {
				$client	=	'content';
				$query	=	'SELECT cc.*, c.ordering, c.label as label2, c.variation, c.link, c.link_options, c.markup, c.markup_class, c.typo, c.typo_label, c.typo_options, c.access, c.restriction, c.restriction_options, c.position'
						.	' FROM #__cck_core_type_field AS c'
						.	' LEFT JOIN #__cck_core_types AS sc ON sc.id = c.typeid'
						.	' LEFT JOIN #__cck_core_fields AS cc ON cc.id = c.fieldid'
						.	' WHERE sc.name = "'.$contentType.'" AND sc.published = 1 AND c.client = "'.$client.'" AND c.access IN ('.$access.')'
						.	' ORDER BY c.ordering ASC'
						;
				$fields	=	JCckDatabaseCache::loadObjectList( $query, 'name' );	//#
			}
		} else {
			if ( $parent_type != '' ) {
				$w_type	=	'(sc.name = "'.$contentType.'" OR sc.name = "'.$parent_type.'")';
			} else {
				$w_type	=	'sc.name = "'.$contentType.'"';	
			}
			$query		=	'SELECT cc.*, c.ordering, c.label as label2, c.variation, c.link, c.link_options, c.markup, c.markup_class, c.typo, c.typo_label, c.typo_options, c.access, c.restriction, c.restriction_options, c.position'
						.	' FROM #__cck_core_type_field AS c'
						.	' LEFT JOIN #__cck_core_types AS sc ON sc.id = c.typeid'
						.	' LEFT JOIN #__cck_core_fields AS cc ON cc.id = c.fieldid'
						.	' WHERE '.$w_type.' AND sc.published = 1 AND c.client = "'.$client.'" AND c.access IN ('.$access.')'
						.	' ORDER BY'
						;
			if ( $parent_type != '' ) {
				$query	.=	' c.typeid ASC,';
			}
			$query		.=	' c.ordering ASC';
			$fields		=	JCckDatabase::loadObjectList( $query, 'name' );	//#
			if ( ! count( $fields ) && $client == 'intro' ) {
				$client	=	'content';
				$query	=	'SELECT cc.*, c.ordering, c.label as label2, c.variation, c.link, c.link_options, c.markup, c.markup_class, c.typo, c.typo_label, c.typo_options, c.access, c.restriction, c.restriction_options, c.position'
						.	' FROM #__cck_core_type_field AS c'
						.	' LEFT JOIN #__cck_core_types AS sc ON sc.id = c.typeid'
						.	' LEFT JOIN #__cck_core_fields AS cc ON cc.id = c.fieldid'
						.	' WHERE '.$w_type.' AND sc.published = 1 AND c.client = "'.$client.'" AND c.access IN ('.$access.')'
						.	' ORDER BY'
						;
				if ( $parent_type != '' ) {
					$query	.=	' c.typeid ASC,';
				}
				$query	.=	' c.ordering ASC';
				$fields	=	JCckDatabase::loadObjectList( $query, 'name' );	//#
			}
		}
		if ( !isset( $this->loaded[$contentType.'_'.$client.'_options'] ) ) {
			$lang->load( 'pkg_app_cck_'.$cck->folder_app, JPATH_SITE, null, false, false );
			$registry	=	new JRegistry;
			$registry->loadString( $cck->{'options_'.$client} );
			$this->loaded[$contentType.'_'.$client.'_options']	=	$registry->toArray();
			if ( isset( $this->loaded[$contentType.'_'.$client.'_options']['title'] ) ) {
				if ( $this->loaded[$contentType.'_'.$client.'_options']['title'] != '' && $this->loaded[$contentType.'_'.$client.'_options']['title'][0]	==	'{' ) {
					$titles		=	json_decode( $this->loaded[$contentType.'_'.$client.'_options']['title'] );
					$lang_tag	=	JFactory::getLanguage()->getTag();
					$this->loaded[$contentType.'_'.$client.'_options']['title']	=	( isset( $titles->{$lang_tag} ) ) ? $titles->{$lang_tag} : '';
				}
			}
			if ( isset( $this->loaded[$contentType.'_'.$client.'_options']['sef'] ) ) {
				if ( $this->loaded[$contentType.'_'.$client.'_options']['sef'] == '' ) {
					$this->loaded[$contentType.'_'.$client.'_options']['sef']	=	JCck::getConfig_Param( 'sef', '2' );
				}
			}
		}
		
		// Template
		$tpl['home']							=	$app->getTemplate();
		$tpl['folder']							=	$cck->{$client.'_template'};
		$tpl['params']							=	json_decode( $cck->{$client.'_params'}, true );
		$tpl['params']['rendering_css_core']	=	$cck->stylesheets;
		if ( file_exists( JPATH_SITE.'/templates/'.$tpl['home'].'/html/tpl_'.$tpl['folder'] ) ) {
			$tpl['folder']	=	'tpl_'.$tpl['folder'];
			$tpl['root']	=	JPATH_SITE.'/templates/'.$tpl['home'].'/html';
		} else {
			$tpl['root']	=	JPATH_SITE.'/templates';
		}
		$tpl['path']		=	$tpl['root'].'/'.$tpl['folder'];
		if ( ! $tpl['folder'] || ! file_exists( $tpl['path'].'/index.php' ) ) {
			$article->$property		=	str_replace( $article->$property, 'Template Style does not exist. Open the Content Type & save it again. (Intro + Content views)', $article->$property );

			if ( JCck::on( '3.8' ) && isset( $article->introtext ) ) {
				$article->introtext	=	'';
			}
			return;
		}
		
		$this->_render( $context, $article, $tpl, $contentType, $fields, $property, $client, $cck, $parent_type );
	}
	
	// _process
	protected function _process()
	{
		$args	=	func_get_args();
		
		if ( count( $args ) ) {
			$i		=	0;
			$keys	=	array_shift( $args );

			if ( count( $keys ) ) {
				foreach ( $args as $arg ) {
					${$keys[$i++]}	=	$arg;
				}
			}
		}
		$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
		
		unset( $args );

		if ( isset( $processing[$event] ) ) {
			foreach ( $processing[$event] as $p ) {
				if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
					$options	=	new JRegistry( $p->options );

					include JPATH_SITE.$p->scriptfile;
				}
			}
		}
	}

	// _render
	protected function _render( $context, &$article, $tpl, $contentType, $fields, $property, $client, $cck, $parent_type )
	{
		$app		=	JFactory::getApplication();
		$dispatcher	=	JEventDispatcher::getInstance();
		$user		=	JFactory::getUser();
		$params		=	array( 'template'=>$tpl['folder'], 'file'=>'index.php', 'directory'=>$tpl['root'] );
		
		$lang	=	JFactory::getLanguage();
		$lang->load( 'com_cck_default', JPATH_SITE );
		
		JPluginHelper::importPlugin( 'cck_field' );
		JPluginHelper::importPlugin( 'cck_field_link' );
		JPluginHelper::importPlugin( 'cck_field_restriction' );
		$p_sef		=	isset( $this->loaded[$contentType.'_'.$client.'_options']['sef'] ) ? $this->loaded[$contentType.'_'.$client.'_options']['sef'] : JCck::getConfig_Param( 'sef', '2' );
		$p_title	=	isset( $this->loaded[$contentType.'_'.$client.'_options']['title'] ) ? $this->loaded[$contentType.'_'.$client.'_options']['title'] : '';
		$p_typo		=	isset( $this->loaded[$contentType.'_'.$client.'_options']['typo'] ) ? $this->loaded[$contentType.'_'.$client.'_options']['typo'] : 1;
		if ( $p_typo ) {
			JPluginHelper::importPlugin( 'cck_field_typo' );
		}
		
		jimport( 'cck.rendering.document.document' );
		$doc		=	CCK_Document::getInstance( 'html' );
		$positions	=	array();
		if ( $parent_type != '' ) {
			$w_type	=	'(b.name = "'.$contentType.'" OR b.name = "'.$parent_type.'")';
		} else {
			$w_type	=	'b.name = "'.$contentType.'"';
		}
		if ( $client == 'intro' /* && $this->cache */ ) {
			$positions_more	=	JCckDatabaseCache::loadObjectList( 'SELECT * FROM #__cck_core_type_position AS a LEFT JOIN #__cck_core_types AS b ON b.id = a.typeid'
																 . ' WHERE '.$w_type.' AND a.client ="'.$client.'"', 'position' );	// todo::improve
		} else {
			$positions_more	=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_type_position AS a LEFT JOIN #__cck_core_types AS b ON b.id = a.typeid'
															. ' WHERE '.$w_type.' AND a.client ="'.$client.'"', 'position' );	// todo::improve
		}

		// Fields
		if ( count( $fields ) ) {
			JPluginHelper::importPlugin( 'cck_storage' );
			$config	=	array( 'author'=>$cck->author,
							   'client'=>$client,
   							   'doSEF'=>$p_sef,
							   'doTranslation'=>JCck::getConfig_Param( 'language_jtext', 0 ),
							   'doTypo'=>$p_typo,
							   'error'=>0,
							   'fields'=>array(),
							   'id'=>$cck->id,
							   'isNew'=>0,
							   'Itemid'=>$app->input->getInt( 'Itemid', 0 ),
							   'location'=>$cck->storage_location,
							   'pk'=>$cck->pk,
							   'pkb'=>$cck->pkb,
							   'storages'=>array(),
							   'store_id'=>(int)$cck->store_id,
							   'type'=>$cck->cck,
							   'type_id'=>(int)$cck->type_id,
							   'type_alias'=>( $cck->type_alias ? $cck->type_alias : $cck->cck )
							);
			
			foreach ( $fields as $field ) {
				$field->typo_target	=	'value';
				$fieldName			=	$field->name;
				$value				=	'';
				if ( $fieldName ) {
					$Pt	=	$field->storage_table;
					if ( $Pt && ! isset( $config['storages'][$Pt] ) ) {
						$config['storages'][$Pt]	=	'';
						$dispatcher->trigger( 'onCCK_Storage_LocationPrepareContent', array( &$field, &$config['storages'][$Pt], $config['pk'], &$config, &$article ) );
					}
					
					$dispatcher->trigger( 'onCCK_StoragePrepareContent', array( &$field, &$value, &$config['storages'][$Pt] ) );
					if ( is_string( $value ) ) {
						$value		=	trim( $value );
					}
					
					$hasLink	=	( $field->link != '' ) ? 1 : 0;
					$dispatcher->trigger( 'onCCK_FieldPrepareContent', array( &$field, $value, &$config ) );
					$target		=	$field->typo_target;
					if ( $hasLink ) {
						$dispatcher->trigger( 'onCCK_Field_LinkPrepareContent', array( &$field, &$config ) );
						if ( $field->link ) {
							JCckPluginLink::g_setHtml( $field, $target );
						}
					}
					if ( @$field->typo && ( $field->$target !== '' || $field->typo_label == -2 ) && $p_typo ) {
						$dispatcher->trigger( 'onCCK_Field_TypoPrepareContent', array( &$field, $field->typo_target, &$config ) );
					} else {
						$field->typo	=	'';
					}
					$position					=	$field->position;
					$positions[$position][]		=	$fieldName;

					// Was it the last one?
					if ( $config['error'] ) {
						break;
					}
				}
			}
			
			// Merge
			if ( count( $config['fields'] ) ) {
				foreach ( $config['fields'] as $k=>$v ) {
					if ( $v->restriction != 'unset' ) {
						$fields[$k]	=	$v;
					}
				}
				$config['fields']	=	NULL;
				unset( $config['fields'] );
			}
		}
		
		// BeforeRender
		if ( isset( $config['process']['beforeRenderContent'] ) && count( $config['process']['beforeRenderContent'] ) ) {
			JCckDevHelper::sortObjectsByProperty( $config['process']['beforeRenderContent'], 'priority' );

			foreach ( $config['process']['beforeRenderContent'] as $process ) {
				if ( $process->type ) {
					JCck::callFunc_Array( 'plg'.$process->group.$process->type, 'on'.$process->group.'BeforeRenderContent', array( $process->params, &$fields, &$config['storages'], &$config ) );
				}
			}
		}
		
		// Set Title
		if ( $p_title != '' && isset( $fields[$p_title]->value ) && !empty( $fields[$p_title]->value ) ) {
 			$this->title	=	$fields[$p_title]->value;
 		}
		if ( $this->title ) {
			if ( is_object( $article ) && isset( $article->title ) ) {
				$article->title	=	$this->title;
			} else {
				JFactory::getDocument()->setTitle( $this->title );
			}
		}
		
		// Finalize
		$doc->fields	=	&$fields;
		$infos			=	array( 'context'=>$context, 'params'=>$tpl['params'], 'path'=>$tpl['path'], 'root'=>JUri::root( true ), 'template'=>$tpl['folder'], 'theme'=>$tpl['home'] );
		$doc->finalize( 'content', $contentType, $client, $positions, $positions_more, $infos, $cck->id );
		
		$data					=	$doc->render( false, $params );
		$article->$property		=	str_replace( $article->$property, $data, $article->$property );
	}
}
?>
