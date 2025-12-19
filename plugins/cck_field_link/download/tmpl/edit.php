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

JCckDev::initScript( 'link', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_LINK_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Task', 'selectlabel'=>'', 'defaultvalue'=>'download', 'options'=>'Download=download||Read=read', 'storage_field'=>'type', 'required'=>'required' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'', 'label'=>'Content', 'selectlabel'=>'Current', 'options'=>'Use Value=optgroup||Field=2', 'storage_field'=>'content' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'storage_field'=>'file_fieldname' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'storage_field'=>'content_fieldname' ) );
		echo JCckDev::renderForm( 'core_menuitem', '', $config, array( 'selectlabel'=>'None' ) );
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'defaultvalue'=>'0', 'label'=>'Client', 'storage_field'=>'file_client' ) );

		echo JCckDev::renderSpacer( Text::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.Text::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_attributes', '', $config, array( 'label'=>'Custom Attributes', 'storage_field'=>'attributes' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
		echo JCckDev::renderForm( 'core_options_target', '', $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'options'=>'Target Blank=_blank||Target Self=_self||Target Parent=_parent||Target Top=_top||Advanced=optgroup', 'storage_field'=>'target' ) );
		echo '<li><label>'.Text::_( 'COM_CCK_TITLE' ).'</label>'
			. JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'None', 'options'=>'Custom Text=2||Translated Text=3', 'storage_field'=>'title' ) )
			. JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Title', 'size'=>16, 'css'=>'input-medium', 'storage_field'=>'title_custom' ) )
			. '</li>';
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#content_fieldname,#content_location,#blank_li').isVisibleWhen('content','2');
	$('#title_custom').isVisibleWhen('title','2,3',false);
});
</script>