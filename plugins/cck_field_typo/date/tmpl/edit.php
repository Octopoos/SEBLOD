<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JCckDev::initScript( 'typo', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_TYPO_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_select', 'Y-m-d', $config, array( 'label'=>'Format', 'selectlabel'=>'', 'defaultvalue'=>'0', 'storage_field'=>'format',
								  'options'=>'Free=-1||Time Ago=-2||Presets=optgroup||Date Format 01=Y-m-d||Date Format 02=d m y||Date Format 03=d m Y||Date Format 04=m d y||Date Format 05=m d Y||Date Format 06=m/Y||Date Format 07=M Y||Date Format 08=F Y||Date Format 09=F d, Y||Date Format 10=d F Y||Date Format 11=l, F d, Y||Date Format 12=l, d F Y' ) );	
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Free Format', 'storage_field'=>'format_custom' ) );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('ul li:first label').css('width','115px');
	$('#format_custom').isVisibleWhen('format','-1');
});
</script>