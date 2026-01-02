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
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;

// Plugin
class plgCCK_FieldCck_Canonical extends JCckPluginField
{
	protected static $type		=	'cck_canonical';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );

		// Process
		if ( $field->state ) {
			if ( $field->options2 != '' ) {
				$fieldname		=	'';
				$fieldname2		=	'';
				$fieldname_url	=	'';
				$itemIds		=	'';
				$location		=	( $config['location'] ) ? $config['location'] : 'joomla_article';
				$options2		=	new Registry( $field->options2 );
				
				if ( $options2->get( 'content' ) == '2' ) {				
					$fieldname	=	$options2->get( 'content_fieldname', '' );
				}
				if ( $options2->get( 'itemid' ) == '-2' ) {
					$fieldname2	=	$options2->get( 'itemid_fieldname', '' );
					$itemId		=	$config['Itemid'];
				} else {
					$itemId		=	$options2->get( 'itemid', $config['Itemid'] );
				}
				if ( $field->bool2 ) {
					$itemIds	=	$options2->get( 'itemids', '' );
				}

				parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fieldname'=>$fieldname, 'itemId'=>$itemId, 'fieldname2'=>$fieldname2, 'location'=>$location, 'mode'=>(string)$field->bool, 'mode2'=>(string)$field->bool2, 'itemIds'=>$itemIds, 'fieldname_url'=>$options2->get( 'url_fieldname', '' ) ), 5 );
			}
		}

		$field->display	=	0;
		$field->value	=	'';
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		$field->display	=	0;
		$field->form	=	'';
		$field->value	=	'';
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeRenderContent
	public static function onCCK_FieldBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		if ( !$fields[$process['name']]->state ) {
			return;
		}
		
		$app	=	Factory::getApplication();
		$doc	=	Factory::getDocument();
		$uri	=	Uri::getInstance();

		$domain	=	$uri->toString( array( 'scheme', 'host', 'port' ) );
		$pk		=	$config['pk'];
		
		if ( count( $doc->_links ) ) {
			foreach ( $doc->_links as $k=>$link ) {
				if ( $link['relation'] == 'canonical' ) {
					unset( $doc->_links[$k] );
				}
			}
		}

		if ( $process['fieldname_url'] && isset( $fields[$process['fieldname_url']] ) && $fields[$process['fieldname_url']]->value ) {
			$link	=	$fields[$process['fieldname_url']]->value;

			if ( $link[0] != '/' ) {
				$link	=	'/'.$link;
			}
		} else {
			if ( $process['fieldname'] && isset( $fields[$process['fieldname']] ) && $fields[$process['fieldname']]->value ) {
				$pk	=	$fields[$process['fieldname']]->value;
			}

			$config['sef_aliases']	=	(int)JCck::getConfig_Param( 'sef_aliases', '0' );
			$itemId					=	0;
			$sef					=	$process['sef'] ?? '';
			$sef					=	(string)$sef;

			if ( $process['fieldname2'] && isset( $fields[$process['fieldname2']] ) && $fields[$process['fieldname2']]->value ) {
				$itemId	=	$fields[$process['fieldname2']]->value;
			}
			if ( !$itemId ) {
				$itemId	=	$process['itemId'];
			}	
			if ( $sef === '' ) {
				$menu	=	$app->getMenu();

				$active	=	$itemId ? $menu->getItem( $itemId ) : $menu->getActive();

				if ( isset( $active->query['view'] ) && $active->query['view'] == 'list' ) {
					$options	=	JCckDatabase::loadResult( 'SELECT options FROM #__cck_core_searchs WHERE name = "'.$active->query['search'].'"' );
					$options	=	json_decode( $options, true );
					$path 		=	Uri::getInstance()->getPath();
					
					$sef		=	$options['sef'];

					if ( $active->route != '' ) {
						$pos		=	strpos( $path, $active->route );

						if ( $pos !== false ) {
							if ( $path[0] == '/' ) {
								$pos++;
							}

							$path	=	substr( $path, $pos + strlen( $active->route ) );
							$segments	=	explode( '/', $path );

							if ( ( isset( $sef[0] ) && $sef[0] == '4' ) && count( $segments ) === 1 ) {
								$sef[0]	=	'2';
							}
						}
					}
					if ( isset( $options['sef_route_aliases'] ) && (int)$options['sef_route_aliases'] != -1 ) {
						$config['sef_aliases']	=	$options['sef_route_aliases'];
					}
				}
			}
			if ( $sef === '' ) {
				$sef	=	$config['doSEF'];
			}

			$link	=	JCck::callFunc_Array( 'plgCCK_Storage_Location'.$process['location'], 'getRoute', array( $pk, $sef, $itemId, $config ) );
		}

		if ( $process['mode2'] == '2' ) {
			$itemIds	=	explode( '||', $process['itemIds'] );

			if ( in_array( Factory::getApplication()->input->getInt( 'Itemid' ), $itemIds ) && $doc->getBase() != $domain.$link ) {
				$app->redirect( $link, 301 );
				return;
			}
		}
		if ( $process['mode'] == '0' || ( $process['mode'] == '1' && $doc->getBase() != $domain.$link ) ) {
			if ( $link == '/' ) {
				$link	=	'';
			}

			$doc->addHeadLink( $domain.$link, 'canonical' );
			$app->cck_canonical_url	=	$link;
			$app->cck_canonical		=	true;	
		}
	}
}
?>