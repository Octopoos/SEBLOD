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

$elem	=	JText::_( 'COM_CCK_'._C4_TEXT );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );

$options		=	array();
$options[]		=	JHtml::_( 'select.option', 0, '- '.JText::_( 'COM_CCK_NONE' ).' -', 'value', 'text' );
$options2		=	JCckDatabase::loadObjectList( 'SELECT a.title AS text, a.name AS value FROM #__cck_core_types AS a WHERE a.published = 1 ORDER BY a.title' );
if ( count( $options2 ) ) {
	$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_CONTENT_TYPES' ) );
	$options	=	array_merge( $options, $options2 );
	$options[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
}
$template			=	Helper_Admin::getDefaultTemplate();
$lists['featured']	=	JHtml::_( 'select.genericlist', $options, 'featured', 'class="inputbox" size="1"', 'value', 'text', '', 'featured' );

$doc	=	JFactory::getDocument();
$js		=	'
			(function ($){
				JCck.Dev = {
					submit: function() {
						var content_type = $("#featured").val();
						var tpl_s = $("#tpl_search").val();
						var tpl_f = $("#tpl_filter").val();
						var tpl_l = "";
						var tpl_i = $("#tpl_item").val();
						var url = "index.php?option=com_cck&task=search.add&content_type="+content_type+"&tpl_s="+tpl_s+"&tpl_f="+tpl_f+"&tpl_l="+tpl_l+"&tpl_i="+tpl_i;
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
		<?php echo JText::sprintf( 'COM_CCK_SEARCH_SPLASH_DESC', $elem ); ?>
	</div>
    <div style="text-align: center; margin-top: 30px;">
        <ul class="adminformlist">
        	<li><label><?php echo JText::_( 'COM_CCK_'._C2_TEXT ); ?></label><?php echo $lists['featured']; ?></li>
        	<li><label></label><button type="button" class="inputbutton" onclick="JCck.Dev.submit();"><?php echo JText::_( 'COM_CCK_CREATE' ) .' '. $elem; ?></button></li>
        </ul>
    </div>
</div>
<div class="clr"></div>

<input type="hidden" id="tpl_search" name="tpl_search" value="<?php echo $template; ?>" />
<input type="hidden" id="tpl_filter" name="tpl_filter" value="<?php echo $template; ?>" />
<input type="hidden" id="tpl_list" name="tpl_list" value="" />
<input type="hidden" id="tpl_item" name="tpl_item" value="<?php echo $template; ?>" />
<input type="hidden" id="task" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>

<?php
Helper_Display::quickCopyright();
?>