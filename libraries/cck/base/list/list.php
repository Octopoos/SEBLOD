<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// List
class CCK_List
{
	// generateRange
	public static function generateRange( $min, $max )
	{
		$digit		=	'[0-9]';
		$lenMax		=	strlen( $max );
		$lenMin		=	strlen( $min );
		$lenDiff	=	$lenMax - $lenMin;
		$min 		=	str_pad( $min, $lenMax, 0, STR_PAD_LEFT );
		$max		=	(string)$max;
		
		// find length of common prefix
		for ( $i = 0; $i < $lenMin && $min[$i] == $max[$i]; $i++ );
		$prefixLength	=	$i;
		// add non-conflicting ranges from each end
		for ( $i = $lenMax, $j = 0; $i-- > 1 + $prefixLength; $j++ ) {
			$lower	=	$min[$i];
			$upper	=	$max[$i];
			// correct bounds if not final range
			if ( $j ) {
				++$lower;
				--$upper;
			}
			// lower bound
			if ( $lower < 10 ) {
				$char		=	( $lower == 9 ) ? 9 : '[' . $lower . '-9]';
				$pattern[]	=	( $j >= $lenMin ? '' : substr( $min, $lenDiff, $i - $lenDiff ) ) . $char . str_repeat( $digit, $j );
			}
			// upper bound
			if ( $upper >= 0 ) {
				$char		=	$upper ? '[0-' . $upper . ']' : 0;
				$pattern[]	=	substr($max, 0, $i) . $char . str_repeat( $digit, $j );
			}
		}
		// add middle range
		if ( !$j || $max[$prefixLength] - $min[$prefixLength] > 1 ) {
			$prefix	=	substr( $min, 0, $prefixLength );
			$lower	=	@$min[$prefixLength];
			$upper	=	@$max[$prefixLength];
			// correct bounds if not final range
			if ( $j && $i == $prefixLength ) {
				++$lower;
				--$upper;
			}
			$char		=	( $lower == $upper ) ? $lower : '[' . $lower . '-' . $upper . ']';
			$pattern[]	=	$prefix . $char . @str_repeat( $digit, $lenMax - $prefixLength - 1 );
		}
	 
		return join( '|', $pattern );
	}

	// getCountFromRoute
	public static function getCountFromRoute( $route )
	{
		$uri		=	Uri::getInstance();

		if ( $uri->getScheme() === 'https' ) {
			$headers	=	array(
								'Host'=>$uri->getHost(),
								'X-Forwarded-Proto'=>'https'
							);
			$page_url	=	'127.0.0.1'.$route;
		} else {
			$headers	=	array();
			$page_url	=	$uri->getHost().$route;
		}
		
		$resp	=	HttpFactory::getHttp()->get( $page_url.'?format=total', $headers, 10 );

		return is_object( $resp ) && isset( $resp->body ) ? (int)$resp->body : 0;
	}

	// getFieldColumns_asString
	public static function getFieldColumns_asString( $t )
	{
		$columns	=	array(
							'id',
							'title',
							'name',
							'folder',
							'type',
							'description',
							'published',
							'label',
							'selectlabel',
							'display',
							'required',
							'validation',
							'defaultvalue',
							'options',
							'options2',
							'minlength',
							'maxlength',
							'size',
							'cols',
							'rows',
							'sorting',
							'divider',
							'bool',
							'location',
							'extended',
							'style',
							'script',
							'bool2',
							'bool3',
							'bool4',
							'bool5',
							'bool6',
							'bool7',
							'bool8',
							'css',
							'attributes',
							'storage',
							'storage_cck',
							'storage_crypt',
							'storage_location',
							'storage_table',
							'storage_field',
							'storage_field2',
							'storage_key',
							'storage_mode'
						);

		return $t.'.'.implode( ', '.$t.'.', $columns );
	}

