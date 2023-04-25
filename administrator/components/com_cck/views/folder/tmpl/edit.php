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

use Joomla\CMS\HTML\HTMLHelper;

$uix		=	JCck::getUIX();
$config		=	JCckDev::init( array( '42', 'checkbox', 'colorpicker', 'radio', 'text', 'wysiwyg_editor' ), true, array( 'item' => $this->item ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="<?php echo $this->css['wrapper_first']; ?>">
        <?php
        $attr         =   ( $this->item->id == 1 || $this->item->id == 2 ) ? 'disabled="disabled"' : '';
        $dataTmpl   =   array(
                            'fields'=>array(
                                'color'=>JCckDev::renderForm( 'core_color', $this->item->color, $config ),
                                'colorchar'=>JCckDev::renderForm( 'core_colorchar', $this->item->colorchar, $config ),
                                'description'=>JCckDev::getForm( 'core_description', $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ),
                                'elements'=>JCckDev::getForm( 'core_elements', $this->item->elements, $config, array( 'bool'=>1 ) ),
                                'featured'=>JCckDev::renderForm( 'core_featured', $this->item->featured, $config, array( 'attributes'=>$attr, 'css'=>'btn-group btn-group-yesno' ), array(), 'w100' ),
                                'folder'=>JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolderParent', 'name'=>'core_folder_folder' ), $this->item->parent_id, $config, array( 'storage_field'=>'parent_id', 'required'=>'required' ) ),
                                'home'=>JCckDev::renderForm( 'core_featured', $this->item->home, $config, array( 'label'=>'App Root',  'options'=>'No=0||Yes App Root=1', 'storage_field'=>'home', 'attributes'=>$attr, 'css'=>'btn-group btn-group-yesno' ), array(), 'w100' ),
                                'introchar'=>JCckDev::renderForm( 'core_introchar', $this->item->introchar, $config ),
                                'state'=>JCckDev::renderForm( 'core_state', $this->item->published, $config, array( 'label'=>( JCck::on( '4.0' ) ? 'Status' : 'clear' ), 'defaultvalue'=>1 ) ),
                                'title'=>JCckDev::renderForm( 'core_title_folder', $this->item->title, $config )
                            ),
                            'item'=>$this->item,
                            'params'=>array()
                        );

        echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.folder.edit_main', $dataTmpl );
        ?>
	</div>
    
    <div class="main-card">
        <?php
        if ( JCck::on( '4.0' ) ) {
            echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768] );
            echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'details', JText::_( 'COM_CCK_DETAILS' ) );
            echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.folder.edit_details', $dataTmpl );
            echo HTMLHelper::_( 'uitab.endTab' );
            echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'publishing', JText::_( 'COM_CCK_PUBLISHING' ) );
            echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.folder.edit_publishing', $dataTmpl );
            echo HTMLHelper::_( 'uitab.endTab' );
            echo HTMLHelper::_( 'uitab.endTabSet' );
        }
        ?>
    </div>

    <?php if ( !JCck::on( '4.0' ) ) { ?>
	<div class="seblod">
        <div class="legend top left"><?php echo JText::_( 'COM_CCK_OPTIONS' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
			<?php
            echo $dataTmpl['fields']['home'];
            echo $dataTmpl['fields']['featured'];
            echo JCckDev::renderForm( 'core_dev_text', $this->item->icon_path, $config, array( 'label'=>'Icon', 'size'=>64, 'storage_field'=>'icon_path' ), array(), 'w100' );
            echo $dataTmpl['fields']['elements'];
            echo $dataTmpl['fields']['color'];
            echo $dataTmpl['fields']['introchar'];
            echo $dataTmpl['fields']['colorchar'];
            ?>
        </ul>
        <a id="toggle_acl" href="javascript:void(0);" class="btn btn-small" style="float:right;"><span class="icon-users"></span></a>
	</div>
    <?php } ?>
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
    };
    Joomla.submitbutton = function(task) {
        if (task == "folder.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
            JCck.submitForm(task, document.getElementById('adminForm'));
        }
    };
    $(document).ready(function() {
        $("#toggle_acl").click(function(){
            $("#acl").slideToggle();
        });
        var insidebox = '<?php echo $this->insidebox; ?>';
        if (insidebox) { $("#title").after(insidebox); }
    });
})(jQuery);
</script>