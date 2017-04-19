<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: new.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$elem	=	JText::_( 'COM_CCK_'._C2_TEXT );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );

$options			=	array();
$options[]			=	JHtml::_( 'select.option', 0, '- '.JText::_( 'COM_CCK_NONE' ).' -', 'value', 'text' );
$options2			=	Helper_Admin::getFolderOptions( false, false, true, true, '', true );
if ( count( $options2 ) ) {
	$options		=	array_merge( $options, $options2 );
}
$template			=	Helper_Admin::getDefaultTemplate();
$lists['featured']	=	JHtml::_( 'select.genericlist', $options, 'featured', 'class="inputbox" size="1"', 'value', 'text', 10, 'featured' );

$doc	=	JFactory::getDocument();
$js		=	'
			(function ($){
				JCck.Dev = {
					submit: function() {
						var skeleton_id = $("#featured").val();
						var tpl_a = $("#tpl_admin").val();
						var tpl_s = $("#tpl_site").val();
						var tpl_c = $("#tpl_content").val();
						var tpl_i = $("#tpl_intro").val();
						var url = "index.php?option=com_cck&task=type.add&skeleton_id="+skeleton_id+"&tpl_a="+tpl_a+"&tpl_s="+tpl_s+"&tpl_c="+tpl_c+"&tpl_i="+tpl_i;
						top.location.href = url;
						return false;
					}
    			}
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );
?>

<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" id="adminForm" name="adminForm">

<div class="seblod">
	<div class="legend top center" style="font-size: 42px; font-style:italic;">
		<?php echo JText::_( 'JTOOLBAR_NEW' ) .' '. $elem; ?>
	</div>
	<div class="legend top center" style="margin-top: 10px; font-style:italic;">
		<?php echo JText::sprintf( 'COM_CCK_TYPE_SPLASH_DESC', $elem ); ?>
	</div>
    <div style="text-align: center; margin-top: 30px;">
        <ul class="adminformlist">
        	<li><label><?php echo JText::_( 'COM_CCK_SKELETON' ); ?></label><?php echo $lists['featured']; ?></li>
        	<li><label></label><button type="button" class="inputbutton" onclick="JCck.Dev.submit();"><?php echo JText::_( 'COM_CCK_CREATE' ) .' '. $elem; ?></button></li>
        </ul>
    </div>
</div>
<div class="clr"></div>

<input type="hidden" id="tpl_admin" name="tpl_admin" value="<?php echo $template; ?>" />
<input type="hidden" id="tpl_site" name="tpl_site" value="<?php echo $template; ?>" />
<input type="hidden" id="tpl_content" name="tpl_content" value="<?php echo $template; ?>" />
<input type="hidden" id="tpl_intro" name="tpl_intro" value="<?php echo $template; ?>" />
<input type="hidden" id="task" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>

<?php
Helper_Display::quickCopyright();
?>