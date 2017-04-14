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

$uix		=	JCck::getUIX();
$config		=	JCckDev::init( array( '42', 'checkbox', 'colorpicker', 'jform_rules', 'radio', 'text', 'wysiwyg_editor' ), true, array( 'item' => $this->item ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod first">
        <ul class="spe spe_title">
            <?php echo JCckDev::renderForm( 'core_title_folder', $this->item->title, $config ); ?>
        </ul>
        <ul class="spe spe_folder">
            <?php echo JCckDev::renderForm( 'core_folder_folder', $this->item->parent_id, $config ); ?>
        </ul>
        <ul class="spe spe_state spe_third">
            <?php echo JCckDev::renderForm( 'core_state', $this->item->published, $config, array( 'label'=>'clear', 'defaultvalue'=>1 ) ); ?>
        </ul>
        <ul class="spe spe_description">
            <?php echo JCckDev::renderForm( 'core_description', $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ); ?>
        </ul>
	</div>
    
	<div class="seblod">
        <div class="legend top left"><?php echo JText::_( 'COM_CCK_OPTIONS' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
			<?php
            $attr   =   ( $this->item->id == 1 || $this->item->id == 2 ) ? 'disabled="disabled"' : '';

            echo JCckDev::renderForm( 'core_featured', $this->item->home, $config, array( 'label'=>'App Root', 'options'=>'No=0||Yes App Root=1', 'storage_field'=>'home', 'attributes'=>$attr ), array(), 'w100' );
            echo JCckDev::renderForm( 'core_featured', $this->item->featured, $config, array( 'attributes'=>$attr ), array(), 'w100' );
            echo JCckDev::renderForm( 'core_dev_text', $this->item->icon_path, $config, array( 'label'=>'Icon', 'size'=>64, 'storage_field'=>'icon_path' ), array(), 'w100' );
            echo JCckDev::renderForm( 'core_elements', $this->item->elements, $config, array( 'bool'=>1 ) );
            echo JCckDev::renderForm( 'core_color', $this->item->color, $config );
            echo JCckDev::renderForm( 'core_introchar', $this->item->introchar, $config );
            echo JCckDev::renderForm( 'core_colorchar', $this->item->colorchar, $config );
            ?>
        </ul>
        <a id="toggle_acl" href="javascript:void(0);" class="btn btn-small" style="float:right;"><span class="icon-users"></span></a>
	</div>
    
	<div class="seblod" id="acl" style="display: none;">
		<div class="legend top left"><?php echo JText::_( 'COM_CCK_PERMISSIONS' ); ?></div>
		<?php echo JCckDev::getForm( 'core_rules_folder', $this->item->asset_id, $config ); ?>
    </div>
</div>

<div class="clr"></div>
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="parent_db" value="<?php echo @$this->item->parent_db; ?>" />
    <?php
	echo $this->form->getInput( 'id' );
	JCckDev::validate( $config );
    echo JHtml::_( 'form.token' );
	?>
</div>
</form>

<?php
Helper_Display::quickCopyright();
?>

<script type="text/javascript">
(function ($){
    JCck.Dev = {
        submit: function(task) {
            Joomla.submitbutton(task);
        }
    }
    Joomla.submitbutton = function(task) {
        if (task == "folder.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
            JCck.submitForm(task, document.getElementById('adminForm'));
        }
    }
    $(document).ready(function() {
        $("#toggle_acl").click(function(){
            $("#acl").slideToggle();
        });
        var insidebox = '<?php echo $this->insidebox; ?>';
        if (insidebox) { $("#title").after(insidebox); }
    });
})(jQuery);
</script>