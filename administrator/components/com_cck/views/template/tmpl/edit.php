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

$config	=	JCckDev::init( array( '42', 'radio', 'select_dynamic', 'select_simple', 'text', 'wysiwyg_editor' ), true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
$cck	=	JCckDev::preload( array( 'core_title_template', 'core_description', 'core_state', 'core_name_template', 'core_type_template', 'core_featured' ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="<?php echo $this->css['wrapper_first']; ?>">  
		<?php
		$dataTmpl	=	array(
							'fields'=>array(
								'description'=>JCckDev::getForm( $cck['core_description'], $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ),
								'featured'=>JCckDev::renderForm( $cck['core_featured'], $this->item->featured, $config, array( 'label'=>( JCck::on( '4.0' ) ? 'Featured' : 'clear' ), 'selectlabel'=>'', 'options'=>'No=0||Yes=1', 'css'=>'btn-group btn-group-yesno' ) ),
								'folder'=>JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolder', 'name'=>'core_folder' ), $this->item->folder, $config, array( 'label'=>_C0_TEXT, 'storage_field'=>'folder' ) ),
								'name'=>JCckDev::renderForm( $cck['core_name_template'], $this->item->name, $config ),
								'state'=>JCckDev::renderForm( $cck['core_state'], $this->item->published, $config, array( 'label'=>( JCck::on( '4.0' ) ? 'Status' : 'clear' ) ) ),
								'title'=>JCckDev::renderForm( $cck['core_title_template'], $this->item->title, $config ),
								'type'=>JCckDev::renderForm( $cck['core_type_template'], $this->item->mode, $config, array( 'required'=>'required' ) )
							),
							'item'=>$this->item,
							'params'=>array()
						);

		echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.template.edit_main', $dataTmpl );
		?> 
	</div>

	<div class="main-card">
		<?php
		if ( JCck::on( '4.0' ) ) {
			echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768] );
			echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'details', JText::_( 'COM_CCK_DETAILS' ) );
		}
		?>
		<div id="layer" style="text-align: center;">
			<?php
			JFactory::getLanguage()->load( 'tpl_'.$this->item->name, JPATH_SITE, null, false, true );
			
			$layer  =   JPATH_SITE.'/templates/'.$this->item->name.'/tmpl/edit.php';
			if ( is_file( $layer ) ) {
				include_once $layer;
			}
			?>
		</div>
		<?php
		if ( JCck::on( '4.0' ) ) {
			echo HTMLHelper::_( 'uitab.endTab' );
			echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'publishing', JText::_( 'COM_CCK_PUBLISHING' ) );
			echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.template.edit_publishing', $dataTmpl );
			echo HTMLHelper::_( 'uitab.endTab' );
			echo HTMLHelper::_( 'uitab.endTabSet' );
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
	};
	Joomla.submitbutton = function(task) {
		if (task == "template.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
			JCck.submitForm(task, document.getElementById('adminForm'));
		}
	};
	$(document).ready(function() {
		$("#featured").isVisibleWhen('mode','0');
	});
})(jQuery);
</script>