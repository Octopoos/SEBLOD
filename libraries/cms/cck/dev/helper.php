<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDevHelper
abstract class JCckDevHelper
{
	// alterTableAddColumn
	public static function alterTableAddColumn( $table, $column, $column_prev = '', $type = 'VARCHAR(50)' )
	{
		$db			=	JFactory::getDbo();
		$columns	=	$db->getTableColumns( $table );

		if ( $column_prev != '' && $column != $column_prev ) {
			if ( !isset( $columns[$column] ) ) {
				JCckDatabase::execute( 'ALTER TABLE '.JCckDatabase::quoteName( $table ).' CHANGE '.JCckDatabase::quoteName( $column_prev ).' '.JCckDatabase::quoteName( $column ).' '.$type.' NOT NULL' );
			}
		} elseif ( !isset( $columns[$column] ) ) {
			JCckDatabase::execute( 'ALTER TABLE '.JCckDatabase::quoteName( $table ).' ADD '.JCckDatabase::quoteName( $column ).' '.$type.' NOT NULL' );
		}
	}

	// createFolder
	public static function createFolder( $path, $mode = 0755 )
	{
		jimport( 'joomla.filesystem.folder' );
		
		if ( ! JFolder::exists( $path ) ) {
			JFolder::create( $path, $mode );
			$buffer	=	'<!DOCTYPE html><title></title>';
			JFile::write( $path.'/index.html', $buffer );
		}
		
		return $path;
	}

	// explode
	public static function explode( $delimiters, $string, $limit = null, $replace = array( 'search'=>array( ' ', "\r" ), 'replace'=>'' ) )
	{
		if ( is_array( $replace ) && isset( $replace['search'] ) && isset( $replace['replace'] ) ) {
			$string		=	str_replace( $replace['search'], $replace['replace'], $string );
		}

		$string		=	str_replace( $delimiters, '||', $string );
		$list		=	explode( '||', $string );
		
		return $list;
	}

	// formatBytes
	public static function formatBytes( $bytes, $precision = 2 )
	{ 
		$units	=	array( 'B', 'KB', 'MB', 'GB', 'TB' ); 
	   
		$bytes	=	max( $bytes, 0 );
		$pow	=	floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow	=	min( $pow, count( $units ) - 1 );
		$bytes	/=	pow( 1024, $pow );
		
		return round( $bytes, $precision ).' '.$units[$pow];
	}
	
	// getAbsoluteUrl
	public static function getAbsoluteUrl( $itemId = '', $query = '', $method = 'base' )
	{
		if ( $query != '' ) {
			if ( $query[0] == '?' || $query[0] == '&' ) {
				$query	=	substr( $query, 1 );
			}
		}
		$glue	=	( $query != '' ) ? '/?' : '';
		
		if ( $itemId == '' || $itemId == 'auto' ) {
			$itemId	=	(int)JCck::getConfig_Param( 'sef_root', 0 );

			if ( $itemId > 0 && JFactory::getApplication()->isClient( 'site' ) ) {
				return JRoute::_( 'index.php?Itemid='.$itemId, true, ( JUri::getInstance()->isSSL() ? 1 : 2 ) ).$glue.$query;
			} elseif ( $itemId == -1 ) {
				return JUri::root().'component/cck'.$glue.$query;
			} else {
				$context	=	'';
				$glue		=	( $query != '' ) ? '&' : '';

				if ( JCck::isSite() && JCck::getSite()->context ) {
					$context	=	JCck::getSite()->context.'/';
				}

				return JUri::$method().$context.'index.php?option=com_cck'.$glue.$query;
			}
		} else {
			return JRoute::_( 'index.php?Itemid='.$itemId, true, ( JUri::getInstance()->isSSL() ? 1 : 2 ) ).$glue.$query;
		}
	}
	
	// getBranch
	public static function getBranch( $table, $pk )
	{
		$query 	= 'SELECT s.id, (COUNT(parent.id) - (branch.depth2 + 1)) AS depth2'
				. ' FROM '.$table.' AS s,'
				. $table.' AS parent,'
				. $table.' AS subparent,'
				. ' ('
					. ' SELECT s.id, (COUNT(parent.id) - 1) AS depth2'
					. ' FROM '.$table.' AS s,'
					. $table.' AS parent'
					. ' WHERE s.lft BETWEEN parent.lft AND parent.rgt'
					. ' AND s.id ='.(int)$pk
					. ' GROUP BY s.id'
					. ' ORDER BY s.lft'
					. ' ) AS branch'
				. ' WHERE s.lft BETWEEN parent.lft AND parent.rgt'
				. ' AND s.lft BETWEEN subparent.lft AND subparent.rgt'
				. ' AND subparent.id = branch.id'
				. ' GROUP BY s.id'
				. ' ORDER BY s.lft';
		$items	=	JCckDatabase::loadColumn( $query );
		
		return( $items );
	}
	
