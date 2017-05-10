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

JCckDev::initScript( 'link', $this->item );
$hide	=	$this->item->alt ? 'hide' : '';
if ( $this->item->alt ) {
	$hide		=	'hide';
	$required	=	'';
} else {
	$hide		=	'';
	$required	=	'required';
}
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_LINK_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_list', '', $config, array(), array(), $hide );
		echo JCckDev::renderForm( 'core_menuitem', '', $config, array( 'required'=>$required ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>0, 'label'=>'Field', 'options'=>'None=-1||Field=optgroup||Inherited=0||Custom=1',
																		 'selectlabel'=>'', 'storage_field'=>'search_field' ), array(), $hide );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field name', 'required'=>'required', 'storage_field'=>'search_fieldname' ), array(), $hide );
		
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.JText::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
		echo JCckDev::renderForm( 'core_options_target', '', $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'options'=>'Target Blank=_blank||Target Self=_self||Target Parent=_parent||Target Top=_top||Advanced=optgroup||Modal Box=modal', 'storage_field'=>'target' ) );
		echo JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Parameters', 'cols'=>80, 'rows'=>1, 'storage_field'=>'target_params' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Rel', 'size'=>32, 'storage_field'=>'rel' ) );
		echo JCckDev::renderForm( 'core_tmpl', '', $config );
		echo JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Custom variables', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'1', 'options'=>'Apply=1||Prepare=0', 'storage_field'=>'state' ) );
		echo JCckDev::renderBlank();
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#search_fieldname').isVisibleWhen('search_field','1');
	$('#target_params').isVisibleWhen('target','modal');
});
</script>