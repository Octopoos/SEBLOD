<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: stage.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$doc	=	JFactory::getDocument();
$name	=	$this->item->id;
$root	=	JUri::root( true );
Helper_Include::addDependencies( 'box', 'edit' );
$doc->addStyleSheet( $root.'/media/cck/scripts/jquery-colorbox/css/colorbox.css' );
$doc->addScript( $root.'/media/cck/scripts/jquery-colorbox/js/jquery.colorbox-min.js' );
$js		=	'
			(function ($){
				JCck.Dev = {
					reset: function() {
						parent.jQuery("#'.$name.'_live_options").val("");
						this.close();
					},
					submit: function() {
						var data = {};
						data["value"] = $("#value").myVal();
						data["default_value"] = $("#default_value").myVal();
						var encoded = $.toJSON(data);
						parent.jQuery("#'.$name.'_live_options").val(encoded);
						parent.jQuery("#'.$name.'_live_value").val("");
						this.close();
						return;
					}
    			}
				$(document).ready(function(){
					var encoded = parent.jQuery("#'.$name.'_live_options").val();
					var data = ( encoded != "" ) ? $.evalJSON(encoded) : "";
					var legacy = parent.jQuery("#'.$name.'_live_value").val();
					if (legacy) {
						$("#value").myVal(legacy);
					}
					if (data) {
						$.each(data, function(k, v) {
							var elem = k;
							$("#"+elem).myVal(v);
						});
					}
					if(!$("#value").myVal()) {
						$("#value").myVal("1");
					}
				});
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ) ); ?>
	<ul class="adminformlist adminformlist-2cols">
		<?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Stage', 'selectlabel'=>'', 'options'=>'STAGE_1ST=1||STAGE_2ND=2||STAGE_3RD=3||STAGE_4TH=4||STAGE_5TH=5', 'storage_field'=>'value' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Default Value', 'storage_field'=>'default_value' ) );
		?>
	</ul>
</div>