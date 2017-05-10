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

if ( $show_list_title ) {
	$tag		=	$tag_list_title;
	$class		=	trim( $class_list_title );
	$class		=	$class ? ' class="'.$class.'"' : '';
	echo '<'.$tag.$class.'>' . @$search->title . '</'.$tag.'>';
}
if ( $description != '' ) {
	$description	=	JHtml::_( 'content.prepare', $description );

	if ( !( $tag_desc == 'p' && strpos( $description, '<p>' ) === false ) ) {
		$tag_desc	=	'div';
	}
	$description	=	'<'.$tag_desc.' class="cck_module_desc'.$class_sfx.'">' . $description . '</'.$tag_desc.'>';

	if ( $tag_desc == 'div' ) {
		$description	.=	'<div class="clr"></div>';
	}
}
if ( $show_list_desc == 1 && $description != '' ) {
	echo $description;
}
if ( ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) && $config['validation'] != '' ) {
	JCckDev::addValidation( $config['validation'], $config['validation_options'], $formId );
	$js	=	'if ( jQuery("#'.$formId.'").validationEngine("validate",task) === true ) { JCck.Core.submitForm("search", document.getElementById("'.$formId.'")); }';
} else {
	$js	=	'JCck.Core.submitForm("search", document.getElementById("'.$formId.'"));';
}
?>
<script type="text/javascript">
<?php echo $config['submit']; ?> = function(task) { <?php echo $js; ?> }
</script>
<?php
echo ( $config['action'] ) ? $config['action'] : '<form action="'.( $action_url ? $action_url : JRoute::_( 'index.php?option=com_cck'.$action_vars ) ).'" autocomplete="off" method="get" id="'.$formId.'" name="'.$formId.'">';
echo ( $raw_rendering ) ? $form : '<div class="cck_module_search'.$class_sfx.'">' . $form . '</div>';
?>
<?php if ( !$raw_rendering ) { ?>
<div class="clr"></div>
<div>
<?php } ?>
<input type="hidden" name="search" value="<?php echo $preconfig['search']; ?>" />
<input type="hidden" name="task" value="search" />
<?php if ( !JFactory::getConfig()->get( 'sef' ) ) { ?>
<input type="hidden" name="option" value="com_cck" />
<input type="hidden" name="view" value="list" />
<?php if ( $params->get( 'menu_item', '' ) ) { ?>
<input type="hidden" name="Itemid" value="<?php echo $params->get( 'menu_item', '' ); ?>" />
<?php } ?>
<?php } ?>
<?php if ( !$raw_rendering ) { ?>
</div>
<?php } ?>
</form>
<?php
if ( $show_list_desc == 2 && $description != '' ) {
	echo $description;
}
?>