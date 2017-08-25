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

// Init
$doc		=	JFactory::getDocument();
$reset		=	0;
$client		=	JFactory::getApplication()->isClient( 'site' ) ? 'site' : 'admin';
$params		=	explode( '||', $this->item->params );
$component	=	$params[0];
$section	=	$params[1];
$path		=	'';
$type		=	'rules';
	
// Prepare
if ( !$component || !$section ) {
	$form	=	'';
	return;
} elseif ( $component == 'com_cck' ) {
	$reset	=	1;
	if ( $section == 'form' ) {
		$path	=	'addfieldpath="/libraries/cck/construction/field"';
		$type	=	'cckrules';
	}
}
$xml	=	'
			<form>
				<field '.$path.'
					type="'.$type.'"
					id="'.$this->item->id.'"
					name="'.$this->item->id.'"
					label=""
					filter="rules"
					component="'.$component.'"
					section="'.$section.'"
					class="inputbox select"
				/>
				<field
					type="hidden"
					name="asset_id"
					readonly="true"
					class="readonly"
				/>
			</form>
		';
$form	=	JForm::getInstance( $this->item->id, $xml );
$form->setValue( 'asset_id', null, (int)$this->item->type );
$form	=	$form->getInput( $this->item->id );
$form	=	str_replace( 'onchange="sendPermissions.call(this, event)"', '', $form );

// Set
$js		=	'
			(function ($){
				JCck.Dev = {
					reset: function() {
						var elem = "'.$this->item->id.'";
						parent.jQuery("#"+elem).val("");
						this.close();
					},
					submit: function() {
						var elem = "'.$this->item->id.'";
						var data = {};
						var val = key = idx = "";
						$("#permissions-sliders select, #permissions-sliders input").each(function(i) {
							var name = $(this).attr("name").split("][");
							key = name[0].replace(elem+"[","");
							idx = name[1].replace("]","");
							if (!data[key]) { data[key] = {}; }
							data[key][idx] = $(this).myVal();
							if (key == "core.edit.own.content" && data[key][idx] != "") {
								if (data[key][idx] == "0") {
									data[key][idx] = "";
								} else {
									data[key][idx] = \'"\'+data[key][idx]+\'"\';
								}
							}
						});
						var encoded	= $.toJSON(data);
						parent.jQuery("#"+elem).val(encoded);
						this.close();
						return;
					}
    			}
				$(document).ready(function(){
					var reset = "'.$reset.'";
					if (!reset) {
						$("#resetBox").hide();
					}
					var elem = "'.$this->item->id.'";
					var encoded = parent.jQuery("#"+elem).val();
					var data = ( encoded != "" ) ? $.evalJSON(encoded) : "";
					if (data) {
						$.each(data, function(key, val) {
							$.each(val, function(k, v) {
								if (key == "core.edit.own.content") {
									if (v == "0" || v == \'"0"\') {
										v = "";
									} else {
										var len = v.length;
										var end = len - 1;
										if (len && v[0] == \'"\' && v[end] == \'"\') {
											v = v.substring(1,end);
										}
									}
								}
								key = key.replace(".","\\.");
								if (v!="") {
									$("[id=\'"+elem+"_"+key+"_"+k+"\']").myVal(v); /todo: empty/
								} else {
									$("[id=\'"+elem+"_"+key+"_"+k+"\']").val("");
								}
							});
						});
					}
				});
			})(jQuery);
			';
JHtml::_( 'behavior.framework' );
$doc->addScriptDeclaration( $js );
$doc->addStyleSheet( JUri::root( true ).'/media/cck/css/cck.'.$client.'.css' );
?>

<form method="post" id="adminForm" name="adminForm">
	<div class="seblod">
		<?php echo $form; ?>
	</div>
</form>