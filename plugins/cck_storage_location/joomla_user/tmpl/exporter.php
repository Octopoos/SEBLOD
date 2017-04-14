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

require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_user/classes/exporter.php';
$options	=	plgCCK_Storage_LocationJoomla_User_Exporter::getColumnsToExport();
$options	=	implode( '||', $options );
?>

<div class="seblod cck-padding-top-0 cck-padding-bottom-0">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_FIELDS' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'0', 'label'=>'Core Table', 'selectlabel'=>'', 'options'=>'All Fields=0||No Fields or from List=-1||Only Selected Fields=1', 'storage_field'=>'columns[core]' ) );
		echo '<li><label></label>'
		 .	 JCckDev::getForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'', 'label'=>'', 'selectlabel'=>'', 'type'=>'select_multiple', 'options'=>$options, 'bool8'=>0, 'size'=>0, 'storage_field'=>'columns[core_selected]' ) )
		 .	 '</li>';
		?>
	</ul>
</div>

<script type="text/javascript">
(function ($){
	JCck.Dev.applyConditionalStates = function() {
		$('#columns_core_selected').isVisibleWhen('columns_core','1');
	}
	$(document).ready(function() {
		JCck.Dev.applyConditionalStates();
	});
})(jQuery);
</script>