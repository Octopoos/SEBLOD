<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( JCck::on( '4.0' ) ) {
	return;
}
?>
<div class="seblod" id="storage_more" <?php echo ( $value == 'dev' ) ? '' : 'style="display: none;"'?>>
	<div class="legend top left"><span class="hasTooltip qtip_cck" title="<?php echo htmlspecialchars( JText::_( 'COM_CCK_STUFF_DESC' ) ); ?>"><?php echo JText::_( 'COM_CCK_STUFF' ); ?></span></div>
	<ul class="adminformlist adminformlist-2cols">
		<?php
		$required	=	JCckDev::get( $cck['core_required'], $config['item']->required, $config );
		$class_css	=	JCckDev::get( $cck['core_dev_text'], $config['item']->css, $config, array( 'label'=>'Class CSS', 'storage_field'=>'css' ) );
		$attributes	=	JCckDev::get( $cck['core_attributes'], $config['item']->attributes, $config, array( 'label'=>'Custom Attributes' ) );
		$script		=	JCckDev::get( $cck['core_script'], $config['item']->script, $config );
		?>
		<li>
			<label><?php echo $class_css->label; ?></label><?php echo $class_css->form; ?>
		</li>
		<li class="storage_more" <?php echo ( $value == 'dev' ) ? '' : 'style="display: none;"'?>>
			<label><?php echo $required->label; ?></label><?php echo $required->form; ?>
		</li>
		<li class="w100">
			<label><?php echo $attributes->label; ?></label><?php echo $attributes->form; ?>
		</li>
		<li class="w100">
			<label><?php echo $script->label; ?></label><?php echo $script->form; ?>
		</li>