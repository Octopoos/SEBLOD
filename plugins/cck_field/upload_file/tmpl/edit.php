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
		echo JCckDev::renderForm( 'core_options_path', @$options2['path'], $config, array( 'required'=> 'required' ) );
		echo JCckDev::renderForm( 'core_options_format_file', @$options2['storage_format'], $config );
		echo '<li><label>'.JText::_( 'COM_CCK_PATH_PER_CONTENT' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_path_content', @$options2['path_content'], $config )
		 .	 JCckDev::getForm( 'core_dev_text', @$options2['folder_permissions'], $config, array( 'defaultvalue'=>'0755', 'size'=>4, 'storage_field'=>'json[options2][folder_permissions]' ) )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_options_path_user', @$options2['path_user'], $config );
		echo '<li><label>'.JText::_( 'COM_CCK_LEGAL_EXTENSIONS' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_media_extensions', $media_ext, $config )
		 .	 JCckDev::getForm( 'core_options_legal_extensions', @$options2['legal_extensions'], $config, array( 'size'=>13, 'required'=>'required' ) )
		 .	 '</li>';
		echo '<li><label>'.JText::_( 'COM_CCK_MAXIMUM_SIZE' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_max_size', @$options2['max_size'], $config )
		 .	 JCckDev::getForm( 'core_options_size_unit', @$options2['size_unit'], $config )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_dev_select', @$options2['forbidden_extensions'], $config, array( 'label'=>'Forbidden Extensions', 'selectlabel'=>'Inherited', 'options'=>'None=0||Whitelist=1', 'storage_field'=>'json[options2][forbidden_extensions]' ) );
		echo JCckDev::renderForm( 'core_size', $this->item->size, $config );
		echo JCckDev::renderForm( 'core_options_preview', @$options2['preview'], $config );
		echo JCckDev::renderForm( 'core_options_delete_box', @$options2['delete_box'], $config );
		echo '<li><label>'.JText::_( 'COM_CCK_SHOW_CUSTOM_PATH' ).'</label>'
		 .	 JCckDev::getForm( 'core_bool', @$options2['custom_path'], $config, array( 'defaultvalue'=>'0', 'options'=>'Hide=0||Show=1', 'storage_field'=>'json[options2][custom_path]' ) )
		 .	 JCckDev::getForm( 'core_options_path', @$options2['path_label'], $config, array( 'defaultvalue'=>'Path', 'size'=>18, 'storage_field'=>'json[options2][path_label]' ) )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_options_multivalue_mode', @$options2['multivalue_mode'], $config, array( 'label'=>'MULTIVALUE_MODE' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_SHOW_TITLE' ).'</label>'
		 .	 JCckDev::getForm( 'core_bool', @$options2['title_file'], $config, array( 'defaultvalue'=>'1', 'options'=>'Hide=0||Show=1', 'storage_field'=>'json[options2][title_file]' ) )
		 .	 JCckDev::getForm( 'core_options_path', @$options2['title_label'], $config, array( 'defaultvalue'=>'Link Title', 'size'=>18, 'storage_field'=>'json[options2][title_label]' ) )
		 .	 '</li>';
		// core_options_path_box (useless)
		
		echo JCckDev::renderHelp( 'field', 'seblod-2-x-upload-file-field' );
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#json_options2_legal_extensions').isVisibleWhen('json_options2_media_extensions','custom',false);
	$('#json_options2_title_file').isVisibleWhen('json_options2_multivalue_mode','1');
	$('#json_options2_path_label').isVisibleWhen('json_options2_custom_path','1',false);
	$('#json_options2_title_label').isVisibleWhen('json_options2_title_file','1',false);
	if ( $('#json_options2_storage_format').val() == 1 ){
		$('#json_options2_path_user').val(0);
	}
	$('#json_options2_storage_format').change( function() {
		if ( $('#json_options2_storage_format').val() == 1 ){
			$('#json_options2_path_user').val(0);
		}
	});
	$('#json_options2_storage_format').isDisabledWhen('json_options2_path_user','1' );
	$('#json_options2_path_user').isDisabledWhen('json_options2_storage_format','1' );
});
</script>