	// getPermalink()
	public static function getPermalink( $types = 'canonical', $object = 'joomla_article' )
	{
		$lang_code		=	JFactory::getLanguage()->getTag();
		$permalink		=	'';
		$root			=	substr( JUri::root(), 0, -1 );

		if ( !is_array( $types ) ) {
			$types		=	array( 0=>$types );
		}
		if ( count( $types ) ) {
			foreach ( $types as $type ) {
				if ( $permalink != '' ) {
					break;
				}
				if ( $type == 'canonical' ) {
					if ( isset( JFactory::getApplication()->cck_canonical_url ) ) {
						$permalink	=	$root.JFactory::getApplication()->cck_canonical_url;
					}
				} elseif ( $type == 'current' ) {
					$properties	=	array( 'routes' );
					require_once JPATH_SITE.'/plugins/cck_storage_location/'.$object.'/'.$object.'.php';
					$properties	=	JCck::callFunc( 'plgCCK_Storage_Location'.$object, 'getStaticProperties', $properties );
					if ( isset( $properties['routes'][$lang_code] ) && $properties['routes'][$lang_code] != '' ) {
						$permalink	=	$root.JRoute::_( $properties['routes'][$lang_code] );
					}
				}
			}
		}

		return $permalink;
	}

	// getRelativePath
	public static function getRelativePath( $path, $prepend = true )
	{
		$path	=	str_ireplace( JPATH_ROOT, '', $path );
		$path	=	str_replace( '\\', '/', $path );
		
		if ( !$path ) {
			return $path;
		}

		if ( $path[0] == '/' && $prepend === false ) {
			$path	=	substr( $path, 1 );
		} elseif ( $path[0] != '/' && $prepend === true ) {
			$path	=	'/'.$path;
		}
		
		return $path;
	}

	// getRules
	public static function getRules( $rules, $default = '{}' )
	{
		$json	=	'';
		
		if ( count( $rules ) ) {
			foreach ( $rules as $name => $r ) {
				$j	=	'';
				foreach ( $r as $k => $v ) {
					if ( $v != '' ) {
						$j	.=	'"'.$k.'":'.$v.',';
					}
				}
				$json	.=	'"'.$name.'":'.( $j ? '{'.substr( $j, 0, -1 ).'}' : '[]' ).',';
			}
			$json	=	substr( $json, 0, -1 );
		}
		
		return ( $json != '' ) ? '{'.$json.'}' : $default;
	}
	
	// getRouteParams
	public static function getRouteParams( $name, $sef = '' )
	{
		static $params	=	array();
		
		if ( $name == '' ) {
			return array();
		}
		if ( !isset( $params[$name] )  ) {
			$object				=	JCckDatabase::loadObject( 'SELECT a.storage_location, a.options FROM #__cck_core_searchs AS a WHERE a.name = "'.$name.'"' );
			$object->options	=	json_decode( $object->options );

			$params[$name]				=	array();

			if ( $sef != '' ) {
				$params[$name]['doSEF']	=	$sef;
			} else {
				$params[$name]['doSEF']	=	( isset( $object->options->sef ) && $object->options->sef != '' ) ? $object->options->sef : JCck::getConfig_Param( 'sef', '2' );
			}

			$params[$name]['join_key']	=	'pk';
			$params[$name]['location']	=	( $object->storage_location ) ? $object->storage_location : 'joomla_article';
		}
		
		return $params[$name];
	}

	// getUrlVars
	public static function getUrlVars( $url, $force = false )
	{
		if ( ( $pos = strpos( $url, '?') ) !== false ) {
			$url	=	substr( $url, $pos + 1 );
		}
		$vars	=	explode( '&', $url );
		$url	=	array();
		if ( count( $vars ) ) {
			foreach ( $vars as $var ) {
				$v	=	explode( '=', $var );
				if ( $v[0] ) {
					if ( $force ) {
						$url[$v[0]]	=	(string)@$v[1];
					} else {
						$url[$v[0]]	=	@$v[1];
					}
				}
			}
		}
		$url	=	new JRegistry( $url );
		
		return $url;
	}
	
