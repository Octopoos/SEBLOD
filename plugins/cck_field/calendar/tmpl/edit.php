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
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
        echo JCckDev::renderForm( 'core_options_format', @$options2['format'], $config );
		echo JCckDev::renderForm( 'core_options_format_date', @$options2['storage_format'], $config, array( 'label'=>'STORAGE_FORMAT', 'storage_field'=>'json[options2][storage_format]' ) );
		echo JCckDev::renderForm( 'core_options_dates', @$options2['dates'], $config );
		echo JCckDev::renderForm( 'core_options_theme_calendar', @$options2['theme'], $config );
		echo JCckDev::renderForm( 'core_options_week_numbers', @$options2['week_numbers'], $config );
        echo JCckDev::renderForm( 'core_size', $this->item->size, $config, array( 'defaultvalue'=>'27' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_SHOW_TIME' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_time', @$options2['time'], $config, array( 'options' => 'Hide=0||Show=optgroup||12AM=12||24H=24' ) )
		 .	 JCckDev::getForm( 'core_options_time_pos', @$options2['time_pos'], $config )
		 .	 '</li>';
		echo '<li><label>'.JText::_( 'COM_CCK_DEFAULT_TIME' ).'</label>'
		 .	 JCckDev::getForm( 'core_dev_select_numeric', @$options2['default_hour'], $config, array( 'defaultvalue'=>'00', 'selectlabel'=>'',
		 									'storage_field'=>'json[options2][default_hour]',
											'options2'=>'{"math":"0","start":"0","first":"","step":"1","last":"","end":"23","force_digits":"2"}') )
		 .	 JCckDev::getForm( 'core_dev_select_numeric', @$options2['default_min'], $config, array( 'defaultvalue'=>'00', 'selectlabel'=>'',
		 									'storage_field'=>'json[options2][default_min]',
											'options2'=>'{"math":"0","start":"0","first":"","step":"1","last":"","end":"59","force_digits":"2"}') )
		 .	 JCckDev::getForm( 'core_dev_select_numeric', @$options2['default_sec'], $config, array( 'defaultvalue'=>'00', 'selectlabel'=>'',
		 									'storage_field'=>'json[options2][default_sec]',
											'options2'=>'{"math":"0","start":"0","first":"","step":"1","last":"","end":"59","force_digits":"2"}') )
		 .	 '</li>';
		echo JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label'=>'Text Input', 'defaultvalue'=>'0', 'options'=>'Enabled=1||Readonly=0' ) );

		echo JCckDev::renderHelp( 'field', 'seblod-2-x-calendar-field' );
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config, array(), array( 'alter_type_value' => 'DATETIME' ) );
        ?>
    </ul>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#json_options2_time_pos').isVisibleWhen('json_options2_time','12,24',false);
		$('#json_options2_default_hour').isVisibleWhen('json_options2_time','0');
		$('#json_options2_storage_format').on('change', function() {
			if ($(this).val() == 1)  {
				$('#storage_alter_type').val('INT(11)');
				$('#storage_alter0').click();

			} else {
				$('#storage_alter_type').val('DATETIME');
				$('#storage_alter0').click();
			}
		});
	});
</script>