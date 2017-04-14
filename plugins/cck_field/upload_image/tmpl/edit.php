<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$options2	=	JCckDev::fromJSON( $this->item->options2 );
$media_ext	=	( $this->isNew ) ? '' : ( ( isset( $options2['media_extensions'] ) ) ? $options2['media_extensions'] : 'custom' );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
		echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		echo JCckDev::renderForm( 'core_options_path', @$options2['path'], $config, array( 'required'=>'required' ) );
		echo JCckDev::renderForm( 'core_options_format_file', @$options2['storage_format'], $config, array( 'options'=>'Full Path=0' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_PATH_PER_CONTENT' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_path_content', @$options2['path_content'], $config )
		 .	 JCckDev::getForm( 'core_dev_text', @$options2['folder_permissions'], $config, array( 'defaultvalue'=>'0755', 'size'=>4, 'storage_field'=>'json[options2][folder_permissions]' ) )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_options_path_user', @$options2['path_user'], $config );
		echo '<li><label>'.JText::_( 'COM_CCK_LEGAL_EXTENSIONS' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_media_extensions', $media_ext, $config, array( 'defaultvalue'=>'image', 'options'=>'image' ) )
		 .	 JCckDev::getForm( 'core_options_legal_extensions_image', @$options2['legal_extensions'], $config, array( 'size'=>13, 'required'=>'required' ) )
		 .	 '</li>';
		echo '<li><label>'.JText::_( 'COM_CCK_MAXIMUM_SIZE' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_max_size', @$options2['max_size'], $config )
		 .	 JCckDev::getForm( 'core_options_size_unit', @$options2['size_unit'], $config )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_options_preview_image', @$options2['form_preview'], $config, array( 'label'=>'SHOW_PREVIEW', 'storage_field'=>'json[options2][form_preview]' ) );
		echo JCckDev::renderForm( 'core_options_delete_box', @$options2['delete_box'], $config );
		echo '<li><label>'.JText::_( 'COM_CCK_SHOW_CUSTOM_PATH' ).'</label>'
		 .	 JCckDev::getForm( 'core_bool', @$options2['custom_path'], $config, array( 'defaultvalue'=>'0', 'options'=>'Hide=0||Show=1', 'storage_field'=>'json[options2][custom_path]' ) )
		 .	 JCckDev::getForm( 'core_options_path', @$options2['path_label'], $config, array( 'defaultvalue'=>'Path', 'size'=>18, 'storage_field'=>'json[options2][path_label]' ) )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_size', $this->item->size, $config );
		echo JCckDev::renderForm( 'core_options_multivalue_mode', @$options2['multivalue_mode'], $config, array( 'label'=>'MULTIVALUE_MODE' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_SHOW_TITLE' ).'</label>'
		 .	 JCckDev::getForm( 'core_bool', @$options2['title_image'], $config, array( 'defaultvalue'=>'1', 'options'=>'Hide=0||Show=1', 'storage_field'=>'json[options2][title_image]' ) )
		 .	 JCckDev::getForm( 'core_options_path', @$options2['title_label'], $config, array( 'defaultvalue'=>'Title', 'size'=>18, 'storage_field'=>'json[options2][title_label]' ) )
		 .	 '</li>';
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
		echo '<li><label>'.JText::_( 'COM_CCK_SHOW_DESCRIPTION_ALT' ).'</label>'
		 .	 JCckDev::getForm( 'core_bool', @$options2['desc_image'], $config, array( 'defaultvalue'=>'1', 'options'=>'Hide=0||Show=1', 'storage_field'=>'json[options2][desc_image]' ) )
		 .	 JCckDev::getForm( 'core_options_path', @$options2['desc_label'], $config, array( 'defaultvalue'=>'Description alt', 'size'=>18, 'storage_field'=>'json[options2][desc_label]' ) )
		 .	 '</li>';

		echo JCckDev::renderHelp( 'field', 'seblod-2-x-upload-image-field' );
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_PROCESSING' ), JText::_( 'COM_CCK_PROCESSING_DESC_UPLOAD_IMAGE' ), 2 );
		echo JCckDev::renderForm( 'core_options_force_thumb_creation', @$options2['force_thumb_creation'], $config, array( 'label'=>'FORCE_THUMB_CREATION' ) );
		echo JCckDev::renderForm( 'core_options_preview_image', @$options2['content_preview'], $config, array( 'defaultvalue'=>'1', 'label'=>'DISPLAY_AS_DEFAULT',
																'options'=> 'Image=0||Thumb1=1||Thumb2=2||Thumb3=3||Thumb4=4||Thumb5=5||Thumb6=6||Thumb7=7||Thumb8=8||Thumb9=9||Thumb10=10', 'storage_field'=>'json[options2][content_preview]' ) );
		echo JCckDev::renderForm( 'core_options_image_process', @$options2['image_process'], $config, array( 'label'=>'Image', 'defaultvalue'=>'', 'storage_field'=>'json[options2][image_process]' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_WIDTH_HEIGHT' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_image_width', @$options2['image_width'], $config )
		 .	 '<span class="variation_value" style="margin-right: 5px;">x</span>'
		 .	 JCckDev::getForm( 'core_options_image_height', @$options2['image_height'], $config )
		 .	 '<span class="variation_value">px</span></li>';
		// core_options_image_color (see later)
		// core_options_image_watermark (see later)
		// core_options_image_default (see later)
		// core_options_image_box (see later)
		echo JCckDev::renderForm( 'core_options_thumb_process', @$options2['thumb1_process'], $config, array( 'label'=>'Thumb1' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_WIDTH_HEIGHT' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_thumb_width', @$options2['thumb1_width'], $config )
		 .	 '<span class="variation_value" style="margin-right: 5px;">x</span>'
		 .	 JCckDev::getForm( 'core_options_thumb_height', @$options2['thumb1_height'], $config )
		 .	 '<span class="variation_value">px</span></li>';
		$js	=	'';
		for ( $i = 2; $i < 11; $i++ ) {
			echo JCckDev::renderForm( 'core_options_thumb_process', @$options2['thumb'.$i.'_process'], $config, array( 'label'=>'Thumb'.$i, 'defaultvalue'=>'', 'storage_field'=>'json[options2][thumb'.$i.'_process]' ) );
			echo '<li><label>'.JText::_( 'COM_CCK_WIDTH_HEIGHT' ).'</label>'
		 	 .	 JCckDev::getForm( 'core_options_thumb_width', @$options2['thumb'.$i.'_width'], $config, array( 'defaultvalue'=>'', 'storage_field'=>'json[options2][thumb'.$i.'_width]' ) )
		 	 .	 '<span class="variation_value" style="margin-right: 5px;">x</span>'
		 	 .	 JCckDev::getForm( 'core_options_thumb_height', @$options2['thumb'.$i.'_height'], $config, array( 'defaultvalue'=>'', 'storage_field'=>'json[options2][thumb'.$i.'_height]' ) )
		 	 .	 '<span class="variation_value">px</span></li>';
		 	 $js	.=	'$("#json_options2_thumb'.$i.'_width").isVisibleWhen("json_options2_thumb'.$i.'_process","addcolor,crop,crop_dynamic,maxfit,shrink,stretch,stretch_dynamic,shrink_dynamic",true,"visibility");'."\n";
		}
		
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ), 3 );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
		?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#json_options2_legal_extensions').isVisibleWhen('json_options2_media_extensions','custom',false);
	$('#json_options2_image_width').isVisibleWhen('json_options2_image_process','addcolor,crop,crop_dynamic,maxfit,shrink,stretch,stretch_dynamic,shrink_dynamic',true,'visibility');
	$('#json_options2_thumb1_width').isVisibleWhen('json_options2_thumb1_process','addcolor,crop,crop_dynamic,maxfit,shrink,stretch,stretch_dynamic,shrink_dynamic',true,'visibility');
	<?php echo $js; ?>
	$('#json_options2_title_image').isVisibleWhen('json_options2_multivalue_mode','1');
	$('#json_options2_desc_image').isVisibleWhen('json_options2_multivalue_mode','1');
	$('#blank_li').isVisibleWhen('json_options2_multivalue_mode','1');
	$('#json_options2_path_label').isVisibleWhen('json_options2_custom_path','1',false);
	$('#json_options2_title_label').isVisibleWhen('json_options2_title_image','1',false);
	$('#json_options2_desc_label').isVisibleWhen('json_options2_desc_image','1',false);
	$('#json_options2_storage_format').isDisabledWhen('json_options2_path_user','1' );
	$('#json_options2_path_user').isDisabledWhen('json_options2_storage_format','1' );
});
</script>