	// getFields
	public static function getFields( $search, $client, $excluded = '', $idx = true, $cck = false )
	{
		$where 	=	' WHERE b.name = "'.$search.'"';

		// Client
		if ( $client != 'all' )  {
			$and		=	array();

			if ( !is_array( $client ) ) {
				$client	=	array( $client );
			}

			if ( count( $client ) ) {
				foreach ( $client as $k=>$v ) {
					$and[]	=	'c.client = "'.$v.'"';
				}
				$where	.=	' AND ('.implode( ' OR ', $and ).')';
			}
		}

		// Exclude
		if ( $excluded != '' ) {
			$where	.=	' AND a.id NOT IN ('.$excluded.')';
		}
		
		// Access
		$user	=	Factory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );
		$where	.=	' AND c.access IN ('.$access.')';
		
		$query	=	' SELECT '.self::getFieldColumns_asString( 'a' ).', c.client, c.ordering,'
				.	' c.label as label2, c.variation, c.variation_override, c.required, c.required_alert, c.validation, c.validation_options, c.live, c.live_options, c.live_value, c.markup, c.markup_class, c.match_collection, c.match_mode, c.match_options, c.match_value, c.stage, c.access, c.restriction, c.restriction_options, c.computation, c.computation_options, c.conditional, c.conditional_options, c.position'
				.	' FROM #__cck_core_fields AS a '
				.	' LEFT JOIN #__cck_core_search_field AS c ON c.fieldid = a.id'
				. 	' LEFT JOIN #__cck_core_searchs AS b ON b.id = c.searchid'
				.	$where
				.	' GROUP BY c.client, c.fieldid'
				.	' ORDER BY c.ordering ASC';
				;
		$fields	=	( $idx ) ? JCckDatabase::loadObjectListArray( $query, 'client', 'name' ) : JCckDatabase::loadObjectList( $query, 'client' ); //#
		
		if ( ! count( $fields ) ) {
			$fields	=	array();

			if ( count( $client ) ) {
				foreach ( $client as $k=>$v ) {
					$fields[$v]	=	array();
				}
			}
		}
		if ( !isset( $fields['order'] ) ) {
			$fields['order']	=	array();
		}
		
