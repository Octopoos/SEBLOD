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

$app	=	JFactory::getApplication();
$lang	=	JFactory::getLanguage();
$my		=	JFactory::getUser();
$tmpl	=	$app->input->getString( 'tmpl', '' );
$wrap	=	( $tmpl ) ? $this->css['wrapper_tmpl'] : $this->css['wrapper'];

$ajax_load	=	'components/com_cck/assets/styles/seblod/images/ajax.gif';
$cck		=	JCckDev::preload( array( 'core_title_field', 'core_folder', 'core_name_field', 'core_state', 'core_type', 'core_description' ) );
$config		=	JCckDev::init( array(), true, array( 'item'=>$this->item, 'vName'=>$this->vName, 'tmpl'=>'' ) );
$key		=	'COM_CCK_TRANSLITERATE_CHARACTERS';
if ( $lang->hasKey( $key ) == 1 ) {
	$transliterate	=	JText::_( $key );
	$transliterate	=	'{"'.str_replace( array( ':', '||' ), array( '":"', '","' ), $transliterate ).'"}';
} else {
	$transliterate	=	'{}';
}
Helper_Include::addDependencies( $this->getName(), $this->getLayout(), $tmpl );

JHtml::_( 'bootstrap.tooltip' );

JText::script( 'JLIB_APPLICATION_SAVE_SUCCESS' );
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<?php if ( $tmpl ) { ?>
    <div id="ajaxToolbar" class="span12">
        <div style="float: left;" id="ajaxMessage"></div>
        <a href="javascript:void(0);" class="btn btn-small" id="cancel_ajax"><span class="icon-unpublish"></span>
			<?php echo JText::_( 'COM_CCK_CLOSE' ); ?>
        </a>
        <a href="javascript:void(0);" class="btn btn-small submit_ajax" data-task="save2new"><span class="icon-save-new"></span>
			<?php echo JText::_( 'JTOOLBAR_SAVE_AND_NEW' ); ?>
		</a>
        <a href="javascript:void(0);" class="btn btn-small submit_ajax" data-task="save"><span class="icon-save"></span>
			<?php echo JText::_( 'COM_CCK_SAVE_AND_CLOSE' ); ?>
		</a>
		<a href="javascript:void(0);" class="btn btn-small btn-success submit_ajax" data-task="apply"><span class="icon-apply"></span>
			<?php echo JText::_( 'COM_CCK_SAVE' ); ?>
		</a>
    </div>
<?php } ?>