	// hasLanguageAssociations
	public static function hasLanguageAssociations()
	{
		if ( class_exists( 'JLanguageAssociations' ) ) {
			return JLanguageAssociations::isEnabled();
		} else {
			$app	=	JFactory::getApplication();
			return ( isset( $app->item_associations ) ? $app->item_associations : 0 );
		}
	}
	
	// matchUrlVars
	public static function matchUrlVars( $vars, $url = NULL )
	{
		$app	=	JFactory::getApplication();
		$custom	=	( is_object( $url ) ) ? true : false;
		$vars	=	explode( '&', $vars );
		$count	=	count( $vars );

		if ( $count ) {
			if ( $custom === false ) {
				$url	=	JUri::getInstance();
			}
			$query		=	self::getUrlVars( $url->toString(), true );

			foreach ( $vars as $var ) {
				if ( $var ) {
					if ( strpos( $var, '=' ) !== false ) {
						$v	=	explode( '=', $var );
						$x	=	( $custom !== false ) ? $url->get( $v[0], '' ) : $app->input->get( $v[0], '' );
						if ( $x == $v[1] ) {
							$count--;
						}
					} else {
						if ( $query->exists( $var ) ) {
							$count--;
						}
					}
				}
			}
		}
		if ( $count > 0 ) {
			return false;
		}
		
		return true;
	}

