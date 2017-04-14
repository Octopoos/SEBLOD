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

JCckDev::initScript( 'restriction', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_RESTRICTION_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_location2', '', $config, array( 'label'=>'Location' ) );
		echo JCckDev::renderForm( 'core_action2', '', $config );
		echo JCckDev::renderForm( 'core_form', '', $config, array( 'selectlabel'=>'Any Form', 'required'=>'' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Author', 'selectlabel'=>'Any Author', 'options'=>'Current=1||Someone Else=-1', 'required'=>'', 'storage_field'=>'author' ) );
        ?>
    </ul>
</div>