<div class="<?php echo $wrap; ?>">
	<div class="seblod first">
        <div id="loading" class="loading"></div>
        <ul class="spe spe_title">
            <?php echo JCckDev::renderForm( $cck['core_title_field'], $this->item->title, $config ); ?>
        </ul>
        <ul class="spe spe_folder">
			<?php echo JCckDev::renderForm( $cck['core_folder'], $this->item->folder, $config, array( 'label'=>_C0_TEXT ) ); ?>
        </ul>
        <ul class="spe spe_state">
            <?php echo JCckDev::renderForm( $cck['core_state'], $this->item->published, $config, array( 'label'=>'clear' ) ); ?>
        </ul>
        <ul class="spe spe_name">
	        <?php
			$ajax			=	'';
    		$iAmSuperAdmin	=	$my->authorise( 'core.admin' );
			if ( ! $this->item->id || ( $this->item->id && ( JCck::getConfig_Param( 'storage_dev', '0' ) == 1 && $iAmSuperAdmin === true ) ) ) {
				JFactory::getLanguage()->load( 'plg_cck_field_validation_ajax_availability', JPATH_ADMINISTRATOR, null, false, true );
				$class	=	'inputbox text validate[required,custom[field_name],ajax[availability_name]]';
				$extra	=	'';
				if ( (int)$this->item->id > 0 ) {
					$extra	=	'&avKey=id&avPk='.$this->item->id.'&avPv='.htmlspecialchars( $this->item->name );
				}
				$ajax	=	'"availability_name":{"url": "index.php?option=com_cck&task=ajax&format=raw&file=/plugins/cck_field_validation/ajax_availability/assets/ajax/script.php"'
						.	',"extraData": "avTable=cck_core_fields&avColumn=name'.$extra.'"'
						.	',"alertText": "* '.JText::_( 'PLG_CCK_FIELD_VALIDATION_AJAX_AVAILABILITY_ALERT' ).'"'
						.	',"alertTextOk": "* '.JText::_( 'PLG_CCK_FIELD_VALIDATION_AJAX_AVAILABILITY_ALERT2' ).'"'
						.	',"alertTextLoad": "* '.JText::_( 'PLG_CCK_FIELD_VALIDATION_AJAX_AVAILABILITY_ALERT3' ).'"}';
				echo	'<li><label>'.JText::_( 'COM_CCK_NAME' ).'<span class="star"> *</span></label>'
					.	'<input type="text" id="name" name="name" value="'.$this->item->name.'" class="'.$class.'" maxlength="50" size="28" tabindex="2" />'
					.	'</li>';				
			} else {
				echo '<li><label>'.JText::_( 'COM_CCK_NAME' ).'</label><span class="variation_value" style="display:block;"><strong>'.$this->item->name.'</strong></span>'
				 .	 '<input type="hidden" id="name" name="name" value="'.$this->item->name.'" /></li>';
			}
			?>
		</ul>
        <ul class="spe spe_type">
            <?php echo JCckDev::renderForm( $cck['core_type'], $this->item->type, $config ); ?>
        </ul>
        <ul class="spe spe_description spe_latest">
            <?php echo JCckDev::renderForm( $cck['core_description'], $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ); ?>
        </ul>
	</div>
    
    <div id="layer" style="text-align: center;">
	    <?php
		$type	=	( $this->item->type ) ? $this->item->type : 'text';
		$layer	=	JPATH_PLUGINS.'/cck_field/'.$type.'/tmpl/edit.php';
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
    <?php if ( $tmpl == 'component' ) { ?>
    <input type="hidden" id="tmpl" name="tmpl" value="component" />
    <input type="hidden" id="brb" name="brb" value="field" />
    <?php } ?>
    <?php
    echo $this->form->getInput( 'id' );
	$config['validation']['field_name']				=	'"field_name":{"regex": /^[a-z0-9_]+$/,"alertText":"* '.JText::_( 'COM_CCK_FIELD_NAME_VALIDATION' ).'"}';
	if ( $ajax ) {
		$config['validation']['availability_name']	=	$ajax;
	}
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
		doTranslation:"<?php echo JCck::getConfig_Param( 'language_jtext', 0 ); ?>",
		name:"field",
		transliteration:<?php echo $transliterate; ?>,
		ajaxLayer: function(view, layout, elem, mydata) {
			var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />";  
			$.ajax({
				cache: false,
				data: mydata,
				type: "POST",
				url: "index.php?option=com_cck&view="+view+"&layout="+layout+"&format=raw",
				beforeSend:function(){ $("#loading").html(loading); $(elem).html(""); },
				success: function(response){ $("#loading").html(""); $(elem).css("opacity", 0.4).html(response).fadeTo("fast",1); },
				error:function(){ $(elem).html("<div><strong>Oops!</strong> Try to close the page & re-open it properly.</div>"); }
			});
		},
		ajaxTask: function(task) {
			var existing = $("#myid").val();
			var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />";
			$("#task").val(task);
			$.ajax({
				cache: false,
				data: $("#adminForm").serialize(),
				type: "POST",
				url: 'index.php?option=com_cck&task='+task,
				beforeSend:function(){ $("#ajaxMessage").html(loading); },
				success: function() {
					if (task!='field.cancel') {
						if ( !existing ) {
							var fieldname = '&fieldname='+$("#name").val();
							var element = '&element='+parent.jQuery("#element").val();
							var client = '&client='+parent.jQuery('input[name=client]:checked', '#adminForm').val();
							$.ajax({
								cache: false,
								type: "POST",
								url: 'index.php?option=com_cck&task=ajax_field_li&format=raw'+fieldname+element+client,
								success: function(response) {
									var obj = jQuery.parseJSON(response);
									$("#myid").val(obj.id); $("#jform_id").val(obj.id);
									var elem = parent.jQuery('input:radio[name="positions"]:checked').attr('golast');
									if (!(!elem || elem=="undefined")) {
										if (!parent.jQuery("ul#sortable1 li#"+obj.id).length) {
											parent.jQuery(elem).before(obj.html);
											JCck.DevHelper.switchP(JCck.DevHelper.getPane('parent'), obj.id, 'parent');
										}
									}
									var target_id = "#layer_fields_options";
									if (obj.construction != "") {
										parent.jQuery(target_id).after('<div id="layer_fields_options_tmp">'+obj.construction+'</div>');
										parent.jQuery(target_id+"_tmp select").each(function(i) {
											if (!parent.jQuery(target_id+" #"+$(this).attr("id")).length) {
												$(this).appendTo(parent.jQuery(target_id));
											}
							  			});
										parent.jQuery(target_id+"_tmp").remove();
									}
									if (task=="field.save2new") {
										$('#ajaxMessage').html('');
										document.location.replace("index.php?option=com_cck&task=field.add&tmpl=component&ajax_state=1&ajax_type=text");
									} else {
										$('#ajaxMessage').html('<span class="badge badge-info">'+Joomla.JText._("JLIB_APPLICATION_SAVE_SUCCESS")+'</span>').hide().fadeIn(150, function() {
											if ( task=="field.save" && parent.jQuery.colorbox ) { parent.jQuery.colorbox.close(); } else { $('#ajaxMessage').html(''); }
										});
									}
								}
							});
						} else {
							if (task=="field.save2new") {
								$('#ajaxMessage').html('');
								document.location.replace("index.php?option=com_cck&task=field.add&tmpl=component&ajax_state=1&ajax_type=text");
							} else {
								$('#ajaxMessage').html('<span class="badge badge-info">'+Joomla.JText._("JLIB_APPLICATION_SAVE_SUCCESS")+'</span>').hide().fadeIn(150, function() {
									if ( task=="field.save" && parent.jQuery.colorbox ) { parent.jQuery.colorbox.close(); } else { $('#ajaxMessage').html(''); }
								});
							}
						}
					}
				}
			});
		},
		transliterateName: function() {
			if ($("span.insidebox").length > 0) { var p = $("span.insidebox").html()+"_"; } else { var p = ""; }
			var str = JCck.transliterate(p+$("#title").val(),JCck.Dev.transliteration);
			$("#name").val( str.toLowerCase().replace(/^\s+|\s+$/g,"").replace(/\s/g, "_").replace(/[^a-z0-9_]/gi, "") );
		},
		toggleTranslation: function() {
			if (JCck.Dev.doTranslation == "0") {
				if($("#storage").val() == "dev") {
					$("#bool8").show();
				} else {
					$("#bool8").hide();
				}
			}
		},
		submit: function(task) {
			<?php if ( !$tmpl ) { ?>
			Joomla.submitbutton(task);
			<?php } else { ?>
			if ($("#adminForm").validationEngine("validate",task) === true) {
				JCck.Dev.ajaxTask(task);
			}
			<?php } ?>
		}
	}
	<?php if ( !$tmpl ) { ?>
	Joomla.submitbutton = function(task) {
		if (task == 'field.cancel') {
			JCck.submitForm(task, document.getElementById('adminForm'));
		} else {
			if ($("#adminForm").validationEngine("validate",task) === true) {
				JCck.submitForm(task, document.getElementById('adminForm'));
			}
		}
	}
	<?php } ?>
	$(document).ready(function() {
		$("#type").on('change', function() {
			var cur = $("#myid").val();
			var data = "id="+cur+"&ajax_type="+$("#type").val();
			JCck.Dev.ajaxLayer("field", "edit2", "#layer", data);
		});
		$(".submit_ajax").on("click", function() {
			var task = $(this).attr("data-task");
			task = "field."+task;
			JCck.Dev.submit(task);
		});
		$("#cancel_ajax").on("click", function() {
			JCck.Dev.ajaxTask("field.cancel");
			parent.jQuery.colorbox.close();
		});
		var insidebox = '<?php echo $this->insidebox; ?>';
		if (insidebox) { $("#title").after(insidebox); }
		if ($("#jform_id").val()==0){
			if (parent.jQuery("#folder")){
				$("#folder").val(parent.jQuery("#folder").val());
			}
		}
	});
})(jQuery);
</script>