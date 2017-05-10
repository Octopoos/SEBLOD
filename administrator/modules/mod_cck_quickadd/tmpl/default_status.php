<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_status.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$class	=	'';
$href	=	'#';
if ( !$app->input->getBool( 'hidemainmenu' ) ) {
	$layout	=	JPATH_ADMINISTRATOR.'/components/com_cck/views/form/tmpl/modal_'.$modal_layout.'.php';
	JCckDevIntegration::appendModal( $layout, 'collapseModal3', '.cck-quickadd > button', array( 'quickadd'=>1 ) );
	$class	=	' btn-success';
}
?>
<div class="btn-group cck-quickadd">
	<button href="<?php echo $href; ?>" class="btn btn-small<?php echo $class; ?>" data-toggle="modal" data-target="#collapseModal3">
		<span class="icon-plus"></span>
		<?php echo $label; ?>
	</button>
</div>
<div class="btn-group">
	<span class="btn-group separator"></span>
</div>