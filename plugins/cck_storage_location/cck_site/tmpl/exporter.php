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

use Joomla\CMS\Language\Text;

require_once JPATH_SITE.'/plugins/cck_storage_location/cck_site/classes/exporter.php';
$options	=	plgCCK_Storage_LocationCck_Site_Exporter::getColumnsToExport();
$options	=	implode( '||', $options );
?>
<div class="seblod cck-padding-top-0 cck-padding-bottom-0">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_FIELDS' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'0', 'label'=>'Core Table', 'selectlabel'=>'', 'options'=>'Raw Output=optgroup||All Fields=0||Raw Prepared Output=optgroup||No Fields or from List=-1||Only Selected Fields=1', 'storage_field'=>'columns[core]' ) );
		echo '<li><label></label>'
		 .	 JCckDev::getForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'', 'label'=>'', 'selectlabel'=>'', 'type'=>'select_multiple', 'options'=>$options, 'bool8'=>0, 'size'=>0, 'storage_field'=>'columns[core_selected]' ) )
		 .	 '</li>';
		?>
	</ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	//$('#columns_core_selected').isVisibleWhen('columns_core','1');
});
</script>