		return $fields;
	}
	
	// getFields_Items
	public static function getFields_Items( $search_name, $client, $access )
	{
		$query		=	'SELECT '.self::getFieldColumns_asString( 'cc' ).', c.ordering,'
					.	' c.label as label2, c.variation, c.variation_override, c.link, c.link_options, c.markup, c.markup_class, c.typo, c.typo_label, c.typo_options, c.access, c.restriction, c.restriction_options, c.position'
					.	' FROM #__cck_core_search_field AS c'
					.	' LEFT JOIN #__cck_core_searchs AS sc ON sc.id = c.searchid'
					.	' LEFT JOIN #__cck_core_fields AS cc ON cc.id = c.fieldid'
					.	' WHERE sc.name = "'.$search_name.'" AND sc.published = 1 AND c.client = "'.$client.'" AND c.access IN ('.$access.')'
					.	' ORDER BY c.ordering ASC'
					;
		
		return JCckDatabase::loadObjectList( $query, 'name' ); //#
	}
	
	// getList
	public static function getList( $ordering, $areas, $fields, $fields_order, &$config, $current, $options, $user )
	{
		PluginHelper::importPlugin( 'search', 'cck' );
		$app		=	Factory::getApplication();
		$doCache	=	$options->get( 'cache' );
		$doDebug	=	(int)$options->get( 'debug' );

		// Debug
		if ( $doDebug ) {
			$profiler	=	JProfiler::getInstance();
		}
		if ( $doCache ) {
			$group		=	( $doCache == '2' ) ? 'com_cck_'.$config['type_alias'] : 'com_cck';
			$cache		=	Factory::getCache( $group );
			$cache->setCaching( 1 );
			$isCached	=	' [Cache=ON]';
			$user		=	( $options->get( 'cache_per_user' ) && $user->id > 0 ) ? $user : null;
			$data		=	$cache->get( array( $app, 'triggerEvent' ), array( 'onContentSearch', array( '', '', $ordering, $areas['active'], $fields, $fields_order, $config, $current, $options, $user ) ) );
		} else {
			$isCached	=	' [Cache=OFF]';
			$data		=	$app->triggerEvent( 'onContentSearch', array( '', '', $ordering, $areas['active'], $fields, $fields_order, $config, $current, $options, $user ) );
		}

		if ( isset( $data[0] ) ) {
			$config		=	$data[0]['config'];
			$list		=	$data[0]['results'];
		} else {
			$list		=	array();
		}

		// Debug
		if ( $doDebug > 0 ) {
			$count		=	( isset( $config['total'] ) && $config['total'] ) ? $config['total'] : count( $list );
			echo $profiler->mark( 'afterSearch'.$isCached ).' = '.$count.' '.( $count > 1 ? 'results' : 'result' ).'.<br />';
			if ( isset( $current['stage'] ) && (int)$current['stage'] > 0 ) {
				echo '<br />';
			}
		} elseif ( $doDebug == -1 ) {
			echo Text::_( 'COM_CCK_DEBUG_OUTPUT_NO_SEARCH' );
		}
		
		return $list;
	}
	
	// getPositions
	public static function getPositions( $search_id, $client )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$search_id.'_'.$client] ) ) {
			$cache[$search_id.'_'.$client]	=	JCckDatabase::loadObjectList( 'SELECT a.position, a.client, a.legend, a.variation, a.variation_options, a.width, a.height, a.css'
																			. ' FROM #__cck_core_search_position AS a'
																			. ' WHERE a.searchid = '.(int)$search_id.' AND a.client ="'.(string)$client.'"', 'position' );
		}
		
		return $cache[$search_id.'_'.$client];
	}

	// getPropertyColumns_asString
	public static function getPropertyColumns_asString( $level )
	{
		if ( !$level || $level > 10 ) {
			return array(); /* ALL */
		}

		$columns	=	array(
							'access',
							'computation_options',
							'conditional_options',
							'folder',
							'link_options',
							'linked',
							'live_options',
							'match_collection',
							'match_mode',
							'match_options',
							'match_value',
							'ordering',
							'published',
							'restriction_options',
							'storage',
							'storage_cck',
							'storage_crypt',
							'storage_location',
							'storage_table',
							'storage_field',
							'storage_field2',
							'storage_filter',
							'storage_mode',
							'typo_label',
							'typo_options',
							'validation',
							'validation_options'
						);

		if ( $level < 3 ) {
			return $columns; /* ~45 */
		}

		$columns[]	=	'attributes';
		$columns[]	=	'bool';
		$columns[]	=	'bool2';
		$columns[]	=	'bool3';
		$columns[]	=	'bool4';
		$columns[]	=	'bool5';
		$columns[]	=	'bool6';
		$columns[]	=	'bool7';
		$columns[]	=	'bool8';
		$columns[]	=	'access';
		$columns[]	=	'css';
		$columns[]	=	'cols';
		$columns[]	=	'defaultvalue';
		$columns[]	=	'divider';
		$columns[]	=	'extended';
		$columns[]	=	'location';
		$columns[]	=	'maxlength';
		$columns[]	=	'minlength';
		$columns[]	=	'options';
		$columns[]	=	'options2';
		$columns[]	=	'required';
		$columns[]	=	'rows';
		$columns[]	=	'selectlabel';
		$columns[]	=	'size';
		$columns[]	=	'sorting';
		
		return $columns; /* ~21 */
	}

	// getSearch
	public static function getSearch( $name, $id, $location = '' )
	{
		/* TODO#SEBLOD: API (move) */
		$query	=	'SELECT a.id, a.title, a.name, a.alias, a.description, a.access, a.content, a.location, a.storage_location, a.stylesheets, a.sef_route_aliases, b.app as folder_app,'
				.	' a.options, a.template_search, a.template_filter, a.template_list, a.template_item'
				.	' FROM #__cck_core_searchs AS a'
				.	' LEFT JOIN #__cck_core_folders AS b ON b.id = a.folder'
				.	' WHERE a.name ="'.JCckDatabase::escape( (string)$name ).'" AND a.published = 1';
		
		return JCckDatabase::loadObject( $query );
	}
	
	// getTemplate
	public static function getTemplateStyle( $id, $params = array() )
	{
		if ( ! $id ) {
			return;
		}
		static $cache	=	array();
		
		if ( !isset( $cache[$id] ) ) {
			$query				=	'SELECT a.id, a.template as name, a.params FROM #__template_styles AS a'
								.	' LEFT JOIN #__cck_core_templates AS b ON b.name = a.template'
								.	' WHERE a.id = '.(int)$id.' AND b.published = 1'
								;
			$cache[$id]			=	JCckDatabase::loadObject( $query );
			$cache[$id]->params	=	json_decode( $cache[$id]->params, true );
			
			if ( count( $params ) ) {
				foreach ( $params as $k=>$v ) {
					if ( !isset( $cache[$id]->params[$k] ) ) {
						$cache[$id]->params[$k]	=	$v;
					}
				}
			}
		}
		
		return $cache[$id];
	}

	// prepareSearch
	public static function prepareSearch( &$fields, &$config, $target_name, &$target )
	{
		$app	=	Factory::getApplication();
		
		foreach ( $fields as $field ) {
			$name	=	$field->name;
			$value	=	'';
			
			// Variation
			if ( $field->variation_override ) {
				$override	=	json_decode( $field->variation_override, true );
				if ( count( $override ) ) {
					foreach ( $override as $k=>$v ) {
						$field->$k	=	$v;
					}
				}
				$field->variation_override	=	null;
			}
			$field->variation	=	( isset( $config['variations'][$name] ) ) ? ( $config['variations'][$name] == 'form' ? '' : $config['variations'][$name] ) : $field->variation;

			if ( $field->variation == 'form_filter_ajax' || $field->variation == 'list_filter_ajax' ) {
				$config['doAjax']	=	true;
				$config['infinite']	=	true;
			}

			// Value
			if ( ( !$field->variation || $field->variation == 'form_filter' || $field->variation == 'form_filter_ajax' || $field->variation == 'list' || $field->variation == 'list_filter' || $field->variation == 'list_filter_ajax' || strpos( $field->variation, 'custom_' ) !== false ) && isset( $config['post'][$name] ) ) {
				$value	=	$config['post'][$name];
				
				// Set Persistent Values
				if ( $config['doPersistent'] ) {
					$app->setUserState( $list_context.'.filter.'.$name, $value );
				}
			} else {
				if ( isset( $config['lives'][$name] ) ) {
					$value		=	$config['lives'][$name];
				} else {
					if ( $field->live && $field->variation != 'clear' ) {
						$app->triggerEvent( 'onCCK_Field_LivePrepareForm', array( &$field, &$value, &$config ) );
					} else {
						$value	=	$field->live_value;
					}
				}

				// Get Persistent Values
				if ( $config['doPersistent'] && !( $field->variation == 'clear' || $field->variation == 'disabled' || $field->variation == 'hidden' || $field->variation == 'hidden_anonymous' || $field->variation == 'value' ) ) {
					if ( $config['registry']->exists( $list_context.'.filter.'.$name ) ) {
						$value	=	$app->getUserState( $list_context.'.filter.'.$name, '' );
					}
				}
			}

			// Prepare
			if ( !$config['show_form'] && $field->variation != 'clear' ) {
				$field->variation	=	'hidden';
			}

			if ( $target_name == 'form' ) {
				$inherit	=	array( 'caller'=>$field->extended );
				$results	=	$app->triggerEvent( 'onCCK_FieldPrepareSearch', array( &$field, $value, &$config, $inherit, true ) );
				
				if ( !isset( $results[0] ) ) {
					continue;
				}
				
				$target[$name]				=	$results[0];
				$target[$name]->name		=	$field->name;
				
				$config['fields'][$name]	=	$target[$name];
			} elseif ( $target_name == 'positions' ) {
				$app->triggerEvent( 'onCCK_FieldPrepareSearch', array( &$field, $value, &$config, array() ) );

				if ( $config['show_form'] ) {
					$position				=	$field->position;
					$target[$position][]	=	$field->name;
				}
			}

			// Stage
			if ( (int)$field->stage > 0 ) {
				$config['stages'][$field->stage]	=	0;
			}
		}
	}

	// render
	public static function render( $items, $search, $path, $client, $itemId, $siteId, $options, $config_list )
	{
		if ( isset( $search->lang_tag ) && $search->lang_tag ) {
			if ( self::_language() != $search->lang_tag ) {
				JCckDevHelper::setLanguage( $search->lang_tag );
			}
		}

		$app			=	Factory::getApplication();
		$access			=	implode( ',', Factory::getUser()->getAuthorisedViewLevels() );
		$data			=	array(
								'buffer'=>'',
								'config'=>array()
							);
		$form_wrapper	=	0;
		$list			=	array(
								'doSEF'=>$config_list['doSEF'],
								'formId'=>$config_list['formId'],
								'isCore'=>$config_list['doQuery'],
								'itemId'=>( ( $itemId == '' ) ? $app->input->getInt( 'Itemid', 0 ) : $itemId ),
								'location'=>$config_list['location'],
								'sef_aliases'=>$config_list['sef_aliases']
							);
		
		include JPATH_SITE.'/libraries/cck/base/list/list_inc_list.php';
		
		if ( $validation ) {
			$data['config']['formValidation']	=	$validation;
		}
		if ( $form_wrapper ) {
			$data['config']['formWrapper']	=	$form_wrapper;
		}
		if ( $options->get( 'prepare_content', JCck::getConfig_Param( 'prepare_content', 0 ) ) ) {
			PluginHelper::importPlugin( 'content' );
			$data['buffer']	=	HTMLHelper::_( 'content.prepare', $data['buffer'] );
		}

		if ( isset( $search->lang_tag ) && $search->lang_tag ) {
			if ( self::_language() != $search->lang_tag ) {
				JCckDevHelper::setLanguage( self::_language() );
			}
		}

		return $data;
	}

	// redirect
	public static function redirect( $action, $url, $message, $type, &$config, $debug = 0 )
	{
		$app				=	Factory::getApplication();
		$config['error']	=	true;

		if ( ! $message ) {
			if ( $debug ) {
				$message	=	Text::sprintf( 'COM_CCK_NO_ACCESS_DEBUG', $config['type'].'@'.$config['formId'] );
			} else {
				$message	=	Text::_( 'COM_CCK_NO_ACCESS' );
			}
		} else {
			if ( JCck::getConfig_Param( 'language_jtext', 1 ) ) {
				$message	=	Text::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $message ) ) );
			}
			if ( $debug ) {
				$message	.=	' '.$config['type'].'@'.$config['formId'];
			}
		}
		if ( $type ) {
			if ( $type == -1 ) {
				echo $message;
			} else {
				$app->enqueueMessage( $message, $type );
			}
		}
		
		if ( $action == 'redirection' ) {
			$url	=	( $url != 'index.php' ) ? Route::_( $url, false ) : $url;
			$app->redirect( $url );
		}
	}

	// _language
	protected static function _language()
	{
		static $lang_tag	=	null;

		if ( !$lang_tag ) {
			$lang_tag	=	Factory::getLanguage()->getTag();
		}

		return $lang_tag;
	}
}
?>