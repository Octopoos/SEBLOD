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
$to_admin	=	( is_array( @$options2['to_admin'] ) ) ? implode( ',', $options2['to_admin'] ) : ( ( @$options2['to_admin'] ) ? $options2['to_admin'] : '' );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
		<?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
		echo '<li><label>'.JText::_( 'COM_CCK_SEND_EMAIL' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_send', @$options2['send'], $config )
		 .	 JCckDev::getForm( 'core_options_from_param', @$options2['send_field'], $config, array( 'label' => 'Send Email Field', 'defaultvalue' => '', 'size' => '14', 'storage_field' => 'json[options2][send_field]' ) )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_options_from', @$options2['from'], $config );
		echo JCckDev::renderForm( 'core_options_from_param', @$options2['from_param'], $config );
		echo JCckDev::renderForm( 'core_options_from', @$options2['from_name'], $config, array( 'label' => 'From Name', 'options' => 'Default=0||Name=1||Field=3', 'storage_field' => 'json[options2][from_name]' ) );
		echo JCckDev::renderForm( 'core_options_from_param', @$options2['from_name_param'], $config, array( 'label' => 'From Name Field', 'storage_field' => 'json[options2][from_name_param]' ) );
		echo JCckDev::renderForm( 'core_options_subject', @$options2['subject'], $config );
        echo JCckDev::renderForm( 'core_options_to', @$options2['to'], $config );
		echo JCckDev::renderForm( 'core_options_message', @$options2['message'], $config );
		echo JCckDev::renderForm( 'core_options_to_field', @$options2['to_field'], $config );
		echo JCckDev::renderForm( 'core_options_from_param', @$options2['message_field'], $config, array( 'label' => 'Message Field', 'defaultvalue' => '', 'storage_field' => 'json[options2][message_field]' ) );
		echo JCckDev::renderForm( 'core_options_to_admin', $to_admin, $config );
		echo JCckDev::renderForm( 'core_options_from_param', @$options2['send_attachment_field'], $config, array( 'label' => 'Send Attachment Field', 'defaultvalue' => '', 'storage_field' => 'json[options2][send_attachment_field]' ) );
		echo JCckDev::renderForm( 'core_options_to', @$options2['attachment_field'], $config, array( 'label' => 'Attachment Field', 'defaultvalue' => '', 'storage_field' => 'json[options2][attachment_field]' ) );
		echo JCckDev::renderForm( 'core_options_from', @$options2['cc'], $config, array( 'label' => 'CC', 'options' => 'None=0||Email=1||Field=3', 'storage_field' => 'json[options2][cc]' ) );
		echo JCckDev::renderForm( 'core_options_to', @$options2['cc_param'], $config, array( 'label' => 'CC Email Field', 'storage_field' => 'json[options2][cc_param]' ) );
		echo JCckDev::renderForm( 'core_options_from', @$options2['bcc'], $config, array( 'label' => 'BCC', 'options' => 'None=0||Email=1||Field=3', 'storage_field' => 'json[options2][bcc]' ) );
		echo JCckDev::renderForm( 'core_options_to', @$options2['bcc_param'], $config, array( 'label' => 'BCC Email Field', 'storage_field' => 'json[options2][bcc_param]' ) );
        echo JCckDev::renderForm( 'core_size', $this->item->size, $config );
        echo JCckDev::renderForm( 'core_dev_select', @$options2['format'], $config, array( 'label'=>'Format', 'defaultvalue'=>'1', 'selectlabel'=>'', 'options'=>'HTML=1||HTML as Plain Text=2||Plain Text=0', 'storage_field'=>'json[options2][format]' ) );
		
		echo JCckDev::renderHelp( 'field', 'seblod-2-x-email-field' );
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#json_options2_from_param').isVisibleWhen('json_options2_from','1,3',true,'visibility');
	$('#json_options2_from_name_param').isVisibleWhen('json_options2_from_name','1,3',true,'visibility');
	$('#json_options2_cc_param').isVisibleWhen('json_options2_cc','1,3',true,'visibility');
	$('#json_options2_bcc_param').isVisibleWhen('json_options2_bcc','1,3',true,'visibility');
});
</script>