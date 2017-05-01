<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: select.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\String\StringHelper;

// Init
$app		=	JFactory::getApplication();
$base		=	'index.php?option=com_cck&view=form';
$layer		=	$app->input->getCmd( 'quicklayout', 'icon' );
$lang		=	JFactory::getLanguage();
$market		=	'https://www.seblod.com/download.html?tmpl=component';
$opts		=	'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=685,height=600';
$quickadd	=	$app->input->getInt( 'quickadd', 0 );
$type		=	'';
$variables	=	base64_decode( $app->input->getBase64( 'variables' ) );
$return		=	base64_decode( $app->input->getBase64( 'return' ) );
$user		=	JFactory::getUser();

if ( $quickadd ) {
	$legacy	=	0;
	$url	=	'none';
	$items	=	( $layer == 'list' ) ? JCckDevIntegration::getForms( $url, $type, 'folder' ) : JCckDevIntegration::getForms( $url, $type );
} else {
	$legacy	=	2;
	$url	=	JCckDevHelper::getUrlVars( $return );
	$layer	=	JCck::getConfig_Param( 'integration_layout', 'icon' );
	$items	=	( $layer == 'list' ) ? JCckDevIntegration::getForms( $url, $type, 'folder' ) : JCckDevIntegration::getForms( $url, $type );
	if ( strpos( $type, ',' ) === false ) {
		$options	=	JCckDatabase::loadResult( 'SELECT a.options FROM #__cck_core_objects AS a WHERE a.name = "'.$type.'"' );
		$options	=	new JRegistry( $options );
		$legacy		=	$options->get( 'add_alt', 2 );
	}
}

if ( $legacy == 1 ) {
	$legend		=	JText::sprintf( 'LIB_CCK_INTEGRATION_MODAL_BOX_LEGEND',
									'<a id="standard-content" href="javascript:void(0);" style="color:#808080">'.StringHelper::strtolower( JText::_( 'LIB_CCK_INTEGRATION_CLICK_HERE' ) ).'</a>',
									'<span style="color:#808080">'.StringHelper::strtolower( JText::_( 'LIB_CCK_INTEGRATION_SELECT_A_FORM' ) ).'</span>' );
	$legend2	=	'';
} elseif ( $legacy == 2 ) {
	$legend		=	JText::_( 'LIB_CCK_INTEGRATION_SELECT_A_FORM' );
	$legend2	=	JText::sprintf( 'LIB_CCK_INTEGRATION_MODAL_BOX_LEGEND2',
									'<a id="standard-content" href="javascript:void(0);" style="color:#808080">'.StringHelper::strtolower( JText::_( 'LIB_CCK_INTEGRATION_CLICK_HERE' ) ).'</a>' );
} else {
	$legend		=	JText::_( 'LIB_CCK_INTEGRATION_SELECT_A_FORM' );
	$legend2	=	'';
}

// Set
JCck::loadjQuery();
Helper_Include::addStyleSheets( true );

JHtml::_( 'stylesheet', 'administrator/components/com_cck/assets/css/cpanel.css', array(), false );

$doc	=	JFactory::getDocument();
$js		=	'jQuery(document).ready(function($){ $("#standard-content").attr("onclick","parent."+parent.jQuery("#toolbar-new a").attr("onclick2")); });';
$doc->addScriptDeclaration( $js );
?>

<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" id="adminForm" name="adminForm">

<div class="seblod">
    <?php
	if ( $legend ) {
		echo '<div class="legend top left">'.$legend.'</div>';
	}
	if ( count( $items ) ) {
		$layer	=	__DIR__.'/select_'.$layer.'.php';
		if ( is_file( $layer ) ) {
			include_once $layer;
		}
	}
	if ( $legend2 ) {
		echo '<div class="clr"></div><br /><div class="legend bottom left">'.$legend2.'</div>';
	}
    ?>
	<div class="clr"></div>
    <div class="get-more-online">
		<a href="<?php echo $market; ?>" onclick="window.open(this.href, 'targetWindow', '<?php echo $opts; ?>'); return false;">
			<?php echo JText::_( 'LIB_CCK_INTEGRATION_GET_MORE_APPS' ); ?>
		</a>
	</div>
</div>
<div class="clr"></div>

<input type="hidden" id="task" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>

<?php
Helper_Display::quickCopyright();
?>