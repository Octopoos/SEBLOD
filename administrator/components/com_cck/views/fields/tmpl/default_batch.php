<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_batch.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( !$user->authorise( 'core.edit', 'com_cck' ) ) {
    return;
}
?>
<div class="<?php echo $this->css['batch']; ?>" id="collapseModal"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header">
        <?php if ( !JCck::on( '4.0' ) ) { ?>
            <button type="button" class="close" data-dismiss="modal">×</button>
        <?php } ?>
        <h3 class="modal-title"><?php echo JText::_( 'COM_CCK_BATCH_PROCESS'); ?></h3>
        <?php if ( JCck::on( '4.0' ) ) { ?>
            <button type="button" class="btn-close novalidate" data-bs-dismiss="modal" aria-label="Close"></button>
        <?php } ?>
    </div>
    <div class="modal-body">
        <p><?php echo JText::_( 'COM_CCK_BATCH_PROCESS_'.$this->vName ); ?></p>
        <div class="control-group">
            <div class="control-label">
                <label for="batch_folder"><?php echo JText::_( 'COM_CCK_SET_APP_FOLDER' ); ?></label>
            </div>
            <div class="controls">
                <?php echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolder', 'name'=>'core_folder' ), '', $config, array( 'label'=>_C0_TEXT, 'storage_field'=>'batch_folder', 'css'=>'no-chosen' ) ); ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" onclick="" <?php echo $this->html['attr_modal_close']; ?>><?php echo JText::_( 'JCANCEL' ); ?></button>
        <button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('batchFolder');"><?php echo JText::_( 'COM_CCK_GO' ); ?></button>
    </div>
</div></div></div>