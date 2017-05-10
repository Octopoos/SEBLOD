<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( (int)$config['error'] == 1 ) {
	return;
}
if ( ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) && $config['validation'] != '' ) {
	JCckDev::addValidation( $config['validation'], $config['validation_options'], $formId );
	$js	=	'if (jQuery("#'.$formId.'").validationEngine("validate",task) === true) { if (jQuery("#'.$formId.'").isStillReady() === true) { jQuery("#'.$formId.' input[name=\'config[unique]\']").val("'.$formId.'"); JCck.Core.submitForm("save", document.getElementById("'.$formId.'")); } }';
} else {
	$js	=	'if (jQuery("#'.$formId.'").isStillReady() === true) { jQuery("#'.$formId.' input[name=\'config[unique]\']").val("'.$formId.'"); JCck.Core.submitForm("save", document.getElementById("'.$formId.'")); }';
}
?>
<script type="text/javascript">
<?php echo $config['submit']; ?> = function(task) { <?php echo $js; ?> }
</script>
<?php
echo ( $config['action'] ) ? $config['action'] : '<form action="'.JRoute::_( 'index.php?option=com_cck' ).'" autocomplete="off" enctype="multipart/form-data" method="post" id="'.$formId.'" name="'.$formId.'">';
echo ( $raw_rendering ) ? $data : '<div class="cck_module_form'.$class_sfx.'">' . $data . '</div>';
?>
<?php if ( !$raw_rendering ) { ?>
<div class="clr"></div>
<div>
<?php } ?>
<input type="hidden" id="task" name="task" value="" />
<input type="hidden" id="myid" name="id" value="0" />
<input type="hidden" name="config[type]" value="<?php echo $preconfig['type']; ?>">
<input type="hidden" name="config[url]" value="<?php echo $config['url']; ?>" />
<input type="hidden" name="config[copyfrom_id]" value="0" />
<input type="hidden" name="config[id]" value="0" />
<input type="hidden" name="config[itemId]" value="<?php echo $app->input->getInt( 'Itemid', 0 ); ?>" />
<input type="hidden" name="config[tmpl]" value="<?php echo $app->input->getCmd( 'tmpl' ); ?>" />
<input type="hidden" name="config[unique]" value="" />
<?php echo JHtml::_( 'form.token' ); ?>
<?php if ( !$raw_rendering ) { ?>
</div>
<?php } ?>
</form>