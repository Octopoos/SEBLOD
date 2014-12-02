<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) && $config['validation'] != '' ) {
	Helper_Include::addValidation( $config['validation'], $config['validation_options'], $formId );
	$js	=	'if ( jQuery("#'.$formId.'").validationEngine("validate",task) === true ) { Joomla.submitform("search", document.getElementById("'.$formId.'")); }';
} else {
	$js	=	'Joomla.submitform("search", document.getElementById("'.$formId.'"));';
}
?>
<script type="text/javascript">
<?php echo $config['submit']; ?> = function(task) { <?php echo $js; ?> }
</script>
<?php
echo ( $config['action'] ) ? $config['action'] : '<form action="'.JRoute::_( 'index.php?option=com_cck'.$action_vars ).'" autocomplete="off" method="get" id="'.$formId.'" name="'.$formId.'">';
echo ( $raw_rendering ) ? $form : '<div class="cck_module_search'.$class_sfx.'">' . $form . '</div>';
?>
<?php if ( !$raw_rendering ) { ?>
<div class="clr"></div>
<div>
<?php } ?>
	<input type="hidden" value="com_cck" name="option">
	<input type="hidden" value="list" name="view">
	<input type="hidden" value="<?php echo $params->get( 'menu_item', '' ); ?>" name="Itemid">
	<input type="hidden" value="<?php echo $preconfig['search']; ?>" name="search">
	<input type="hidden" value="search" name="task">
<?php if ( !$raw_rendering ) { ?>
</div>
<?php } ?>
</form>