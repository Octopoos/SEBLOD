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

JCckDev::initScript( 'live', $this->item );
JCck::loadModalBox();
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_LIVE_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'', 'label'=>'User', 'selectlabel'=>'Current', 'options'=>'', 'storage_field'=>'content' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_PROPERTY' ).'<span class="star"> *</span></label>'
		 .	 JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Property', 'required'=>'required', 'storage_field'=>'property' ) )
		 .	 '<span id="storage_field_pick_property" name="property" class="value-picker"><span class="icon-menu-2"></span></span>';
		 ;
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Excluded Values', 'storage_field'=>'excluded' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Default Value', 'storage_field'=>'default_value' ) );
		?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#excluded').isVisibleWhen('property','access,groups');

	$("span.value-picker").on("click", function() {
        var field = $(this).attr("name");
        var cur = "none";
        var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/selection.php&title=construct&name=joomla_user&type="+field+"&id=object_property";
        $.colorbox({href:url, iframe:true, innerWidth:300, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
    });
});
</script>