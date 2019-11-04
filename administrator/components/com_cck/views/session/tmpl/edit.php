<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$extension_short_name	=	Helper_Session::getExtensionShortName( $this->item->extension );

$config		=	JCckDev::init( array( '42', 'radio', 'select_dynamic', 'select_simple', 'text', 'wysiwyg_editor' ), true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );	
$cck		=	JCckDev::preload( array( 'core_title_field', 'core_session_extension', 'more_'.$extension_short_name.'_storage_location' ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod first">
		<ul class="spe spe_title">
	        <?php echo JCckDev::renderForm( $cck['core_title_field'], $this->item->title, $config ); ?>
	    </ul>
	    <ul class="spe spe_folder">
			<?php echo JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolder', 'name'=>'core_folder' ), $this->item->folder, $config, array( 'label'=>_C0_TEXT, 'storage_field'=>'folder' ) ); ?>
	    </ul>
        <ul class="spe spe_third">
			<?php echo JCckDev::renderForm( $cck['core_session_extension'], $this->item->extension, $config, array( 'attributes'=>'disabled="disabled"' ) ); ?>
	    </ul>
        
	</div>
    
	<div class="seblod">
        <div class="legend top left"><?php echo JText::_( 'COM_CCK_SETTINGS' ); ?></div>
       	<pre><?php echo htmlspecialchars( json_encode( json_decode( $this->item->options ), JSON_PRETTY_PRINT ) ); ?></pre>
	</div>
</div>

<div class="clr"></div>
<div>
    <input type="hidden" id="task" name="task" value="" />
    <input type="hidden" id="myid" name="id" value="<?php echo @$this->item->id; ?>" />
    <input type="hidden" name="extension" value="<?php echo $this->item->extension; ?>" />
    <?php
    echo JHtml::_( 'form.token' );
	?>
</div>
</form>

<?php
Helper_Display::quickCopyright();
?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	JCck.submitForm(task, document.getElementById('adminForm'));
}
</script>