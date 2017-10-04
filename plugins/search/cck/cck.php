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
class plgSearchCCK extends JPlugin
{
	// __construct
	public function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );
		
		$this->loadLanguage();
	}
	
	// onContentSearchAreas
	public function onContentSearchAreas()
	{
		static $areas	=	array( 'cck'=>'CCK' );
		
		return $areas;
	}
	
	// onContentSearch
	public function onContentSearch( $text = '', $phrase = '', $ordering = '', $areas = NULL, $fields = array(), $fields_order = array(), &$config = array(), $current = array(), $options = NULL, $user = NULL )
	{
		if ( is_array( $areas ) ) {
			if ( ! array_intersect( $areas, array_keys( $this->onContentSearchAreas() ) ) ) {
				return array();
			}
		} else {
			return array();
		}
		if ( !count( $fields ) ) {
			return array();
		}
		if ( ! $user ) {
			$user	=	JCck::getUser();
		}
		if ( !is_object( $options ) ) {
			$options	=	new JRegistry;
		}

		$app			=	JFactory::getApplication();
		$db				=	JFactory::getDbo();
		$dispatcher		=	JEventDispatcher::getInstance();
		$doClean		=	false;
		$doCount		=	(int)$options->get( 'count' );
		$doLimit		=	false;
		$limit			=	(int)$options->get( 'limit' );
		$doLimit		=	( $limit > 0 ) ? false : true;
		$hasGroup		=	false;
		$isLoadingMore	=	( $app->input->get( 'format' ) == 'raw' && $app->input->getInt( 'infinite' ) > 0 ) ? 1 : 0;
		if ( $isLoadingMore ) {
			$isLoadingMore	=	0; /* todo: only when it was triggered by the component itself */
		}
		$glues			=	1;
		$order			=	'';
		$order_string	=	'';
		$where			=	'';
		$w				=	-1;
		$where2			=	array();
		$t				=	1;
		$t2				=	1;
		$tables			=	array( '#__cck_core'=>array( '_'=>'t0', 'fields'=>array(), 'join'=>1 ) );
		$colums			=	array();
		
		if ( isset( $config['joins'][$current['stage']] ) && is_array( $config['joins'][$current['stage']] ) ) {
			foreach ( $config['joins'][$current['stage']] as $j ) {
				if ( $j->table ) {
					if ( !isset( $tables[$j->table] ) ) {
						$tables[$j->table]	=	array( '_'=>'t'.$t++, 'fields'=>array(), 'key'=>$j->column, 'join'=>2, 'join_key'=>$j->column2, 'join_table'=>$j->table2, 'join_and'=>@$j->and, 'join_type'=>@$j->type, 'join_mode'=>@$j->mode, 'join_query'=>@$j->query );

						if ( @$j->aka != '' && $j->aka != $j->table ) {
							$tables[$j->table]['table']	=	$j->aka;
						}
					} elseif ( @$j->and != '' ) {
						$tables[$j->table.'@'.md5( $j->and )]	=	array( '_'=>'t'.$t++, 'fields'=>array(), 'key'=>$j->column, 'join'=>2, 'join_key'=>$j->column2, 'join_table'=>$j->table2, 'join_and'=>$j->and, 'join_type'=>@$j->type, 'join_mode'=>@$j->mode, 'join_query'=>@$j->query );
					}
				}
			}
			$t2		=	count( $tables );
		}
		foreach ( $fields as $field ) {
			if ( !@$field->state ) {
				continue;
			}
			if ( $current['stage'] != (int)$field->stage ) {
				continue;
			}
			$hasSQL	=	true;
			$name2	=	( $field->match_collection != '' ) ? '\\\|[0-9]+\\\|'.$field->match_collection : '';
			// -
			if ( $field->live == 'stage' ) {
				$live_options	=	new JRegistry;
				$live_options->loadString( $field->live_options );
				$live_value		=	$live_options->get( 'value', $field->live_value );
				$live_value		=	( $live_value ) ? $live_value : 1;
				$value			=	$current['stages'][$live_value];

				if ( $value == '' && $live_options->get( 'default_value' ) != '' ) {
					$value		=	(string)$live_options->get( 'default_value' );
				}
			} else {
				$value	=	$field->value;
			}
			// -
			$Pf		=	$field->storage_field;
			$Pt		=	$field->storage_table;
			
			if ( ((( $value !== '' && $field->match_mode != 'none' ) || ( $field->match_mode == 'empty' || $field->match_mode == 'not_empty' || $field->match_mode == 'not_null' )) && $field->storage != 'none' )
			|| ( ( $field->type == 'search_operator' ) && $field->match_mode != 'none' ) ) {
				$glue	=	'';
				$sql	=	'';
				
				if ( $field->match_options != '' ) {
					$field->match_options	=	new JRegistry( $field->match_options );
				}
				
				// Glue
				if ( $glues == 1 ) {
					$glue	=	( $where != '' ) ? 'AND' : '';
					$where	.=	$glue;
					if ( $glue != '' ) {
						$where2[++$w]	=	$glue;
					}
					$glue	=	'';
				}
				
				// Sql
				if ( $field->type == 'search_generic' ) {
					$sql	=	'';

					if ( count( $field->children ) ) {
						$hasAka		=	false;
						$sqls		=	array();
						
						if ( count( $field->children_akas ) ) {
							if ( strpos( $value, ' ' ) !== false ) {
								$values	=	explode( ' ', $value );
							} else {
								$values	=	array( 0=>$value );
							}


							if ( count( $values ) ) {
								foreach ( $values as $key=>$v ) {
									$values[$key]	=	array( 'aka'=>false, 'sql'=>'', 'str'=>$v );

									foreach ( $field->children_akas as $aka=>$val ) {
										$pos	=	strpos( $values[$key]['str'], $aka.':' );
										
										if ( $pos !== false && $pos == 0 ) {
											$hasAka	=	true;

											$values[$key]['aka']	=	$aka;
											$values[$key]['str']	=	substr( $values[$key]['str'], strlen( $aka ) + 1 );
										}
									}
								}
							}
						}
						if ( !$hasAka ) {
							$values	=	array( 0=>array( 'aka'=>false, 'sql'=>'', 'str'=>$value ) );
						}
						foreach ( $values as $v ) {
							$k		=	0;

							foreach ( $field->children as $child ) {
								if ( $hasAka ) {
									if ( $v['aka'] && $v['aka'] != $child->aka ) {
										continue;
									} else {
										$value	=	$v['str'];
									}
								}
								if ( $k > 0 ) {
									$v['sql']	.=	' OR ';
								}

								$child->match_mode		=	$field->match_mode;
								$child->match_options	=	$field->match_options;
								$child->match_value		=	$field->match_value;
								if ( $child->storage && $child->storage != 'none' ) {
									$Pf		=	$child->storage_field;
									$Pt		=	$child->storage_table;
									// -
									if ( $Pt && !isset( $tables[$Pt] ) ) {
										$tables[$Pt]	=	array( '_'=>'t'.$t, 'fields'=>array(), 'join'=>1 );
										$t++;
									}
									$tables[$Pt]['location']	=	$child->storage_location;
									// -
									$name	=	$child->storage_field2 ? $child->storage_field2 : $child->name;
									if ( $Pt != '' ) {
										$target	=	$tables[$Pt]['_'].'.'.$Pf;
										$tables[$Pt]['fields'][$Pf]	=	( $Pt == '#__cck_core' ) ? $value : $name;
									}
									if ( JCck::callFunc( 'plgCCK_Field'.$child->type, 'isFriendly' ) ) {
										if ( isset( $fields[$child->name] ) ) {
											$value2	=	JCck::callFunc_Array( 'plgCCK_Field'.$child->type, 'getValueFromOptions', array( $fields[$child->name], $value, $config ) );
										} else {
											$value2	=	JCck::callFunc_Array( 'plgCCK_Field'.$child->type, 'getValueFromOptions', array( $child, $value, $config ) );
										}
									} else {
										$value2		=	$value;
									}
									require_once JPATH_PLUGINS.'/cck_storage/'.$child->storage.'/'.$child->storage.'.php';
									$v['sql']	.=	JCck::callFunc_Array( 'plgCCK_Storage'.$child->storage, 'onCCK_StoragePrepareSearch', array( &$child, $child->match_mode, $value2, $name, $name2, $target, $fields, &$config ) );
									$k++;
								}
							}
							if ( $v['sql'] != '' ) {
								$sqls[]	=	'('.$v['sql'].')';
							}
						}
						if ( $count2 = count( $sqls ) ) {
							$sql	=	implode( ' AND ', $sqls );

							if ( $count2 > 1 ) {
								$sql	=	'('.$sql.')';
							}
						}
					}
				} elseif ( $field->type == 'search_ordering' ) {
					$sql	=	'';
					array_pop( $where2 );
					if ( isset( $field->children[$value] ) ) {
						if ( isset( $field->children[$value]->prepend ) ) {
							if ( is_object( $field->children[$value]->prepend ) ) {
								if ( !isset( $fields_order[$field->children[$value]->prepend->name] )  ) {
									$fields_order[$field->children[$value]->prepend->name]	=	$field->children[$value]->prepend;
								}
							}
						}
						if ( !isset( $fields_order[$field->children[$value]->name] )  ) {
							$fields_order[$field->children[$value]->name]	=	$field->children[$value];
						}
					}
				} elseif ( $field->type == 'search_operator' ) {
					// Glue
					if ( $field->value == '((' ) {
						$glues	=	0;
						$sql	=	'(';
					} elseif ( $field->value == '))' ) {
						$glues	=	1;
						$sql	=	')';
					} else {
						if ( $where2[$w] != '(' ) {
							$glue	=	$field->value;
							$sql	=	$glue;
						}
					}
					$doClean	=	true;
				} else {
					// -
					if ( $Pt && !isset( $tables[$Pt] ) ) {
						$tables[$Pt]	=	array( '_'=>'t'.$t, 'fields'=>array(), 'join'=>1 );
						$t++;
					}
					$tables[$Pt]['location']	=	$field->storage_location;
					// -
					
					$name	=	$field->storage_field2 ? $field->storage_field2 : $field->name;
					if ( $Pt != '' ) {
						$target	=	$tables[$Pt]['_'].'.'.$Pf;
						$tables[$Pt]['fields'][$Pf]	=	( $Pt == '#__cck_core' ) ? $value : $name;
					} else {
						$target	=	$Pf;
						$hasSQL	=	false;
					}
					
					require_once JPATH_PLUGINS.'/cck_storage/'.$field->storage.'/'.$field->storage.'.php';
					$sql	=	JCck::callFunc_Array( 'plgCCK_Storage'.$field->storage, 'onCCK_StoragePrepareSearch', array( &$field, $field->match_mode, $value, $name, $name2, $target, $fields, &$config ) );
				}
				if ( $hasSQL === false ) {
					if ( $glues == 1 ) {
						if ( $where != '' ) {
							$where		=	substr( $where, 0, -3 );
							$where2[$w]	=	'';
						}
					}
					$config['query_parts']['having'][]	=	$sql;
					$sql								=	'';
				} else {
					$where			.=	$sql;
					$where2[++$w]	=	$sql;	
				}
			} else {
				if (  @$glue ) {
					$where2[$w]	=	'';
				}
				// unset( $fields[$field->name] );
			}
		}
		
		// Finalize
		$where		=	implode( ' ', $where2 );
		if ( $doClean !== false ) {
			$where	=	preg_replace( '/\s+/', ' ', $where );
			$where	=	str_replace( 'AND (  )', '', $where );
			$where	=	str_replace( 'AND ( )', '', $where );
			$where	=	str_replace( 'OR OR', 'OR', $where );
			$where	=	str_replace( '( OR', '(', $where );
			$where	=	str_replace( 'OR ) )', ')', $where );
			$where	=	str_replace( 'OR )', ')', $where );
		}
		$where		=	str_replace( 'AND ()', '', $where );
		$pos		=	strpos( $where, '() AND' );
		
		if ( $pos !== false && $pos == 0 ) {
			$where	=	substr( $where, 6 );
		} else {
			$pos	=	strpos( $where, 'AND' );

			if ( $pos !== false && $pos == 0 ) {
				$where	=	substr( $where, 3 );
			}
		}
		
		// -------- -------- Order
		if ( ! $order ) {
			$order	=	' t1.title ASC';
		}
		
		$inherit	=	array( 'bridge'=>'', 'query'=>'' );
		$query		=	NULL;
		$query2		=	'';
		$results	=	array();
		self::_setStorage( $tables, $config, $inherit );
		JPluginHelper::importPlugin( 'cck_storage_location' );
		if ( isset( $config['location'] ) && $config['location'] ) {
			$dispatcher->trigger( 'onCCK_Storage_LocationSearch', array( $config['location'], $tables, $fields, $fields_order, &$config, &$inherit, &$results ) );
			$query	=	$inherit['query'];
		}
		if ( $config['doQuery'] !== false ) {
			if ( $current['stage'] == 0 ) {
				if ( isset( $config['query_variables'] ) && count( $config['query_variables'] ) ) {
					foreach ( $config['query_variables'] as $var ) {
						if ( $var != '' ) {
							$db->setQuery( $var );
							$db->execute();
						}
					}
				}
				$query	=	$db->getQuery( true );
				$query->select( 't0.id AS pid,t0.pk AS pk,t0.pkb AS pkb,t0.parent_id AS parent,t0.author_id AS author' );
				$query->from( '`#__cck_core` AS t0' );
				self::_buildQuery( $dispatcher, $query, $tables, $t, $config, $inherit, $user, $config['doSelect'] );
				$query->select( 't0.cck AS cck,t0.storage_location AS loc' );
				if ( $config['location'] == 'cck_type' ) {
					$query->select( $tables['#__cck_core_types']['_'].'.id AS type_id,'.$tables['#__cck_core_types']['_'].'.alias AS type_alias' );
				} else {
					$query->select( 'tt.id AS type_id,tt.alias AS type_alias' );
					$query->join( 'LEFT', '`#__cck_core_types` AS tt ON tt.name = t0.cck' );
				}
				if ( isset( $config['query_parts']['select'] ) ) {
					if ( ( is_string( $config['query_parts']['select'] ) && $config['query_parts']['select'] != '' )
						|| count( $config['query_parts']['select'] ) ) {
						$query->select( self::_prepareParts( $config['query_parts']['select'], $tables ) );
					}
				}
				if ( $where != '' ) {
					$query->where( $where );
				}
				if ( isset( $config['query_parts']['where'] ) ) {
					if ( ( is_string( $config['query_parts']['where'] ) && $config['query_parts']['where'] != '' )
						|| count( $config['query_parts']['where'] ) ) {
						$query->where( self::_prepareParts( $config['query_parts']['where'], $tables ) );
					}
				}
				if ( isset( $config['query_parts']['having'] ) ) {
					if ( ( is_string( $config['query_parts']['having'] ) && $config['query_parts']['having'] != '' )
						|| count( $config['query_parts']['having'] ) ) {
						$query->having( self::_prepareParts( $config['query_parts']['having'], $tables ) );	
					}
				}
				if ( isset( $config['query_parts']['group'] ) && count( $config['query_parts']['group'] ) ) {
					$hasGroup	=	true;
					$query->group( self::_prepareParts( $config['query_parts']['group'], $tables ) );
				}
				self::_buildQueryOrdering( $order, $ordering, $fields_order, $dispatcher, $query, $tables, $t, $config, $current, $inherit, $user );
				
				if ( $doLimit && $config['limitstart'] >= 0 ) {
					$db->setQuery( $query, $config['limitstart'], $config['limitend'] );
				} else {
					$db->setQuery( $query, 0, $limit );
				}
				$results	=	$db->loadObjectList();

				if ( $doLimit ) {
					$count	=	count( $results );
					
					if ( ( $count < $config['limitend'] && !$config['limitstart'] ) || $isLoadingMore ) {
						$config['total']		=	$count;
					} else {
						if ( $doCount == 1 && strpos( JUri::getInstance()->toString(), 'task=' ) === false ) {
							$query2				=	'SELECT COUNT(id) FROM #__cck_core WHERE cck = "'.$tables['#__cck_core']['fields']['cck'].'"';
							$config['total']	=	JCckDatabaseCache::loadResult( $query2 );

							if ( isset( $config['doQuery2'] ) && $config['doQuery2'] ) {
								$query->clear( 'limit' );
								$query->clear( 'select' )->select( 't0.id AS pid,t0.pk AS pk' );
								$db->setQuery( $query );
								$results2		=	$db->loadObjectList();
								$query3			=	(string)$query;
							}
						} else {
							$query1				=	(string)$query;

							if ( strpos( $query1, 'HAVING' ) !== false ) {
								if ( isset( $config['doQuery2'] ) && $config['doQuery2'] ) {
									// todo
								}
								$query->clear( 'order' )->clear( 'limit' );

								$query2				=	$db->getQuery( true );
								$query2->select( 'COUNT(*)' );
								$query2->from( '('.(string)$query.') AS Count' );
								$db->setQuery( $query2 );
								$config['total']	=	$db->loadResult();
								$query2				=	(string)$query2;
							} else {
								if ( isset( $config['doQuery2'] ) && $config['doQuery2'] ) {
									$query->clear( 'limit' );
									$query->clear( 'select' )->select( 't0.id AS pid,t0.pk AS pk' );
									$db->setQuery( $query );
									$results2		=	$db->loadObjectList();
									$query3			=	(string)$query;
								}
								$query->clear( 'order' )->clear( 'limit' )->clear( 'select' );
								
								if ( $hasGroup ) {
									$query->clear( 'group' );
									$query->select( 'COUNT(DISTINCT t0.id)' );
								} else {
									$query->select( 'COUNT(t0.id)' );
								}
								$db->setQuery( $query );
								$config['total']	=	$db->loadResult();
								$query2				=	(string)$query;
							}
						}
					}
				}
				if ( isset( $config['doQuery2'] ) && $config['doQuery2'] && isset( $results2 ) && count( $results2 ) ) {
					$config['ids2']		=	'';
					$config['pks2']		=	'';

					foreach ( $results2 as $k=>$v ) {
						$config['ids2']	.=	(int)$results2[$k]->pid.',';
						$config['pks2']	.=	(int)$results2[$k]->pk.',';
					}
					$config['ids2']		=	substr( $config['ids2'], 0, -1 );
					$config['pks2']		=	substr( $config['pks2'], 0, -1 );
				}
			} else {
				$query	=	$db->getQuery( true );
				$query->select( 't0.pk as pk' );
				$query->from( '`#__cck_core` AS t0' );
				self::_buildQuery( $dispatcher, $query, $tables, $t, $config, $inherit, $user, false );
				if ( $where != '' ) {
					$query->where( $where );
				}
				$db->setQuery( $query );
				$results	=	$db->loadColumn();
			}
		}
		
		// Debug
		if ( $options->get( 'debug' ) ) {
			if ( !isset( $query1 ) ) {
				$query1	=	(string)$query;
			}
			if ( $query2 ) {
				$query2	.=	'<br />';
			}
			if ( isset( $query3 ) ) {
				$query2	.=	$query3.'<br />';
			}
			echo str_replace( array( 'SELECT', 'FROM', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'WHERE', 'AND', 'HAVING', 'ORDER BY', 'GROUP BY', 'LIMIT', 'UNION', ') AS Count' ),
							  array( '<br />SELECT', '<br />FROM', '<br />LEFT JOIN', '<br />RIGHT JOIN', '<br />INNER JOIN', '<br />WHERE', '<br />&nbsp;&nbsp;AND', '<br />HAVING', '<br />ORDER BY', '<br />GROUP BY', '<br />LIMIT', '<br />UNION', '<br />) AS Count' ),
							  $query1.'<br />'.$query2 ).'<br />';
		}
		
		unset( $fields );
		unset( $fields_order );
		unset( $tables );

		if ( isset( $config['total'] ) ) {
			$config['doPagination']	=	false;
		}
		
		return $results;
	}
	
	// _buildQuery
	protected static function _buildQuery( &$dispatcher, &$query, &$tables, &$t, &$config, &$inherit, $user, $doSelect )
	{
		if ( isset( $config['location'] ) && $config['location'] ) {
			$dispatcher->trigger( 'onCCK_Storage_LocationPrepareSearch', array( $config['location'], &$query, &$tables, &$t, &$config, &$inherit, $user ) );
		}
		
		self::_buildQuery2( $dispatcher, $query, $tables, $t, $config, $inherit, $user, $doSelect, 1 );
		self::_buildQuery2( $dispatcher, $query, $tables, $t, $config, $inherit, $user, false, 2 );
		if ( !( isset( $tables['#__cck_core']['fields']['cck'] ) || isset( $tables['#__cck_core']['fields']['storage_location'] ) ) && $config['location'] ) {
			$query->where( 't0.storage_location = "'.$config['location'].'"' );
		}
	}
	
	// _buildQuery2
	protected static function _buildQuery2( &$dispatcher, &$query, &$tables, &$t, &$config, &$inherit, $user, $doSelect, $join )
	{
		foreach ( $tables as $tk=>$tv ) {
			$j	=	( isset( $tv['join'] ) ) ? $tv['join'] : 1;
			if ( isset( $tv['_'] ) && $tv['_'] != 't0' && $j == $join ) {
				if ( ! $config['location'] && $tv['_'] == 't1' ) {
					$config['location']	=	$tv['location'];
					$inherit['table']	=	$tk;
					$dispatcher->trigger( 'onCCK_Storage_LocationPrepareSearch', array( $config['location'], &$query, &$tables, &$t, &$config, &$inherit, $user ) );
				}
				if ( $doSelect === true ) {
					$query->select( ' '.$tv['_'].'.*' );
				}
				$key		=	( isset( $tables[$tk]['key'] ) ) ? $tables[$tk]['key'] : 'id';
				$join_type	=	( isset( $tables[$tk]['join_type'] ) ) ? $tables[$tk]['join_type'] : 'LEFT';
				$join_table	=	( isset( $tables[$tk]['join_table'] ) && $tables[$tables[$tk]['join_table']]['_'] ) ? $tables[$tables[$tk]['join_table']]['_'] : 't0';
				$join_key	=	( isset( $tables[$tk]['join_key'] ) ) ? $tables[$tk]['join_key'] : ( ( $tk == $inherit['bridge'] ) ? 'pkb' : 'pk' );
				$join_and	=	( isset( $tables[$tk]['join_and'] ) ) ? $tables[$tk]['join_and'] : '';
				$join_mode	=	( isset( $tables[$tk]['join_mode'] ) ) ? $tables[$tk]['join_mode'] : 0;

				if ( $join_table != '' && $join_key != '' ) {
					if ( $join_and != '' && strpos( $tk, '@' ) !== false ) {
						$tk_table	=	explode( '@', $tk );
						$tk			=	$tk_table[0];
					} elseif ( isset( $tv['table'] ) && $tv['table'] ) {
						$tk			=	$tv['table'];
					}
					if ( $tk != '' ) {
						if ( $join_mode ) {
							$tk	=	( isset( $tables[$tk]['join_query'] ) ) ? $tables[$tk]['join_query'] : '';
						} else {
							$tk	=	'`'.$tk.'`';
						}
						if ( $join_and != '' ) {
							$query->join( $join_type, $tk.' AS '.$tv['_'].' ON ('.$tv['_'].'.'.$key.' = '.$join_table.'.'.$join_key.' AND '.$tv['_'].'.'.$join_and.')' );
						} else {
							$query->join( $join_type, $tk.' AS '.$tv['_'].' ON '.$tv['_'].'.'.$key.' = '.$join_table.'.'.$join_key );
						}	
					}
				}
			}
		}
	}
	
	// _buildQueryOrdering
	protected static function _buildQueryOrdering( &$order, &$ordering, $fields_order, &$dispatcher, &$query, &$tables, &$t, &$config, $current, &$inherit, $user )
	{
		if ( $ordering != '' ) {
			if ( $ordering == '-1' ) {
				// Todo: alias. + join table if doesn't exist..
				if ( $current['order_by'] ) {
					$query->order( $current['order_by'] );
				}
			} elseif ( $ordering != 'none' ) {
				if ( @$config['location'] ) {
					$dispatcher->trigger( 'onCCK_Storage_LocationPrepareOrder', array( $config['location'], &$ordering, &$tables, &$config ) );
					if ( $ordering ) {
						$query->order( $ordering );
					}
				}
			}
		} else {
			$ordered	=	false;
			if ( count( $fields_order ) ) {
				$str		=	(string)$query;
				$str		=	explode( 'FROM', $str );
				$str		=	$str[0];
				foreach ( $fields_order as $field ) {
					$order		=	'';
					$modifier	=	'';
					$modifier2	=	'';
					$modifier3	=	$field->match_mode; // direction
					
					if ( $modifier3 ) {
						$s_field	=	$field->storage_field;
						$s_table	=	$field->storage_table;
						
						// Prepare
						if ( empty( $field->match_options ) ) {
							$field->match_options	=	'{}';
						}
						$field->match_options	=	new JRegistry( $field->match_options );	

						if ( $field->match_options->get( 'var_type' ) == '1' ) {
							$modifier2		=	'+0';
						} elseif ( $field->match_options->get( 'var_type' ) == '0' ) {
							$modifier		=	' LENGTH(';
							$modifier2		=	')';
						}
						if ( $modifier3 == 'FIELD' ) {
							$modifier		=	' FIELD(';
							$modifier2		=	',';
							$s_opts			=	array();
							$s_options		=	explode( '||', ( ( $field->match_options->get( 'by_field' ) == '1' ) ? $field->match_options->get( 'by_field_values' ) : $field->options ) );
							foreach ( $s_options as $s_o ) {
								$s_opt		=	explode( '=', $s_o );
								$s_opts[]	=	( isset( $s_opt[1] ) && $s_opt[1] ) ? $s_opt[1] : $s_opt[0];
							}
							$modifier3		=	'"'.implode( '","', $s_opts ).'"'.')';
						} else {
							$modifier3		=	' '.$modifier3;
						}
						if ( ! isset( $tables[$s_table] ) && $s_table ) {
							$tables[$s_table]['_']		=	't'.$t;
							$tables[$s_table]['fields']	=	array();
							$key						=	'id';
							$join_key					=	'pk';
							$query->join( 'LEFT', '`'.$s_table.'` AS '.$tables[$s_table]['_'].' ON '.$tables[$s_table]['_'].'.'.$key.' = t0.'.$join_key );
							$t++;
						}
						
						// Set
						if ( isset( $tables[$s_table]['_'] ) && $tables[$s_table]['_'] != '' && $tables[$s_table]['_'] != '_' ) {
							$order	.=	$modifier.$tables[$s_table]['_'].'.'.$s_field.$modifier2.$modifier3;
						} elseif ( strpos( $str, $s_field.'.' ) !== false || strpos( $str, 'AS '.$s_field ) !== false ) {
							$order	.=	$modifier.$s_field.$modifier2.$modifier3;
						}
						if ( $order != '' ) {
							$ordered	=	true;
							$query->order( $order );
						}
					}
				}
			}
			if ( !$ordered ) {
				$ordering	=	'alpha';
				if ( @$config['location'] ) {
					$dispatcher->trigger( 'onCCK_Storage_LocationPrepareOrder', array( $config['location'], &$ordering, &$tables, &$config ) );
					if ( $ordering ) {
						$query->order( $ordering );
					}
				}
			}
		}
		if ( isset( $config['query_parts']['order_by'] ) ) {
			if ( ( is_string( $config['query_parts']['order_by'] ) && $config['query_parts']['order_by'] != '' )
				|| count( $config['query_parts']['order_by'] ) ) {
				$query->order( self::_prepareParts( $config['query_parts']['order_by'], $tables ) );
			}
		}
	}

	// _preparePart
	protected static function _preparePart( $part, $tables )
	{
		foreach ( $tables as $k=>$v ) {
			if ( strpos( $part, $k.'.' ) !== false ) {
				$part	=	str_replace( $k.'.', $v['_'].'.', $part );
			}
		}

		return $part;
	}

	// _prepareParts
	protected static function _prepareParts( $parts, $tables )
	{
		if ( is_array( $parts ) ) {
			foreach ( $parts as $k=>$part ) {
				$parts[$k]	=	self::_preparePart( $part, $tables );
			}
		} else {
			$parts	=	self::_preparePart( $parts, $tables );
		}

		return $parts;
	}

	// _setStorage
	protected static function _setStorage( &$tables, &$config, &$inherit )
	{
		if ( isset( $tables['#__cck_core']['fields']['storage_location'] ) ) {
			$config['location']	=	$tables['#__cck_core']['fields']['storage_location'];
			$inherit['table']	=	'';
		} elseif ( isset( $tables['#__cck_core']['fields']['cck'] ) ) {
			$cck	=	str_replace( array( ',', ' ' ), array( '","', '","' ), $tables['#__cck_core']['fields']['cck'] );
			$core	=	JCckDatabaseCache::loadObject( 'SELECT storage_location, storage_table FROM #__cck_core WHERE cck IN ("'.$cck.'") ORDER BY id DESC LIMIT 1' );
			if ( is_object( $core ) ) {
				$config['location']	=	$core->storage_location;
				$inherit['table']	=	$core->storage_table;
			}
		}

		if ( !$config['location'] && isset( $config['type_object'] ) && $config['type_object'] ) {
			$config['location']	=	$config['type_object'];
			$inherit['table']	=	'';
		}
	}
}
?>