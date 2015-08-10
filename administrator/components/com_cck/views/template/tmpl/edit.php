<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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
    
    <?php if ( !$this->isNew ) { ?>
        <div class="seblod">
            <div class="legend top left"><?php echo JText::_( 'COM_CCK_SOURCES' ); ?></div>
            <div id="cck_tree" class="cck_tree" style="padding-left:22px; width: 240px; float: left;">
	            <?php echo $this->item->tree; ?>
            </div>
            <div style="float: left;">
	            <?php
				if ( count( $this->item->files ) ) {
					$html	=	'<table class="adminlist mediamanager cck_radius2 table table-striped table-bordered">';
					foreach ( $this->item->files as $k => $f ) {
						$html	.=	'<tr class="row'.( $k % 2 ).'">'
								.	'<td>'.$f.'</td>'
								.	'<td class="hidden-phone">'.'/'.'templates'.'/'.$this->item->name.'/'.$f.'</td>'
								.	'</tr>';
					}
					$html	.=	'</table>';
					echo $html;
				}
				?>
            </div>
        </div>
    <?php } ?>
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