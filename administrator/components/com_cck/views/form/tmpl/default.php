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

JHtml::_( 'behavior.keepalive' );

$app	=	JFactory::getApplication();
Helper_Include::addScriptDeclaration( $this->config['javascript'] );
if ( ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) && $this->config['validation'] != '' ) {
	JCckDev::addValidation( $this->config['validation'], $this->config['validation_options'], $this->form_id );
	$js	=	'if (task == "form.cancel") { JCck.Core.submitForm(task, document.getElementById("'.$this->form_id.'")); } else { if (jQuery("#'.$this->form_id.'").validationEngine("validate",task) === true) { if (jQuery("#'.$this->form_id.'").isStillReady() === true) { jQuery("#'.$this->form_id.' input[name=\'config[unique]\']").val("'.$this->form_id.'"); JCck.Core.submitForm(task, document.getElementById("'.$this->form_id.'")); } } }';
} else {
	$js	=	'if (jQuery("#'.$this->form_id.'").isStillReady() === true) { jQuery("#'.$this->form_id.' input[name=\'config[unique]\']").val("'.$this->form_id.'"); JCck.Core.submitForm(task, document.getElementById("'.$this->form_id.'")); }';
}
?>

<script type="text/javascript">
<?php echo $this->config['submit']; ?> = function(task) { <?php echo $js; ?> }
</script>

<?php
echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.JRoute::_( 'index.php?option=com_cck' ).'" autocomplete="off" enctype="multipart/form-data" method="post" id="'.$this->form_id.'" name="'.$this->form_id.'">';
echo $this->loadTemplate( 'toolbar' );
echo $this->data;
?>

<div class="clr"></div>
<div>
    <input type="hidden" id="task" name="task" value="" />
    <input type="hidden" id="myid" name="id" value="<?php echo @$this->id; ?>" />
    
    <input type="hidden" name="config[type]" value="<?php echo @$this->type->name; ?>">
    <input type="hidden" name="config[stage]" value="<?php echo $this->stage; ?>">
    <input type="hidden" name="return" value="<?php echo $app->input->getBase64( 'return' ); ?>" />
    <input type="hidden" name="return_o" value="<?php echo $app->input->get( 'return_o', '' ); ?>" />
    <input type="hidden" name="return_v" value="<?php echo $app->input->get( 'return_v', '' ); ?>" />
    <input type="hidden" name="return_extension" value="<?php echo $app->input->get( 'extension', '' ); ?>" />
    <input type="hidden" name="config[copyfrom_id]" value="<?php echo @$this->config['copyfrom_id']; ?>" />
    <input type="hidden" name="config[id]" value="<?php echo @$this->config['id']; ?>" />
    <input type="hidden" name="config[unique]" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</div>
</form>

<?php
Helper_Display::quickCopyright();
?>