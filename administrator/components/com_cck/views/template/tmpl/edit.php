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

$config	=	JCckDev::init( array( '42', 'radio', 'select_dynamic', 'select_simple', 'text', 'wysiwyg_editor' ), true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
$cck	=	JCckDev::preload( array( 'core_title_template', 'core_folder', 'core_description', 'core_state', 'core_name_template', 'core_type_template', 'core_featured' ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod first">       
        <ul class="spe spe_title">
            <?php echo JCckDev::renderForm( $cck['core_title_template'], $this->item->title, $config ); ?>
        </ul>
        <ul class="spe spe_folder">
			<?php echo JCckDev::renderForm( $cck['core_folder'], $this->item->folder, $config, array( 'label'=>_C0_TEXT ) ); ?>
        </ul>
        <ul class="spe spe_state spe_third">
            <?php echo JCckDev::renderForm( $cck['core_state'], $this->item->published, $config, array( 'label'=>'clear' ) ); ?>
        </ul>
        <ul class="spe spe_description">
            <?php echo JCckDev::renderForm( $cck['core_description'], $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ); ?>
        </ul>
        <ul class="spe spe_name">
            <?php echo JCckDev::renderForm( $cck['core_name_template'], $this->item->name, $config ); ?>
        </ul>
        <ul class="spe spe_type">
            <?php echo JCckDev::renderForm( $cck['core_type_template'], $this->item->mode, $config ); ?>
        </ul>
        <ul class="spe spe_type spe_latest">
            <?php echo JCckDev::renderForm( $cck['core_featured'], $this->item->featured, $config, array( 'label'=>'clear', 'selectlabel'=>'', 'options'=>'Featured=1||No=0', 'css'=>'btn-group btn-group-yesno' ) ); ?>
        </ul>
	</div>

    <div id="layer" style="text-align: center;">
        <?php
        JFactory::getLanguage()->load( 'tpl_'.$this->item->name, JPATH_SITE, null, false, true );
        
        $layer  =   JPATH_SITE.'/templates/'.$this->item->name.'/tmpl/edit.php';
        if ( is_file( $layer ) ) {
            include_once $layer;
        }
        ?>
    </div>
</div>

<div class="clr"></div>
<div>
    <input type="hidden" id="task" name="task" value="" />
    <input type="hidden" id="myid" name="id" value="<?php echo @$this->item->id; ?>" />
    <?php
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
        if (task == "template.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
            JCck.submitForm(task, document.getElementById('adminForm'));
        }
    }
    $(document).ready(function() {
        $("#featured").isVisibleWhen('mode','0');
    });
})(jQuery);
</script>