	// replaceLive
	public static function replaceLive( $str, $name = '' )
	{
		$app	=	JFactory::getApplication();
		if ( !$name ) {
			$name	=	uniqid();
		}
		if ( $str != '' ) {
			$str	=	str_replace( '$uri-&gt;get', '$uri->get', $str );
			
			if ( strpos( $str, '$uri->get' ) !== false ) {
				$matches	=	'';
				$search		=	'#\$uri\->get([a-zA-Z]*)\( ?\'?([a-zA-Z0-9_]*)\'? ?\)(;)?#';
				preg_match_all( $search, $str, $matches );
				
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $k=>$v ) {
						$variable	=	$matches[2][$k];
						
						if ( $v == 'Current' ) {
							$request	=	( $variable == 'true' ) ? JUri::getInstance()->toString() : JUri::current();
							$str		=	str_replace( $matches[0][$k], $request, $str );
						} elseif ( $v == 'Array' ) {
							$value				=	'';
							$custom_v			=	'';
							static $custom_vars	=	array();
							
							if ( !isset( $custom_vars[$name] ) ) {
								$custom_vars[$name]	=	explode( '&', $str );
							}
							if ( count( $custom_vars[$name] ) ) {
								foreach ( $custom_vars[$name] as $custom_var ) {
									if ( strpos( $custom_var, $matches[0][$k] ) !== false ) {
										$custom_v	=	substr( $custom_var, 0, strpos( $custom_var, '=' ) );
									}
								}
							}
							if ( $custom_v != '' ) {
								$values		=	$app->input->get( $variable, '', 'array' );
								if ( is_array( $values ) && count( $values ) ) {
									foreach ( $values as $val ) {
										$value	.=	'&'.$custom_v.'[]='.$val;
									}
								}
							}
							$str		=	str_replace( '&'.$custom_v.'='.$matches[0][$k], $value, $str );
						} else {
							$request	=	'get'.$v;
							
							if ( $v == 'Int' ) {
								$str		=	str_replace( $matches[0][$k], (int)$app->input->$request( $variable, '' ), $str );
							} else {
								$str		=	str_replace( $matches[0][$k], $app->input->$request( $variable, '' ), $str );
							}
						}
					}
				}
			}
		}
		if ( $str != '' && strpos( $str, '$user->' ) !== false ) {
			$user			=	JCck::getUser();
			if ( strpos( $str, '$user->getAuthorisedViewLevels()' ) !== false ) {
				$access		=	implode( ',', $user->getAuthorisedViewLevels() );
				$str		=	str_replace( '$user->getAuthorisedViewLevels()', $access, $str );
			}
			$matches		=	'';
			$search			=	'#\$user\->([a-zA-Z0-9_]*)#';
			preg_match_all( $search, $str, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $k=>$v ) {
					$str	=	str_replace( $matches[0][$k], $user->$v, $str );
				}
			}
		}
		if ( $str != '' && strpos( $str, '$site->' ) !== false ) {
			$site	=	JCck::getSite();
			if ( strpos( $str, '$site->getViewLevels()' ) !== false ) {
				$access		=	$site->viewlevels;
				if ( $site->guest_only_viewlevel ) {
					$access	.=	','.$site->guest_only_viewlevel;
				}
				$str		=	str_replace( '$site->getViewLevels()', $access, $str );
			}
			$matches		=	'';
			$search			=	'#\$site\->([a-zA-Z0-9_]*)#';
			preg_match_all( $search, $str, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $k=>$v ) {
					$v2		=	( isset( $site->$v ) ) ? $site->$v : '';
					$str	=	str_replace( $matches[0][$k], $v2, $str );
				}
			}
		}
		if ( $str != '' && strpos( $str, 'J(' ) !== false ) {
			$matches	=	'';
			$search		=	'#J\((.*)\)#U';
			preg_match_all( $search, $str, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $text ) {
					$str	=	str_replace( 'J('.$text.')', JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) ), $str );
				}
			}
		}

		return $str;
	}

	// secureField
	public static function secureField( $field, $value )
	{
		JFactory::getSession()->set( 'cck_hash_live_'.$field->name, JApplication::getHash( $value ) );
	}

	// setDynamicVars
	public static function setDynamicVars( &$vars, $format, $parameters, $fields )
	{
		$app	=	JFactory::getApplication();

		if ( is_array( $format ) ) {
			$format_params	=	$format['options'];
			$format			=	$format['format'];
			$format			=	str_replace(
									array( 'nvp_array', 'nvp_object' ),
									array( 'kvp_array', 'kvp_object' ),
									$format
								);
		}
		$format				=	str_replace( 'nvp', 'kvp', $format );
		if ( $format == 'array' || $format == 'kvp_array' || $format == 'kvp_object' ) {
			if ( !is_array( $vars ) ) {
				$vars	=	array();
			}
		} elseif ( $format == 'object'  ) {
			if ( !is_object( $vars ) ) {
				$vars	=	new stdClass;
			}
		} else {
			if ( !is_string( $vars ) ) {
				$vars	=	'';
			}
		}
		if ( $app->isClient( 'site' ) && JCck::isSite() ) {
			$site			=	JCck::getSite();
			$site_options	=	( is_object( $site ) ) ? new JRegistry( $site->options ) : new JRegistry;
		} else {
			$site_options	=	new JRegistry;
		}

		foreach ( $parameters as $k=>$p ) {
			$v	=	'';
			
			if ( $p['type'] == 'site' ) {
				$v	=	$site_options->get( $p['value'] );
			} elseif ( $p['type'] == 'field' ) {
				if ( isset( $fields[$p['value']] ) ) {
					$v	=	$fields[$p['value']]->value;
				}
			} else {
				$v	=	$p['value'];
			}

			if ( $format == 'array' ) {
				$vars[$k]	=	$v;
			} elseif ( $format == 'object' ) {
				$vars->$k	=	$v;
			} elseif ( $format == 'kvp_array' ) {
				$vars[]		=	array( $format_params['key']=>$k, $format_params['value']=>$v );
			} elseif ( $format == 'kvp_object' ) {
				$vars[]		=	(object)array( $format_params['key']=>$k, $format_params['value']=>$v );
			} elseif ( $format == 'kvp_string' ) {
				$vars		.=	$k.'='.urlencode( $v ).'&';
			} else {
				$vars		=	$v;
			}
		}
		if ( $format == 'kvp_string' && $vars != '' ) {
			$vars	=	substr( $vars, 0, -1 );
		}
	}

	// setLanguage
	public static function setLanguage( $tag )
	{
		$app	=	JFactory::getApplication();
		$lang	=	JLanguage::getInstance( $tag );
		
		$app->loadLanguage( $lang );
		JFactory::$language	=	$app->getLanguage();
		
		JFactory::getConfig()->set( 'language', $tag );
		JFactory::getLanguage()->setLanguage( $tag );
	}

	// sortObjectsByProperty
	public static function sortObjectsByProperty( &$array, $property )
	{
		if ( count( $array ) ) {
			foreach ( $array as $k=>$v ) {
				$v->_index	=	str_pad( $k, 3, '0' , STR_PAD_LEFT );
			}
		}

		$array	=	self::_sortHelper( $array, $property, '_index' );
	}

	// _sortHelper
	protected static function _sortHelper()
	{
		$args	=	func_get_args();
		$array	=	array_splice( $args, 0, 1 ); 
		$array	=	$array[0];
		
		usort( $array, function( $a, $b ) use( $args ) {
			$i		=	0;
			$count	=	count( $args );
			$diff	=	0;
			
			while ( $diff == 0 && $i < $count ) { 
				$diff	=	strcmp( $a->{$args[$i]}, $b->{$args[$i]} );
				$i++;
			}

			return $diff;
		});

		return $array;
	}
}
?>