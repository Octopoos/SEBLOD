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

$config		=	JCckDev::init( array( '42', 'jform_accesslevel', 'radio', 'select_simple', 'text', 'wysiwyg_editor' ), true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
$cck		=	JCckDev::preload( array( 'core_title_search', 'core_description', 'core_state',
										 'core_location2', 'core_alias', 'core_access' ) );
$lang		=	JFactory::getLanguage();
$key		=	'COM_CCK_TRANSLITERATE_CHARACTERS';
$style		=	'seblod';
if ( $lang->hasKey( $key ) == 1 ) {
	$transliterate	=	JText::_( $key );
	$transliterate	=	'{"'.str_replace( array( ':', '||' ), array( '":"', '","' ), $transliterate ).'"}';
} else {
	$transliterate	=	'{}';
}
JHtml::_( 'bootstrap.tooltip' );
$sidebar_inner	=	288;
$sidebar_top	=	93;

Helper_Include::addDependencies( $this->getName(), $this->getLayout() );

if ( $this->item->client == 'list' ) {
	$block_item	=	( strpos( @$this->style->params, '"cck_client_item":"1"' ) !== false || strpos( @$this->style->params, '"cck_client_item":"2"' ) !== false ) ? 0 : 1;	
} else {
	if ( $this->item->template_list ) {
		$params		=	JCckDatabase::loadResult( 'SELECT params FROM #__template_styles WHERE id = '.(int)$this->item->template_list );
		$block_item	=	( strpos( $params, '"cck_client_item":"1"' ) !== false || strpos( $params, '"cck_client_item":"2"' ) !== false ) ? 0 : 1;	
	} else {
		$block_item	=	1;
	}
}
?>

<div id="seblod-app-builder" class="clearfix">
<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?> full">
	<div class="<?php echo $this->css['wrapper_first']; ?>">
		<?php
		// if ( !$this->item->id ) {
		// 	echo '<li><label>'.JText::_( 'COM_CCK_QUICK_MENU_ITEM' ).'</label>'
		// 	 .	 '<select id="quick_menuitem" name="quick_menuitem" class="inputbox" style="max-width:180px;">'
		// 	 .	 '<option value="">- '.JText::_( 'COM_CCK_SELECT_A_PARENT').' -</option>'
		// 	 .	 JHtml::_( 'select.options', JHtml::_( 'menu.menuitems' ) )
		// 	 .	 '</select></li>';
		// } else {
		// 	echo '<li>&nbsp;</li>';
		// }
		?>
		<?php
		$dataTmpl	=	array(
							'fields'=>array(
								'access'=>JCckDev::renderForm( $cck['core_access'], $this->item->access, $config, array( 'css'=>'max-width-180' ) ),
								'alias'=>JCckDev::renderForm( $cck['core_alias'], $this->item->alias, $config ),
								'css_core'=>JCckDev::renderForm( 'core_css_core', $this->item->stylesheets, $config, array( 'label'=>'Stylesheets', 'css'=>'max-width-180', 'storage_field'=>'stylesheets' ) ),
								'description'=>JCckDev::getForm( $cck['core_description'], $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ),
								'folder'=>JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolder', 'name'=>'core_folder' ), $this->item->folder, $config, array( 'label'=>_C0_TEXT, 'storage_field'=>'folder' ) ),
								'location'=>JCckDev::renderForm( $cck['core_location2'], $this->item->location, $config, array( 'css'=>'max-width-140' ) ),
								'name'=>'<input type="hidden" id="name" name="name" value="'.$this->item->name.'" />',
								'state'=>JCckDev::renderForm( $cck['core_state'], $this->item->published, $config, array( 'label'=>( JCck::on( '4.0' ) ? 'Status' : 'clear' ) ) ),
								'storage_location'=>JCckDev::renderFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getStorageLocation2', 'name'=>'core_storage_location2' ), $this->item->storage_location, $config, array( 'selectlabel'=>'Select', 'css'=>'max-width-140', 'required'=>'required', 'storage_field'=>'storage_location' ) ),
								'title'=>JCckDev::renderForm( $cck['core_title_search'], $this->item->title, $config )
							),
							'item'=>$this->item,
							'params'=>array()
						);

		echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.search.edit_main', $dataTmpl );
		?>
	</div>
</div>

<div class="main-card">
	<?php
	if ( JCck::on( '4.0' ) ) {
		echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768] );
		echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'details', JText::_( 'COM_CCK_DETAILS' ) );
		echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getSearchClient', 'name'=>'core_client_search' ), $this->item->client, $config, array( 'storage_field'=>'client' ) );
		echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getLayer', 'name'=>'core_layer' ), $this->item->layer, $config );
	} else {
	?>
	<div class="row togglebar">
		<div>
		<?php
		echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getSearchClient', 'name'=>'core_client_search' ), $this->item->client, $config, array( 'storage_field'=>'client' ) );
		echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getLayer', 'name'=>'core_layer' ), $this->item->layer, $config );
		?>
		</div>
	</div>
	<div class="clr"></div>
	<?php } ?>
	<div align="center" id="layers"></div>
	<?php
	if ( JCck::on( '4.0' ) ) {
		echo HTMLHelper::_( 'uitab.endTab' );
		echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'options', JText::_( 'COM_CCK_OPTIONS' ) );
		echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.search.edit_options', $dataTmpl );
		echo HTMLHelper::_( 'uitab.endTab' );
		echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'publishing', JText::_( 'COM_CCK_PUBLISHING' ) );
		echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.search.edit_publishing', $dataTmpl );
		echo HTMLHelper::_( 'uitab.endTab' );
		echo HTMLHelper::_( 'uitab.endTabSet' );
	}
	?>
