<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Form
class CCK_Form
{
	// applyTypeOptions
	public static function applyTypeOptions( &$config, $client = '' )
	{
		if ( $client == '' ) {
			$client	=	$config['client'];
		}
		$options	=	JCckDatabase::loadResult( 'SELECT options_'.$client.' FROM #__cck_core_types WHERE name ="'.(string)$config['type'].'"' );
		$options	=	JCckDev::fromJSON( $options );
		if ( isset( $options['message'] ) && $options['message'] != '' ) {
			$config['message']	=	$options['message'];
		}
		if ( isset( $options['data_integrity_excluded'] ) ) {
			$options['data_integrity_excluded']	=	explode( ',', str_replace( ' ', ',', trim( $options['data_integrity_excluded'] ) ) );
		} else {
			$options['data_integrity_excluded']	=	array();
		}
		$config['message_style']	=	( isset( $options['message_style'] ) ) ? $options['message_style'] : 'message';
		$config['options']			=	$options;
	}
	
	// getFields
	public static function getFields( $type, $client, $stage, $excluded, $idx, $cck = false )
	{
		if ( is_array( $type ) ) {
			$parent	=	( isset( $type[1] ) ) ? $type[1] : '';
			$type	=	$type[0];
		} else {
			$parent	=	'';
		}

		// Client
		if ( $client == 'all' )  {
			$where 	=	' WHERE b.name = "'.JCckDatabase::escape( $type ).'"';
		} else {
			if ( $parent != '' ) {
				$where 	=	' WHERE (b.name = "'.JCckDatabase::escape( $type ).'" OR b.name = "'.$parent.'") AND c.client = "'.$client.'"';
			} else {
				$where 	=	' WHERE b.name = "'.JCckDatabase::escape( $type ).'" AND c.client = "'.$client.'"';
			}
		}
		if ( $stage > -1 ) {
			$where 	.=	' AND c.stage = '.(int)$stage;
		}
		
		if ( $excluded != '' ) {
			$where	.=	' AND a.id NOT IN ('.$excluded.')';
		}
		// $where	.=	' AND c.variation != "none"';
		
		// Access
		$user	=	JFactory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );
		$where	.=	' AND c.access IN ('.$access.')';
		
		$query	=	'SELECT DISTINCT a.*, c.client, c.ordering,'
				.	' c.label as label2, c.variation, c.variation_override, c.required, c.required_alert, c.validation, c.validation_options, c.live, c.live_options, c.live_value, c.markup, c.markup_class, c.stage, c.access, c.restriction, c.restriction_options, c.computation, c.computation_options, c.conditional, c.conditional_options, c.position'
				.	' FROM #__cck_core_fields AS a '
				. 	' LEFT JOIN #__cck_core_type_field AS c ON c.fieldid = a.id'
				. 	' LEFT JOIN #__cck_core_types AS b ON b.id = c.typeid'
				. 	$where
				.	' ORDER BY'
				;
		if ( $parent != '' ) {
			$query	.=	' c.typeid ASC,';
		}
		$query		.=	' c.ordering ASC';
		
		$fields		=	( $idx ) ? JCckDatabase::loadObjectList( $query, 'name' ) : JCckDatabase::loadObjectList( $query ); //#
		
		if ( ! count( $fields ) ) {
			$fields	=	array();
		}
		
		return $fields;
	}
		
	// getTemplate
	public static function getTemplateStyle( $id, $params = array() )
	{
		if ( ! $id ) {
			return;
		}
		$query			=	'SELECT a.id, a.template as name, a.params FROM #__template_styles AS a'
						.	' LEFT JOIN #__cck_core_templates AS b ON b.name = a.template'
						.	' WHERE a.id = '.(int)$id.' AND b.published = 1'
						;
		$style			=	JCckDatabase::loadObject( $query );
		$style->params	=	json_decode( $style->params, true );

		if ( count( $params ) ) {
			foreach ( $params as $k=>$v ) {
				if ( !isset( $style->params[$k] ) ) {
					$style->params[$k]	=	$v;
				}
			}
		}

		return $style;
	}
	
	// getType
	public static function getType( $name, $location = '' )
	{		
		if ( $location != '' && $location == 'store' ) {
			$select	=	'a.id, a.name, a.admin_form, a.storage_location';
		} else {
			$select	=	'a.id, a.title, a.name, a.description, a.admin_form, a.location, a.parent, a.storage_location, b.app as folder_app,'
					.	' a.options_admin, a.options_site, a.options_content, a.options_intro, a.template_admin, a.template_site, a.template_content, a.template_intro, a.stylesheets';
		}
		$query	=	'SELECT '.$select
				.	' FROM #__cck_core_types AS a'
				.	' LEFT JOIN #__cck_core_folders AS b ON b.id = a.folder'
				.	' WHERE a.name ="'.JCckDatabase::escape( (string)$name ).'" AND a.published = 1';
		
		return JCckDatabase::loadObject( $query );
	}
	
	// redirect
	public static function redirect( $action, $url, $message, $type, &$config, $debug = 0 )
	{
		$app				=	JFactory::getApplication();
		$config['error']	=	true;		
		
		if ( ! $message ) {
			if ( $debug ) {
				$message	=	JText::sprintf( 'COM_CCK_NO_ACCESS_DEBUG', $config['type'].'@'.$config['formId'] );
			} else {
				$message	=	JText::_( 'COM_CCK_NO_ACCESS' );
			}
		} else {
			if ( JCck::getConfig_Param( 'language_jtext', 0 ) ) {
				$message	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $message ) ) );
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
			$url	=	( $url != 'index.php' ) ? JRoute::_( $url, false ) : $url;
			$app->redirect( $url );
		}
	}
}
?>