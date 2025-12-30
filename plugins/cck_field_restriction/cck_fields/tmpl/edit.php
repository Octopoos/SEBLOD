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

use Joomla\CMS\HTML\HTMLHelper;

JCckDev::initScript( 'restriction', $this->item );
JCck::loadModalBox();

require_once JPATH_COMPONENT.'/helpers/helper_admin.php';
$lives	=	array_merge( array( HTMLHelper::_( 'select.option', '', Text::_( 'COM_CCK_DEFAULT' ) ) ), Helper_Admin::getPluginOptions( 'field_live', 'cck_', false, false, true ) );
$html	=	HTMLHelper::_( 'select.genericlist', $lives, 'live', 'class="input select"', 'value', 'text', '', 'live' );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_RESTRICTION_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo '<li><label>'.Text::_( 'COM_CCK_PROPERTY' ).'</label>'
         .	 JCckDev::getForm( 'core_dev_select', '', $config, array( 'label'=>'Property', 'selectlabel'=>'', 'defaultvalue'=>'value', 'options'=>'Custom=-1||Standard=optgroup||Form=form||Value=value', 'required'=>'required', 'storage_field'=>'property' ) )
         .	 JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'css'=>'input-small', 'storage_field'=>'property_custom' ) )
         .	 '</li>';
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Invert', 'defaultvalue'=>'0', 'options'=>'Yes=1||No=0', 'storage_field'=>'do' ) );
		echo '<li class="w100"><label>'.Text::_( 'COM_CCK_FIELD_NAME_VALUES' ).'</label>'
		 .	 JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'storage_field'=>'trigger' ) )
		 .	 JCckDev::getForm( 'core_dev_select', '', $config, array( 'label'=>'', 'selectlabel'=>'', 'defaultvalue'=>'isEqual', 'options'=>'STATE_IS_EQUAL_IN=isEqual||STATE_IS_FILLED=isFilled||STATE_IS_FUTURE=isFuture||STATE_IS_FUTURE_ONLY=isFutureOnly||STATE_HAS_EACH=hasEach', 'storage_field'=>'match' ) )
		 .	 $html
		 .	 JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'', 'defaultvalue'=>'', 'css'=>'input-small', 'storage_field'=>'values' ) )
		 .	 '<span class="c_link" id="live_button" name="live_button">+</span>'
		 .	 '</li>';

        echo JCckDev::renderSpacer( Text::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.Text::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Priority', 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'options'=>'5||6', 'storage_field'=>'priority' ) );
        ?>
    </ul>
</div>

<?php
JCckDev::addField( 'live', $config );
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#live').isVisibleWhen('match','hasEach,isEqual,isFuture,isFutureOnly',false);
	$('#property_custom').isVisibleWhen('property','-1',false);
	if ($("#match").val() == "isFilled") {
		$("#live_button,#values").hide();
	} else {
		if ($("#live").val()) {
			$("#live_button").show();
			$("#values").hide();
		} else {
			$("#live_button").hide();
			$("#values").show();
		}
	}
	$("div#layout").on("change", "#match", function() {
		if ($(this).val() == "isFilled") {
			$("#live_button,#values").hide();
		} else {
			if ($("#live").val()) {
				$("#live_button").show();
				$("#values").hide();
			} else {
				$("#live_button").hide();
				$("#values").show();
			}
		}
	});
	$("div#layout").on("change", "#live", function() {
		$("#values").val("");
		if ($(this).val()) {
			$("#live_button").show();
			$("#values").hide();
		} else {
			$("#live_button").hide();
			$("#values").show();
		}
	});
	$("div#layout").on("click", "span.c_link", function() {
		var type = $("#live").val();
		if (type) {
			var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=plugins/cck_field_live/"+type+"/tmpl/edit.php&id=values&name="+type+"&validation=1";
			$.colorbox({href:url, iframe:true, innerWidth:930, innerHeight:550, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
		}
	});
});
</script>