</div>

<div>
	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" id="myid" name="id" value="<?php echo @$this->item->id; ?>" />
	<input type="hidden" id="element" name="element" value="search" />
	<?php
	if ( $this->isNew && $this->tpl_list && !$this->item->skip ) {
		echo '<input type="hidden" id="tpl_list" name="tpl_list" value="'.$this->tpl_list.'" />';
	} elseif ( $this->item->skip ) {
		echo '<input type="hidden" id="cck_type" name="cck_type" value="'.$this->item->cck_type.'" />';
	}
    echo $this->form->getInput( 'id' );
    if ( !isset( $config['validation']['maxSize'] ) ) {
    	$config['validation']['maxSize']	=	'"maxSize":{"regex":"none","alertText":"* '.JText::_( 'PLG_CCK_FIELD_VALIDATION_MAXLENGTH_ALERT' ).'","alertText2":"'.JText::_( 'PLG_CCK_FIELD_VALIDATION_MAXLENGTH_ALERT2' ).'"}';
    }
	JCckDev::validate( $config );
    echo JHtml::_( 'form.token' );
	?>
</div>
</form>
</div><div id="seblod-loading"<?php echo (int)JCck::getConfig_Param( 'development_overlay', '1' ) ? '' : ' class="disabled"'; ?>></div>

<?php
Helper_Display::quickCopyright();
JText::script( 'COM_CCK_OPTIONAL' );
JText::script( 'COM_CCK_REQUIRED' );
JText::script( 'COM_CCK_GET_FIELDS_FROM_VIEW_CONFIRM' );

$js4	=	'';

if ( JCck::on( '4.0' ) ) {
	$js4	=	'
				var $b4 = $(\'[aria-controls="details"]\');
				$b4.html(\'<span class="d">\'+$b4.html()+\'</span>\');
				$("#client,#layer").appendTo($b4);
				$("#client").after(\'<span class="i icon-arrow-right"></span>\');

				$(document).ready(function(){
					$("#adminForm").on( "click", "#seblod-sidebar .expand-main", function() {
						$("#layer_fields > div").toggleClass("expanded");
					});
				});';
}
?>

<script type="text/javascript">
(function ($){
	JCck.Dev = {
		block_item:<?php echo $block_item; ?>,
		count:7,
		insidebox:'<?php echo $this->insidebox; ?>',
		legacy:<?php echo JCck::on( '4' ) ? 0 : 1; ?>,
		name:"search",
		prompt_group:"<?php echo str_replace( '<br />', '\n', JText::_( 'COM_CCK_MOVE_FIELDS_TO_GROUP' ) ); ?>",
		root:"<?php echo JUri::root(); ?>",
		sb_inner:<?php echo $sidebar_inner; ?>,
		sb_top:<?php echo $sidebar_top; ?>,
		skip:"&skip=<?php echo $this->item->skip; ?>",
		token:Joomla.getOptions("csrf.token")+"=1",
		transliteration:<?php echo $transliterate; ?>,
		trash:"",
		uix:"<?php echo $this->uix; ?>"
	};
	Joomla.submitbutton = function(task) {
		if (task == JCck.Dev.name+".cancel") {
			$("#layers").remove(); JCck.submitForm(task, document.getElementById('adminForm'));
		} else {
			if ($("#adminForm").validationEngine("validate",task) === true) {
				JCck.DevHelper.preSubmit(); JCck.submitForm(task, document.getElementById('adminForm'));
			}
		}
	};
	$(document).ready(function(){
		$("#toolbar-save-new button").prop("disabled",true);
		var outerDiv = $("#seblod-app-builder");
		
		$("#seblod-loading:not(.disabled)")
			.css("top", outerDiv.position().top - $(window).scrollTop())
			.css("left", "0")
			.css("width", "100%")
			.css("height", "100%")
			.css("display", "block")
			.css("margin-top", "-10px");
		<?php echo $js4; ?>
	});
})(jQuery);
</script>