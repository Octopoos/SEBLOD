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

$config	=	JCckDev::init( array( '42', 'checkbox', 'field_x', 'jform_menuitem', 'password', 'radio', 'select_dynamic', 'select_numeric', 'select_simple', 'text', 'textarea', 'wysiwyg_editor' ),
						   true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
$cck	=	JCckDev::preload( array( 'core_title_site', 'core_name_site', 'core_description', 'core_state', 'core_site_name', 'core_site_pagetitles',
									 'core_site_metadesc', 'core_site_metakeys', 'core_site_homepage', 'core_site_offline', 'core_site_language', 'core_site_template_style',
									 'core_guest', 'core_guest_only_group', 'core_guest_only_viewlevel', 'core_public_viewlevel', 'core_groups', 'core_viewlevels' ) );
$hasOpts  =   false;
if ( ( $pos = strpos( $this->item->name, '@' ) ) !== false ) {
    $this->item->name   =   substr( $this->item->name, 0, $pos );
}
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod first">       
        <ul class="spe spe_title">
            <?php echo JCckDev::renderForm( $cck['core_title_site'], $this->item->title, $config ); ?>
        </ul>
        <ul class="spe spe_folder">
            <li class="tweak-site"><label><?php echo JText::_( 'COM_CCK_URL' ); ?><span class="star"> *</span></label>
            <?php
            echo JCckDev::getForm( $cck['core_name_site'], $this->item->name, $config, array(), array('after'=>'<span class="variation_value">/</span>' ) );
            echo JCckDev::getForm( 'core_dev_text', $this->item->context, $config, array( 'css'=>'input-xxsmall', 'storage_field'=>'context' ) );
            ?>
            </li>
        </ul>
        <ul class="spe spe_state spe_third">
            <?php echo JCckDev::renderForm( $cck['core_state'], $this->item->published, $config, array( 'label'=>'clear' ) ); ?>
        </ul>
        <ul class="spe spe_description">
            <?php echo JCckDev::renderForm( $cck['core_description'], $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ); ?>
        </ul>
	</div>
    
	<div class="seblod">
        <div class="legend top left"><?php echo JText::_( 'COM_CCK_OPTIONS' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
			<?php
            $cfg        =	JCckDev::fromJSON( $this->item->configuration, 'array' );
            echo JCckDev::renderForm( $cck['core_site_name'], @$cfg['sitename'], $config );
            echo JCckDev::renderForm( $cck['core_site_pagetitles'], @$cfg['sitename_pagetitles'], $config );
            echo JCckDev::renderForm( $cck['core_site_metadesc'], @$cfg['metadesc'], $config );
            echo JCckDev::renderForm( $cck['core_site_metakeys'], @$cfg['metakeys'], $config );
            echo JCckDev::renderForm( $cck['core_site_homepage'], @$cfg['homepage'], $config );
            echo JCckDev::renderForm( $cck['core_site_language'], @$cfg['language'], $config );
            echo JCckDev::renderForm( $cck['core_site_offline'], @$cfg['offline'], $config );
            echo JCckDev::renderForm( $cck['core_site_template_style'], @$cfg['template_style'], $config );
            ?>
        </ul>
		<?php if ( !$this->isNew ) { ?>
            <a id="toggle_acl" href="javascript:void(0);" class="btn btn-small" style="float:right;"><span class="icon-users"></span></a>
		<?php } ?>
	</div>

    <div class="seblod">
        <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_URLS' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
            $aliases    =   JCckDev::fromSTRING( $this->item->aliases );
            $exclusions =   JCckDev::fromSTRING( @$cfg['exclusions'] );
            echo JCckDev::renderForm( 'core_options', $aliases, $config, array( 'label'=>'Site Aliases', 'rows'=>'1', 'storage_field'=>'aliases' ) );
            echo JCckDev::renderForm( 'core_options', $exclusions, $config, array( 'label'=>'Site Exclusions', 'rows'=>'1', 'storage_field'=>'exclusions', 'name'=>'core_options_url' ), array( 'after'=>'<div style="float:left;">'.JText::_( 'COM_CCK_SITE_EXCLUSIONS_DESC' ).'</div>' ) );
            ?>
        </ul>
    </div>

    <div class="seblod">
        <div class="legend top left"><?php echo '&rArr; ' . JText::_( 'COM_CCK_SITE_OPTIONS' ); ?></div>
        <?php if ( count( $this->item->fields ) ) { ?>
        <ul class="adminformlist adminformlist-2cols">
            <?php
            $fieldnames =   array();
            foreach ( $this->item->fields as $fieldname ) {
                if ( empty( $fieldname ) ) {
                    continue;
                }
                $field  =   JCckDev::get( $fieldname, '', $config );
                if ( !is_object( $field ) ) {
                    continue;
                }
                if ( isset( $fieldnames[$field->name] ) ) {
                    continue;
                }

                $id     =   str_replace( array( 'json[options][', ']' ), '', $field->storage_field );
                $value  =   ( isset( $this->item->options[$id] ) ) ? $this->item->options[$id] : '';
                $class  =   'inputbox text'. ( $field->css ? ' '.$field->css : '' );
                $maxlen =   ( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
                $attr   =   'class="'.$class.'" size="'.$field->size.'"'.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
                $picker =   '';
                $type   =   ( $field->type == 'password' ) ? $field->type : 'text';
                if ( JCck::callFunc( 'plgCCK_Field'.$field->type, 'isFriendly' ) ) {
                    $picker =   '<span id="storage_field_pick_'.$field->name.'" name="'.$field->name.'" class="value-picker">&laquo;</span>';
                }
                $fieldnames[$field->name]   =   '';
                $hasOpts                    =   true;
                echo '<li><label>'.$field->label.'</label>'
                 .   '<input type="'.$type.'" id="json_options_'.$field->name.'" name="'.$field->storage_field.'" value="'.$value.'" '.$attr.'>'
                 .   $picker
                 .   '</li>';
            }
            ?>
        </ul>
        <?php }
        if ( !$hasOpts ) { ?>
            <p class="legend-desc"><?php echo JText::_( 'COM_CCK_SITE_OPTIONS_DESC' ); ?></p>
        <?php } ?>
    </div>

    <?php if ( !$this->isNew ) { ?>
		<div class="seblod" id="acl" style="display: none;">
            <div class="legend top left"><?php echo JText::_( 'COM_CCK_SETTINGS' ); ?></div>
            <p class="legend-desc"><?php echo JText::_( 'COM_CCK_SITE_SETTINGS_DESC' ); ?></p>
            <ul class="adminformlist adminformlist-2cols">
                <?php
                echo JCckDev::renderForm( $cck['core_guest'], $this->item->guest, $config );
				if ( strpos( $this->item->viewlevels, ',' ) !== false ) {
                    if ( $this->item->guest_only_group != '' ) {
                        $this->item->groups     .=  ','.$this->item->guest_only_group;
                    }
                    if ( $this->item->guest_only_viewlevel != '' ) {
                        $this->item->viewlevels .=  ','.$this->item->guest_only_viewlevel;
                    }
					echo JCckDev::renderForm( $cck['core_guest_only_viewlevel'], $this->item->guest_only_viewlevel, $config );
                    echo JCckDev::renderForm( $cck['core_guest_only_group'], $this->item->guest_only_group, $config );
				}
                echo JCckDev::renderForm( $cck['core_public_viewlevel'], $this->item->public_viewlevel, $config );
                echo JCckDev::renderForm( $cck['core_groups'], $this->item->groups, $config );
                echo JCckDev::renderForm( $cck['core_viewlevels'], $this->item->viewlevels, $config );
                ?>
            </ul>
        </div>
	<?php } ?>
</div>

<div class="clr"></div>
<div>
    <input type="hidden" id="task" name="task" value="" />
    <input type="hidden" id="myid" name="id" value="<?php echo @$this->item->id; ?>" />
    <input type="hidden" id="type" name="type" value="<?php echo $this->item->type; ?>" />
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
        if (task == "site.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
            JCck.submitForm(task, document.getElementById('adminForm'));
        }
    }
    $(document).ready(function() {
        $('select.inputbox').css('max-width','200px');
        $("#toggle_acl").click(function(){
            $("#acl").slideToggle();
        });
        $("span.value-picker").on("click", function() {
            var field = $(this).attr("name");
            var cur = "none";
            var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/selection.php&title=dev&name="+field+"&type=json_options_"+field+"&id="+cur;
            $.colorbox({href:url, iframe:true, innerWidth:300, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
        });
    });
})(jQuery);
</script>