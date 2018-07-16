<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// CommonHelper
class CommonHelper_Form
{
	// getClientFilter
	public static function getClientFilter( &$field, $value, $name, $id, $config )
	{
		return JHtml::_( 'select.genericlist', Helper_Admin::getClientOptions( true, false, true ), $name, 'class="inputbox select small span12" onchange="this.form.submit()"', 'value', 'text', $value, $id );
	}

	// getConditionalTrigger
	public static function getConditionalTrigger( &$field, $value, $name, $id, $config )
	{
		return	JCckDev::getForm( 'core_conditional_trigger_type', '', $config, array() )
			.	JCckDev::getForm( 'core_conditional_trigger_value', '', $config, array( 'css'=>'trigger-type' ) )
			.	'&nbsp;<span class="fill trigger-value" name="'.$field->location.'">&laquo;</span>'
			;
		
		$field->script =	'
							$(".trigger-value").on("click", function() {
							var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/selection.php&title=conditional&name='.$field->extended.'&type='.$field->location.'";
							$.colorbox({href:url, iframe:true, innerWidth:300, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $("#cboxClose").remove();}}); });
							';
	}

	// getFolder
	public static function getFolder( &$field, $value, $name, $id, $config )
	{
		if ( empty( $field->label ) ) {
			$field->label	=	'App Folder';
		}
		$class	=	$field->css ? ' '.$field->css : '';

		return JHtml::_( 'select.genericlist', Helper_Admin::getFolderOptions( false, true, false, true, $config['vName'] ), $name, 'class="inputbox select'.$class.'"', 'value', 'text', $value, $id );
	}

	// getFolderFilter
	public static function getFolderFilter( &$field, $value, $name, $id, $config )
	{
		$field->label	=	'App Folder';

		return JHtml::_( 'select.genericlist', Helper_Admin::getFolderOptions( true, true, true, true, $config['vName'] ), $name, 'class="inputbox select small span12" onchange="this.form.submit()"', 'value', 'text', $value, $id );
	}

	// getFolderParent
	public static function getFolderParent( &$field, $value, $name, $id, $config )
	{
		$field->label	=	'Parent';
		
		if ( @$config['item']->id == 1 || @$config['item']->id == 2 ) {
			$options	=	array();
			$options[]	=	JHtml::_( 'select.option',  0, '#', 'value', 'text' );
			$disabled	=	' disabled="disabled" ';
		} else {
			$options	=	Helper_Admin::getFolderOptions( false, false, true );
			$disabled	=	'';
		}
		
		return JHtml::_( 'select.genericlist', $options, $name, 'class="inputbox select" '.$disabled.$field->attributes, 'value', 'text', $value, $id );
	}

	// getLayer
	public static function getLayer( &$field, $value, $name, $id, $config )
	{
		$checked1	=	( $config['item']->layer == 'configuration' ) ? 'checked="checked"' : '';
		$checked2	=	( $config['item']->layer == 'fields' ) ? 'checked="checked"' : '';
		$checked3	=	( $config['item']->layer == 'template' ) ? 'checked="checked"' : '';
		$selected1	=	( $config['item']->layer == 'configuration' ) ? 'selected' : '';
		$selected2	=	( $config['item']->layer == 'fields' ) ? 'selected' : '';
		$selected3	=	( $config['item']->layer == 'template' ) ? 'selected' : '';
		
		$field->script	=	'
							$("fieldset#layer").on("click", "label", function() {
								$("#layer label").removeClass("selected"); $(this).addClass("selected");
								var current = $("#"+$(this).attr("for"));
								if (current.prop("checked") != true) {
									$("#layer input").prop("checked", false); current.prop("checked", true);
									$(".layers").slideUp();  $("#layer_"+current.val()).slideDown();
								}
							});
							';
		
		return	'<fieldset id="layer" class="toggle">'
			.	'<input type="radio" id="layer1" name="layer" value="configuration" '
			.	'style="display: none" '.$checked1.' />'
			.	'<input type="radio" id="layer2" name="layer" value="fields" '
			.	'style="display: none" '.$checked2.' />'
			.	'<input type="radio" id="layer3" name="layer" value="template" '
			.	'style="display: none" '.$checked3.' />'
			.	'<label id="layer1_label" for="layer1" class="toggle first '.$selected1.'">'
			.	JText::_( 'COM_CCK_CONFIG' ).'</label>'
			.	'<label id="layer2_label" for="layer2" class="toggle '.$selected2.'">'
			.	JText::_( 'COM_CCK_FIELDS' ).'</label>'
			.	'<label id="layer3_label" for="layer3" class="toggle last '.$selected3.'">'
			.	JText::_( 'COM_CCK_TEMPLATE' ).'</label>'
			.	'<div align="center" class="subtabs">'
			.	'<div id="subtab4"></div>'
			.	'<div id="subtab5">'.JText::_( 'COM_CCK_OPTIONS' ).'</div>'
			.	'<div id="subtab6"></div>'
			.	'</div>'
			.	'</fieldset>'
			;
	}

	// getLinkage
	public static function getLinkage( &$field, $value, $name, $id, $config )
	{
		$class	=	'icon-lock ';
		$value	=	1;

		if ( $value == '0' ) {
			$c0		=	'checked="checked"';
			$c1		=	'';
			$class	.=	'unlinked';
		} else {
			$c0		=	'';
			$c1		=	'checked="checked"';
			$class	.=	'linked';
		}

		return	'<input type="radio" id="'.$name.'0" name="'.$name.'" value="0" '.$c0.' style="display:none;" />'
			.	'<input type="radio" id="'.$name.'1" name="'.$name.'" value="1" '.$c1.' style="display:none;" />'
			.	'<a href="javascript: void(0);" id="'.$name.'" class="switch hasTooltip" title="'
			.	JText::_( 'COM_CCK_STORAGE_DESC_SHORT' ).'" data-container="body">'
			.	'<span class="'.$name.' '.$class.'"></span>'
			.	'</a>'
			;
	}

	// getLocationFilter
	public static function getLocationFilter( &$field, $value, $name, $id, $config )
	{
		return JHtml::_( 'select.genericlist', Helper_Admin::getLocationOptions(), $name, 'class="inputbox select hidden-phone" '.$field->attributes, 'value', 'text', $value, $id );
	}

	// getMediaExtensions
	public static function getMediaExtensions( &$field, $value, $name, $id, $config )
	{
		$field->attributes	=	'style="width:90px;"';
		
		$value	=	( $value != '' ) ? $value : 'common';
		
		if ( $field->options ) {
			$options	=	explode( '||', $field->options );
		} else {
			$options	=	array( 'archive', 'audio', 'document', 'image', 'video' );
		}
		
		$opts  	=	array();
		$opts[]	=	JHtml::_( 'select.option', 'common', JText::_ ( 'COM_CCK_MEDIA_TYPE_COMMON' ), 'value', 'text' );
		$opts[]	=	JHtml::_( 'select.option', 'custom', JText::_( 'COM_CCK_CUSTOM' ) );
		$opts[]	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MEDIA_TYPES' ) );
		
		foreach ( $options AS $o ) {
			$opts[]	=	JHtml::_( 'select.option', $o, JText::_ ( 'COM_CCK_MEDIA_TYPE_'.$o ), 'value', 'text' );
		}

		$opts[]	=	JHtml::_( 'select.option', '</OPTGROUP>' );
		$opts[]	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_PRESETS' ) );
		
		for ( $i = 1; $i <= 3; $i++ ) {
			if ( JCck::getConfig_Param( 'media_preset'.$i.'_extensions' ) ) {
				$label 	=	JCck::getConfig_Param( 'media_preset'.$i.'_extensions_label' );
				$label 	=	$label ? $label : JText::_( 'COM_CCK_PRESET'.$i );
				$opts[]	=	JHtml::_( 'select.option', 'preset'.$i, $label );
			}
		}
		
		$opts[]	=	JHtml::_( 'select.option', '</OPTGROUP>' );
		
		return JHtml::_( 'select.genericlist', $opts, $name, 'class="inputbox select" '.$field->attributes, 'value', 'text', $value, $id );
	}

	// getPlugins
	public static function getPlugins( &$field, $value, $name, $id, $config )
	{
		$type		=	( $field->location ) ? $field->location : 'field';
		$options	=	array();
		
		if ( trim( $field->selectlabel ) ) {
			$options[]	=	JHtml::_( 'select.option', '', '- '.$field->selectlabel.' -', 'value', 'text' );
		}

		$options	=	array_merge( $options, Helper_Admin::getPluginOptions( $type, 'cck_', false, false, true ) );
		$css		=	( $field->required == 'required' ) ? ' validate[required]' : '';
		
		return JHtml::_( 'select.genericlist', $options, $name, 'class="inputbox select'.$css.'" '.$field->attributes, 'value', 'text', $value, $id );
	}

	// getSearchClient
	public static function getSearchClient( &$field, $value, $name, $id, $config )
	{
		$checked1	=	( $config['item']->client == 'search' ) ? 'checked="checked"' : '';
		$checked2	=	( $config['item']->client == 'filter' ) ? 'checked="checked"' : '';
		$checked3	=	( $config['item']->client == 'order' ) ? 'checked="checked"' : '';
		$checked4	=	( $config['item']->client == 'list' ) ? 'checked="checked"' : '';
		$checked5	=	( $config['item']->client == 'item' ) ? 'checked="checked"' : '';
		$selected1	=	( $config['item']->client == 'search' ) ? 'selected' : '';
		$selected2	=	( $config['item']->client == 'filter' ) ? 'selected' : '';
		$selected3	=	( $config['item']->client == 'order' ) ? 'selected' : '';
		$selected4	=	( $config['item']->client == 'list' ) ? 'selected' : '';
		$selected5	=	( $config['item']->client == 'item' ) ? 'selected' : '';

		return	'<fieldset id="client" class="toggle">'
			.	'<input type="radio" id="client1" name="client" value="search" '
			.	'style="display: none" '.$checked1.' />'
			.	'<input type="radio" id="client3" name="client" value="order" '
			.	'style="display: none" '.$checked3.' />'
			.	'<input type="radio" id="client4" name="client" value="list" '
			.	'style="display: none" '.$checked4.' />'
			.	'<input type="radio" id="client5" name="client" value="item" '
			.	'style="display: none" '.$checked5.' />'
			.	'<label id="client1_label" for="client1" class="toggle first '.$selected1.'">'
			.	JText::_( 'COM_CCK_SEARCH_FORM' ).'</label>'
			.	'<label id="client3_label" for="client3" class="toggle '.$selected3.'">'
			.	JText::_( 'COM_CCK_ORDERING' ).'</label>'
			.	'<label id="client4_label" for="client4" class="toggle '.$selected4.'">'
			.	JText::_( 'COM_CCK_LIST' ).'</label>'
			.	'<label id="client5_label" for="client5" class="toggle last '.$selected5.'">'
			.	JText::_( 'COM_CCK_ITEM' ).'</label>'
			.	'<div align="center" class="subtabs">'
			.	'<div id="subtab1"></div>'
			.	'<div id="subtab2">'.JText::_( 'COM_CCK_VIEWS' ).'</div>'
			.	'<div id="subtab3"></div>'
			.	'</div>'
			.	'</fieldset>'
			;
	}

	// getStorageLocation
	public static function getStorageLocation( &$field, $value, $name, $id, $config )
	{
		if ( empty( $field->label ) ) {
			$field->label	=	'Storage Location';	
		}
		
		$value			=	( $value ) ? $value : 'joomla_article';
		$options		=	array();
		$options		=	array_merge( $options, Helper_Admin::getPluginOptions( 'storage_location', 'cck_', false, false, true ) );

		$attr			=	'data-cck';
		$base			=	JPATH_SITE.'/plugins/cck_storage_location';
		
		if ( count( $options ) ) {
			foreach ( $options as $o ) {
				if ( $o->value == '<OPTGROUP>' || $o->value == '</OPTGROUP>' ) {
					continue;
				}
				$pp		=	array( 'custom' );
		
				if ( is_file( $base.'/'.$o->value.'/'.$o->value.'.php' ) ) {
					require_once $base.'/'.$o->value.'/'.$o->value.'.php';

					$pp	=	JCck::callFunc( 'plgCCK_Storage_Location'.$o->value,'getStaticProperties',$pp );
					$v	=	$pp['custom'];
				} else {
					$v	=	'';
				}
				
				$o->$attr = 'data-custom="'.$v.'"';
			}
		}

		$attr	=	'class="inputbox select" '.$field->attributes;
		$attr	=	array(
						'id'=>$id,
						'list.attr'=>$attr,
						'list.select'=>$value,
						'list.translate'=>FALSE,
						'option.attr'=>'data-cck',
						'option.key'=>'value',
						'option.text'=>'text'
					);

		return JHtml::_( 'select.genericlist', $options, $name, $attr );
	}

	// getStorageLocation2
	public static function getStorageLocation2( &$field, $value, $name, $id, $config )
	{
		if ( empty( $field->label ) ) {
			$field->label	=	'Content Object';
		}

		$view		=	JFactory::getApplication()->input->get( 'view', '' );
		$options	=	array();
		
		if ( trim( $field->selectlabel ) ) {
			$options	=	array( JHtml::_( 'select.option',  '', '- '.$field->selectlabel.' -' ) );
		} else {
			$value		=	( $value ) ? $value : '';
			$options	=	array();
		}
		
		if ( $view == 'type' || $view == 'types' ) {
			$options[] = JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) );
		}
		$class		=	$field->css ? ' '.$field->css : '';
		$options	=	array_merge( $options, Helper_Admin::getPluginOptions( 'storage_location', 'cck_', false, false, true ) );
		
		return JHtml::_( 'select.genericlist', $options, $name, 'class="inputbox select'.$class.'" '.$field->attributes, 'value', 'text', $value, $id );
	}

	// getTables
	public static function getTables( &$field, $value, $name, $id, $config )
	{
		$field->label		=	'Table';
		$field->attributes	=	'style="max-width:200px;"';
		
		$opts		=	array();
		$prefix		=	JFactory::getConfig()->get( 'dbprefix' );
		$tables		=	JCckDatabase::getTableList();
		
		if ( trim( $field->selectlabel ) ) {
			$opts[]	=	JHtml::_( 'select.option',  '', '- '.$field->selectlabel.' -' );
		}
		
		if ( count( $tables ) ) {
			foreach ( $tables as $table ) {
				$t		=	str_replace( $prefix, '#__', $table );
				$opts[]	=	JHtml::_( 'select.option', $t, $t, 'value', 'text' );
			}
		}
		$class	=	$field->css ? ' '.$field->css : '';
		$attr	=	'class="inputbox select'.$class.'" '.$field->attributes;
		
		return JHtml::_( 'select.genericlist', $opts, $name, $attr, 'value', 'text', $value, $id );
	}

	// getType
	public static function getType( &$field, $value, $name, $id, $config )
	{
		$field->label	=	'Type';
		
		return JHtml::_( 'select.genericlist', Helper_Admin::getPluginOptions( 'field', 'cck_', false, false, true ), $name, 'class="inputbox select" tabindex="3"', 'value', 'text', $value, $id );
	}

	// getTypeClient
	public static function getTypeClient( &$field, $value, $name, $id, $config )
	{
		$uix		=	JCck::getUIX();
		$checked1	=	( $config['item']->client == 'admin' ) ? 'checked="checked"' : '';
		$checked2	=	( $config['item']->client == 'site' ) ? 'checked="checked"' : '';
		$selected1	=	( $config['item']->client == 'admin' ) ? 'selected' : '';
		$selected2	=	( $config['item']->client == 'site' ) ? 'selected' : '';
		
		if ( $uix == 'full' ) {
			$checked3	=	( $config['item']->client == 'intro' ) ? 'checked="checked"' : '';
			$checked4	=	( $config['item']->client == 'content' ) ? 'checked="checked"' : '';
			$selected3	=	( $config['item']->client == 'intro' ) ? 'selected' : '';
			$selected4	=	( $config['item']->client == 'content' ) ? 'selected' : '';
		}

		$form	=	'<fieldset id="client" class="toggle">'
				.	'<input type="radio" id="client1" name="client" value="admin" '
				.	'style="display: none" '.$checked1.' />'
				.	'<input type="radio" id="client2" name="client" value="site" '
				.	'style="display: none" '.$checked2.' />';

		if ( $uix == 'full' ) {
			$form	.=	'<input type="radio" id="client3" name="client" value="intro" '
					.	'style="display: none" '.$checked3.' />'
					.	'<input type="radio" id="client4" name="client" value="content" '
					.	'style="display: none" '.$checked4.' />';
		}

		$form	.=	'<label for="client1" class="toggle first '.$selected1.'">'
				.	JText::_( 'COM_CCK_ADMIN_FORM' ).'</label>'
				.	'<label for="client2" class="toggle '.$selected2.'">'
				.	JText::_( 'COM_CCK_SITE_FORM' ).'</label>';

		if ( $uix == 'full' ) {
			$form	.=	'<label for="client3" class="toggle '.$selected3.'">'
					.	JText::_( 'COM_CCK_INTRO' ).'</label>'
					.	'<label for="client4" class="toggle last '.$selected4.'">'
					.	JText::_( 'COM_CCK_CONTENT' ).'</label>';
		}
		
		$form	.=	'<div align="center" class="subtabs">'
				.	'<div id="subtab1"></div>'
				.	'<div id="subtab2">'.JText::_( 'COM_CCK_VIEWS' ).'</div>'
				.	'<div id="subtab3"></div>'
				.	'</div>'
				.	'</fieldset>';
		
		return $form;
	}

	// getTypeFilter
	public static function getTypeFilter( &$field, $value, $name, $id, $config )
	{
		return JHtml::_( 'select.genericlist', Helper_Admin::getPluginOptions( 'field', 'cck_', true, false, true ), $name, 'class="inputbox select small span12" onchange="this.form.submit()"', 'value', 'text', $value, $id );
	}

	// getStorageMode
	public static function getStorageMode( &$field, $value, $name, $id, $config )
	{
		$value		=	( $value ) ? $value : 'custom';
		$options	=	array();
		$options[]	=	JHtml::_( 'select.option', 'none', '- '.JText::_( 'COM_CCK_NONE' ).' -', 'value', 'text' );
		
		if ( ( JCck::getConfig_Param( 'storage_dev', '0' ) == 3 ) || ( $value == 'dev' ) ) {
			$options[] = JHtml::_( 'select.option', 'dev', JText::_ ( 'COM_CCK_DEVELOPMENT' ), 'value', 'text' );
		}

		$options	=	array_merge( $options, Helper_Admin::getPluginOptions( 'storage', 'cck_', false, false, true ) );
		
		return JHtml::_( 'select.genericlist', $options, $name, 'class="inputbox select" '.$field->attributes, 'value', 'text', $value );
	}

	// getVariation
	public static function getVariation( &$field, $value, $name, $id, $config )
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/helper_workshop.php';

		$opts	=	array();
		
		if ( trim( $field->selectlabel ) ) {
			$opts[]	=	JHtml::_( 'select.option', '', '- '.$field->selectlabel.' -', 'value', 'text' );
		}

		$opts	=	array_merge( $opts, Helper_Workshop::getPositionVariations( @$config['item']->template, false ) );
		$attr	=	'class="inputbox"';

		return JHtml::_( 'select.genericlist', $opts, $name, $attr, 'value', 'text', $value, $id );
	}
}
?>