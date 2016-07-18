<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JCck::loadjQuery( true, true );
jimport( 'joomla.filesystem.file' );
$config		=	JCckDev::init( array(), true );
$isImage	=	( JFile::getExt( $this->file ) == 'png' || JFile::getExt( $this->file ) == 'jpg' ) ? 1 : 0;
$doc		=	JFactory::getDocument();
?>
<form action="<?php echo JRoute::_( 'index.php' ) ?>" method="post" id="adminForm" name="adminForm">
<div id="toolbarBox" style="float: right; text-align: right; padding-right: 8px; padding-bottom: 8px; font-weight: bold;">
    <div style="float: left; padding-right: 8px;" id="messageBox"></div>
	<?php if ( $isImage == 1 ) { ?>
        <a href="javascript:void(0);" id="closeBox" class="btn btn-small" onclick="JCck.Dev.close();"><i class="icon-cancel"></i>
			<?php echo JText::_( 'COM_CCK_CLOSE' ); ?>
		</a>
    <?php } else { ?>
        <a href="javascript:void(0);" id="submitBox" class="btn btn-small" onclick="JCck.Dev.submit();"><i class="icon-save"></i>
			<?php echo JText::_( 'COM_CCK_SAVE_AND_CLOSE' ); ?>
		</a>
        <a href="javascript:void(0);" id="resetBox" class="btn btn-small" onclick="JCck.Dev.reset();"><i class="icon-refresh"></i>
			<?php echo JText::_( 'COM_CCK_RESET' ); ?>
		</a>
        <a href="javascript:void(0);" id="closeBox" class="btn btn-small" onclick="JCck.Dev.close();"><i class="icon-cancel"></i>
			<?php echo JText::_( 'COM_CCK_CANCEL' ); ?>
		</a>
    <?php } ?>
</div>
<div>
    <div id="layout" style="text-align: center; margin-top: 10px; float: left;">
		<?php
		if ( $this->function ) {
			$this->onceFile( 'require', $config );
			$this->function( $this->item->title, $this->item->name, $this->item->type, $this->item->params );
		} else {
			if ( $isImage == 1 ) {
				echo '<img src="'.$this->file.'" />';
			} else {
				$this->onceFile( 'include', $config );
			}
		}
        ?>
    </div>
</div>
<div class="clr"></div>
<?php
if ( $this->doValidation == 1 ) {
	JCckDev::validate( $config );
}
$js		=	'if("undefined"===typeof JCck.Dev){JCck.Dev={}};'
		.	'JCck.Dev.close = function() { if (parent.jQuery.colorbox) {parent.jQuery.colorbox.close();}'
		.	'else {if (window.parent.SqueezeBox) {window.parent.SqueezeBox.close();};} };';
$doc->addScriptDeclaration( $js );
?>
</form>