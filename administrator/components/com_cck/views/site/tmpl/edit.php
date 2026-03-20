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

$config	=	JCckDev::init( array( '42', 'checkbox', 'field_x', 'jform_menuitem', 'password', 'radio', 'select_dynamic', 'select_numeric', 'select_simple', 'text', 'textarea', 'wysiwyg_editor' ),
						   true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
$cck	=	JCckDev::preload( array( 'core_title_site', 'core_name_site', 'core_description', 'core_state', 'core_site_name', 'core_site_pagetitles',
									 'core_site_metadesc', 'core_site_metakeys', 'core_site_homepage', 'core_site_offline', 'core_site_language', 'core_site_template_style',
									 'core_guest', 'core_guest_only_group', 'core_guest_only_viewlevel', 'core_public_viewlevel', 'core_groups', 'core_viewlevels', 'core_parent_site' ) );
$cfg        =   JCckDev::fromJSON( $this->item->configuration, 'array' );
$hasOpts    =   false;
if ( ( $pos = strpos( $this->item->name, '@' ) ) !== false ) {
    $this->item->name   =   substr( $this->item->name, 0, $pos );
}
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
    <div class="<?php echo $this->css['wrapper_first']; ?>">  
        <?php
        $dataTmpl   =   array(
                            'fields'=>array(
                                'description'=>JCckDev::getForm( $cck['core_description'], $this->item->description, $config, array( 'label'=>'clear', 'selectlabel'=>'Description' ) ),
                                'name'=>JCckDev::renderLayoutFile(
                                                'cck'.JCck::v().'.form.field', array(
                                                    'label'=>JCckDev::getLabel( $cck['core_name_site'], $config ),
                                                    'html'=>JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.grid', array(
                                                        'grid'=>'|auto|25%',
                                                        'html'=>array(
                                                            JCckDev::getForm( $cck['core_name_site'], $this->item->name, $config, array() ),
                                                            '<span class="variation_value">/</span>',
                                                            JCckDev::getForm( 'core_dev_text', $this->item->context, $config, array( 'storage_field'=>'context' ) )
                                                        )
                                                    ) )
                                                )
                                            ),
                                'parent'=>JCckDev::renderForm( $cck['core_parent_site'], $this->item->parent_id, $config ),
                                'site_aliases'=>JCckDev::renderForm( 'core_options', JCckDev::fromSTRING( $this->item->aliases ), $config, array( 'label'=>'Site Aliases', 'rows'=>'1', 'storage_field'=>'aliases' ) ),
                                'site_exclusions'=>JCckDev::renderForm( 'core_options', JCckDev::fromSTRING( @$cfg['exclusions'] ), $config, array( 'label'=>'Site Exclusions', 'rows'=>'1', 'storage_field'=>'exclusions', 'name'=>'core_options_url' ), array( 'after'=>'<div style="float:left;">'.JText::_( 'COM_CCK_SITE_EXCLUSIONS_DESC' ).'</div>' ) ),
                                'site_homepage'=>JCckDev::renderForm( $cck['core_site_homepage'], @$cfg['homepage'], $config ),
                                'site_language'=>JCckDev::renderForm( $cck['core_site_language'], @$cfg['language'], $config ),
                                'site_metadesc'=>JCckDev::renderForm( $cck['core_site_metadesc'], @$cfg['metadesc'], $config ),
                                'site_metakeys'=>JCckDev::renderForm( $cck['core_site_metakeys'], @$cfg['metakeys'], $config ),
                                'site_name'=>JCckDev::renderForm( $cck['core_site_name'], @$cfg['sitename'], $config ),
                                'site_offline'=>JCckDev::renderForm( $cck['core_site_offline'], @$cfg['offline'], $config ),
                                'site_pagetitles'=>JCckDev::renderForm( $cck['core_site_pagetitles'], @$cfg['sitename_pagetitles'], $config ),
                                'site_pagetitle'=>JCckDev::renderForm( 'core_dev_text', @$cfg['pagetitle'], $config, array( 'label'=>'Page Title', 'maxlength'=>70, 'storage_field'=>'json[configuration][pagetitle]' ) ),
                                'site_template_style'=>JCckDev::renderForm( $cck['core_site_template_style'], @$cfg['template_style'], $config ),
                                'state'=>JCckDev::renderForm( $cck['core_state'], $this->item->published, $config, array( 'label'=>( JCck::on( '4.0' ) ? 'Status' : 'clear' ) ) ),
                                'title'=>JCckDev::renderForm( $cck['core_title_site'], $this->item->title, $config )
                            ),
                            'item'=>$this->item,
                            'params'=>array()
                        );

        echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.site.edit_main', $dataTmpl );
        ?>
    </div>
    
    <div class="main-card">
        <?php
        ob_start();
        include __DIR__.'/edit_fields.php';
        $dataTmpl['fields']['custom_fields']    =   ob_get_clean();

        if ( JCck::on( '4.0' ) ) {
            echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768] );
            echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'details', JText::_( 'COM_CCK_DETAILS' ) );
            echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.site.edit_details', $dataTmpl );
            echo HTMLHelper::_( 'uitab.endTab' );
            if ( !$this->isNew ) {
                echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'publishing', JText::_( 'COM_CCK_CONFIGURATION_LABEL' ) );
                ?>
                <fieldset class="options-form">
                <p class="legend-desc"><?php echo JText::_( 'COM_CCK_SITE_SETTINGS_DESC' ); ?></p>
                <?php
                echo JCckDev::renderForm( $cck['core_guest'], $this->item->guest, $config );
                if ( strpos( $this->item->viewlevels, ',' ) !== false ) {
                    if ( $this->item->guest_only_group != '' ) {
                        $this->item->usergroups     .=  ','.$this->item->guest_only_group;
                    }
                    if ( $this->item->guest_only_viewlevel != '' ) {
                        $this->item->viewlevels .=  ','.$this->item->guest_only_viewlevel;
                    }
                    echo JCckDev::renderForm( $cck['core_guest_only_viewlevel'], $this->item->guest_only_viewlevel, $config );
                    echo JCckDev::renderForm( $cck['core_guest_only_group'], $this->item->guest_only_group, $config );
                }
                echo JCckDev::renderForm( $cck['core_public_viewlevel'], $this->item->public_viewlevel, $config );
                echo JCckDev::renderForm( $cck['core_groups'], $this->item->usergroups, $config, array( 'storage_field'=>'usergroups' ) );
                echo JCckDev::renderForm( $cck['core_viewlevels'], $this->item->viewlevels, $config );
                ?>
                </fieldset>
                <?php
                echo HTMLHelper::_( 'uitab.endTab' );
            }
            echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'url', JText::_( 'COM_CCK_OPTIONS' ) );
            echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.site.edit_options', $dataTmpl );
            echo HTMLHelper::_( 'uitab.endTab' );
            echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'publishing', JText::_( 'COM_CCK_PUBLISHING' ) );
            echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.site.edit_publishing', $dataTmpl );
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
    };
    Joomla.submitbutton = function(task) {
        if (task == "site.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
            JCck.submitForm(task, document.getElementById('adminForm'));
        }
    };
    $(document).ready(function() {
        $("span.value-picker").on("click", function() {
            var field = $(this).attr("name");
            var cur = "none";
            var url = "index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/selection.php&title=dev&name="+field+"&type=json_options_"+field+"&id="+cur;
            $.colorbox({href:url, iframe:true, innerWidth:300, innerHeight:200, scrolling:false, overlayClose:false, fixed:true, onLoad: function(){ $('#cboxClose').remove();}});
        });
    });
})(jQuery);
</script>