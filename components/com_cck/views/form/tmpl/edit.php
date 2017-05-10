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

JHtml::_( 'behavior.keepalive' );

$app	=	JFactory::getApplication();
$id		=	str_replace( ' ', '_', trim( $this->pageclass_sfx ) );
$id		=	( $id ) ? 'id="'.$id.'" ' : '';

if ( !$this->raw_rendering ) { ?>
<div <?php echo $id; ?>class="cck_page cck-clrfix"><div>
<?php }
if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<h1>' . ( ( $this->escape( $this->params->get( 'page_heading' ) ) ) ? $this->escape( $this->params->get( 'page_heading' ) ) : $this->escape( $this->params->get( 'page_title' ) ) ) . '</h1>';
}
if ( $this->show_form_title ) {
	$tag		=	$this->tag_form_title;
	$class		=	trim( $this->class_form_title );
	$class		=	$class ? ' class="'.$class.'"' : '';
	echo '<'.$tag.$class.'>' . $this->title . '</'.$tag.'>';
}
if ( $this->show_form_desc == 1 && $this->description != '' ) {
	echo ( $this->raw_rendering ) ? JHtml::_( 'content.prepare', $this->description ) : '<div class="cck_page_desc'.$this->pageclass_sfx.' cck-clrfix">' . JHtml::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}
if ( isset( $this->config['error'] ) && (int)$this->config['error'] == 1 ) { ?>
	<?php if ( !$this->raw_rendering ) { ?>
		</div></div>
	<?php }
	return;
}
if ( ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) && $this->config['validation'] != '' ) {
	JCckDev::addValidation( $this->config['validation'], $this->config['validation_options'], $this->form_id );
	$js	=	'if (task == "cancel") { JCck.Core.submitForm(task, document.getElementById("'.$this->form_id.'")); } else { if (jQuery("#'.$this->form_id.'").validationEngine("validate",task) === true) { if (jQuery("#'.$this->form_id.'").isStillReady() === true) { jQuery("#'.$this->form_id.' input[name=\'config[unique]\']").val("'.$this->unique.'"); JCck.Core.submitForm(task, document.getElementById("'.$this->form_id.'")); } } }';
} else {
	$js	=	'if (jQuery("#'.$this->form_id.'").isStillReady() === true) { jQuery("#'.$this->form_id.' input[name=\'config[unique]\']").val("'.$this->unique.'"); JCck.Core.submitForm(task, document.getElementById("'.$this->form_id.'")); }';
}
?>
<script type="text/javascript">
<?php echo $this->config['submit']; ?> = function(task) { <?php echo $js; ?> }
</script>
<?php
echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.JRoute::_( 'index.php?option='.$this->option ).'" autocomplete="off" enctype="multipart/form-data" method="post" id="'.$this->form_id.'" name="'.$this->form_id.'">';
echo ( $this->raw_rendering ) ? $this->data : '<div class="cck_page_form'.$this->pageclass_sfx.' cck-clrfix" id="system">' . $this->data . '</div>';
?>
<?php if ( !$this->raw_rendering ) { ?>
<div class="clr"></div>
<?php } ?>
<?php if ( $this->config['core'] !== false ) { ?>
<?php if ( !$this->raw_rendering ) { ?>
<div>
<?php } ?>
<input type="hidden" id="task" name="task" value="" />
<input type="hidden" id="myid" name="id" value="<?php echo $this->id; ?>" />
<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
<input type="hidden" name="config[type]" value="<?php echo $this->type->name; ?>" />
<input type="hidden" name="config[stage]" value="<?php echo $this->stage; ?>" />
<input type="hidden" name="config[skip]" value="<?php echo $this->skip; ?>" />
<input type="hidden" name="config[url]" value="<?php echo $this->config['url']; ?>" />
<input type="hidden" name="config[copyfrom_id]" value="<?php echo @$this->config['copyfrom_id']; ?>" />
<input type="hidden" name="config[id]" value="<?php echo @$this->config['id']; ?>" />
<input type="hidden" name="config[itemId]" value="<?php echo $app->input->getInt( 'Itemid', 0 ); ?>" />
<input type="hidden" name="config[tmpl]" value="<?php echo $app->input->getCmd( 'tmpl' ); ?>" />
<input type="hidden" name="config[unique]" value="" />
<?php echo JHtml::_( 'form.token' ); ?>
<?php if ( !$this->raw_rendering ) { ?>
</div>
<?php } ?>
<?php } ?>
</form>
<?php
if ( $this->show_form_desc == 2 && $this->description != '' ) {
	echo ( $this->raw_rendering ) ? JHtml::_( 'content.prepare', $this->description ) : '<div class="cck_page_desc'.$this->pageclass_sfx.' cck-clrfix">' . JHtml::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}
?>
<?php if ( !$this->raw_rendering ) { ?>
</div></div>
<?php } ?>