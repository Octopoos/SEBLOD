<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$doc	=	JFactory::getDocument();
$js		=	'
			(function ($){
				JCck.Dev = {
					reset: function() {
						var elem = "pos-'.$this->item->id.'_variation_options";
						parent.jQuery("#"+elem).val("");
						this.close();
					},
					submit: function() {
						var elem = "pos-'.$this->item->id.'_variation_options";
						var data = $("#adminForm").serializeObject();
						var encoded = $.toJSON(data);
						parent.jQuery("#"+elem).val(encoded);
						this.close();
						return;
					}
    			}
				$(document).ready(function(){
					var elem = "pos-'.$this->item->id.'_variation_options";
					var encoded = parent.jQuery("#"+elem).val();
					var data = (encoded != "") ? $.evalJSON(encoded) : "";
					if (data) {
						$.each(data, function(k, v) {
							$("#"+k).val( v );
						});
					}
				});
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );

require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_workshop.php';

JFactory::getLanguage()->load( 'files_var_cck_'.$this->item->name.'.sys', JPATH_SITE );
JFactory::getLanguage()->load( 'files_var_cck_seb_css3.sys', JPATH_SITE );

$template	=	( isset( $this->item->type ) && $this->item->type != '' ) ? $this->item->type : 'seb_one';
$path		=	JPATH_SITE.'/templates/'.$template.'/variations/'.$this->item->name.'/options.xml';
if ( ! file_exists( $path ) ) {
	$path	=	JPATH_SITE.'/libraries/cck/rendering/variations/'.$this->item->name.'/options.xml';
} else {
	JFactory::getLanguage()->load( 'tpl_'.$template, JPATH_SITE );
}
$xml		=	JPath::clean( $path );

Helper_Workshop::getTemplateParams( $xml, '//form' );
?>