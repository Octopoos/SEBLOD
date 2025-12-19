<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

// Plugin
class plgCCK_Field_LinkCck_Route extends JCckPluginLink
{
	protected static $type	=	'cck_route';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_LinkPrepareContent
	public static function onCCK_Field_LinkPrepareContent( &$field, &$config = array() )
	{		
		if ( self::$type != $field->link ) {
			return;
		}
		
		// Prepare
		$link	=	parent::g_getLink( $field->link_options );
		
		// Set
		$field->link	=	'';
		self::_link( $link, $field, $config );
	}
	
	// _link
	protected static function _link( $link, &$field, &$config )
	{
		$app			=	Factory::getApplication();
		$custom			=	$link->get( 'custom', '' );
		$fieldnames		=	array();
		$link_url		=	'';
		$mode			=	(int)$link->get( 'mode', '0' );
		$path_type		=	$link->get( 'path_type', '' );

		if ( $mode == -1 ) {
			$link_url	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$config['location'], 'getRouteById', array( $config['pk'] ) );
		} elseif ( $mode ) {
			$group_name	=	$link->get( 'routes_fieldname', '' );
			
			if ( $group_name ) {
				$client	=	( $config['client'] == 'list' || $config['client'] == 'item' ) ? 'intro' : $config['client'];

				if ( isset( $config['client_form'] ) && $config['client_form'] ) {
					$client	=	$config['client_form'];
				}

				$query	=	' SELECT a.name'
						.	' FROM #__cck_core_fields AS a'
						.	' LEFT JOIN #__cck_core_type_field AS c ON c.fieldid = a.id'
						.	' LEFT JOIN #__cck_core_types AS b ON b.id = c.typeid'
						.	' WHERE b.name = "'.$group_name.'" AND c.client = "'.$client.'"'
						;
				$fieldnames	=	JCckDatabaseCache::loadColumn( $query );
			}
		} else {
			$fieldnames	=	$link->get( 'routes', '' );
			$fieldnames	=	explode( '||', $fieldnames );
		}

		// Prepare
		$link_attr		=	$link->get( 'attributes', '' );
		$link_class		=	$link->get( 'class', '' );
		$link_rel		=	$link->get( 'rel', '' );
		$link_target	=	$link->get( 'target', '' );
		
		if ( $link_target == 'modal' ) {
			if ( strpos( $link_attr, 'data-cck-modal' ) === false ) {
				$modal_json	=	$link->get( 'target_params', '' );

				if ( $modal_json != '' ) {
					$modal_json	=	'=\''.$modal_json.'\'';
				}
				$link_attr	=	trim( $link_attr.' data-cck-modal'.$modal_json );				
			}
		}

		if ( count( $fieldnames ) ) {
			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fieldnames'=>$fieldnames, 'path_type'=>$path_type, 'custom'=>$custom ), 5 );
		}

		$field->link			=	$link_url;

		if ( $custom ) {
			$custom	=	parent::g_getCustomVars( self::$type, $field, $custom, $config );

			if ( $custom ) {
				$field->link	.=	( $custom[0] == '#' ) ? $custom : ( ( strpos( $field->link, '?' ) !== false ) ? '&'.$custom : '?'.$custom );
			}
		}

		$field->link_attributes	=	$link_attr ? $link_attr : ( isset( $field->link_attributes ) ? $field->link_attributes : '' );
		$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		$field->link_rel		=	$link_rel ? $link_rel : ( isset( $field->link_rel ) ? $field->link_rel : '' );
		$field->link_state		=	$link->get( 'state', 1 );
		$field->link_target		=	$link_target ? ( $link_target == 'modal' ? '' : $link_target ) : ( isset( $field->link_target ) ? $field->link_target : '' );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_LinkBeforeRenderContent
	public static function onCCK_Field_LinkBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name			=	$process['name'];
		$path_type		=	(string)$process['path_type'];
		$route			=	'';
		$route_class	=	'';
		$route_target	=	'';		
		
		if ( is_array( $process['fieldnames'] ) && count( $process['fieldnames'] ) ) {
			foreach ( $process['fieldnames'] as $fieldname ) {
				if ( isset( $fields[$fieldname] ) && $fields[$fieldname]->link != '' && $fields[$fieldname]->state ) {
					$route			=	$fields[$fieldname]->link;
					$route_class	=	$fields[$fieldname]->link_class;
					$route_target	=	$fields[$fieldname]->link_target;
					break;
				}
			}
		}
		
		if ( $route != '' ) {
			if ( $path_type == '1' ) {
				$base	=	Uri::getInstance()->toString( array( 'scheme', 'host' ) );
				$route	=	$base.$route;
			} else {
				$base	=	'';
			}

			if ( $route_class ) {
				$fields[$name]->link_class	=	trim( $fields[$name]->link_class.' '.$route_class );
			}
			$fields[$name]->link		=	$route;
			$fields[$name]->link_target	=	$route_target;
			$target						=	 $fields[$name]->typo_target;
			
			if ( is_array( $fields[$name]->$target ) ) {
				foreach ( $fields[$name]->$target as $k=>$v ) {
					if ( is_object( $v ) && $v->state ) {
						$fields[$name]->{$target}[$k]->link_attributes	=	$fields[$name]->link_attributes;
						$fields[$name]->{$target}[$k]->link_class		=	$fields[$name]->link_class;
						$fields[$name]->{$target}[$k]->link				=	$fields[$name]->link;
						$fields[$name]->{$target}[$k]->link_state		=	1;
						$fields[$name]->{$target}[$k]->link_target		=	$fields[$name]->link_target;
					}
				}
			}
			
			if ( (int)$fields[$name]->link_state ) {
				if ( $process['custom'] != '' && strpos( $process['custom'], '$cck->get' ) !== false ) {
					$matches	=	'';
					$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,\[\]]*)\' ?\)(;)?#';

					preg_match_all( $search, $process['custom'], $matches );
					
					if ( is_array( $matches[1] ) && count( $matches[1] ) ) {
						foreach ( $matches[1] as $k=>$match ) {
							$m	=	strtolower( $match );
							$v	=	isset( $fields[$matches[2][$k]] ) ? $fields[$matches[2][$k]]->$m : '';

							$process['custom']	=	str_replace( $matches[0][$k], $v, $process['custom'] );
						}
						if ( $process['custom'] ) {
							$fields[$name]->link	.=	( $process['custom'][0] == '#' ) ? $process['custom'] : ( ( strpos( $fields[$name]->link, '?' ) !== false ) ? '&'.$process['custom'] : '?'.$process['custom'] );	
						}					
					}
				}

				JCckPluginLink::g_setHtml( $fields[$name], $target );
			}
		}
	}
}
?>